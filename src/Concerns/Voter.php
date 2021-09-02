<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelInteraction\Vote\Vote;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Vote\Vote[] $voterVotes
 * @property-read int|null $voter_votes_count
 */
trait Voter
{
    public function cancelVote(Model $object): bool
    {
        $hasNotVoted = $this->hasNotVoted($object);
        if ($hasNotVoted) {
            return true;
        }

        $voterVotesLoaded = $this->relationLoaded('voterVotes');
        if ($voterVotesLoaded) {
            $this->unsetRelation('voterVotes');
        }

        return (bool) $this->votedItems(get_class($object))
            ->detach($object->getKey());
    }

    public function downvote(Model $object, int $votes = 1): Vote
    {
        return $this->vote($object, -abs($votes));
    }

    public function hasDownvoted(Model $object): bool
    {
        return ($this->relationLoaded('voterVotes') ? $this->voterVotes : $this->voterVotes())
            ->where('voteable_id', $object->getKey())
            ->where('voteable_type', $object->getMorphClass())
            ->where('votes', '<', 0)
            ->count() > 0;
    }

    public function hasNotDownvoted(Model $object): bool
    {
        return ! $this->hasDownvoted($object);
    }

    public function hasNotUpvoted(Model $object): bool
    {
        return ! $this->hasUpvoted($object);
    }

    public function hasNotVoted(Model $object): bool
    {
        return ! $this->hasVoted($object);
    }

    public function hasUpvoted(Model $object): bool
    {
        return ($this->relationLoaded('voterVotes') ? $this->voterVotes : $this->voterVotes())
            ->where('voteable_id', $object->getKey())
            ->where('voteable_type', $object->getMorphClass())
            ->where('votes', '>', 0)
            ->count() > 0;
    }

    public function hasVoted(Model $object): bool
    {
        return ($this->relationLoaded('voterVotes') ? $this->voterVotes : $this->voterVotes())
            ->where('voteable_id', $object->getKey())
            ->where('voteable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function upvote(Model $object, int $votes = 1): Vote
    {
        return $this->vote($object, abs($votes));
    }

    public function vote(Model $object, int $votes = 1): Vote
    {
        $attributes = [
            'voteable_id' => $object->getKey(),
            'voteable_type' => $object->getMorphClass(),
        ];
        $values = [
            'votes' => $votes,
        ];
        $vote = $this->voterVotes()
            ->where($attributes)
            ->firstOrNew($attributes, $values);
        $vote->fill($values);
        if ($vote->isDirty() || ! $vote->exists) {
            $voterVotesLoaded = $this->relationLoaded('voterVotes');
            if ($voterVotesLoaded) {
                $this->unsetRelation('voterVotes');
            }

            $vote->save();
        }

        return $vote;
    }

    public function voterVotes(): HasMany
    {
        return $this->hasMany(
            config('vote.models.vote'),
            config('vote.column_names.user_foreign_key'),
            $this->getKeyName()
        );
    }

    protected function downvotedItems(string $class): MorphToMany
    {
        return $this->votedItems($class)
            ->wherePivot('votes', '<', 0);
    }

    protected function upvotedItems(string $class): MorphToMany
    {
        return $this->votedItems($class)
            ->wherePivot('votes', '>', 0);
    }

    protected function votedItems(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'voteable',
            config('vote.models.vote'),
            config('vote.column_names.user_foreign_key')
        )
            ->withTimestamps()
            ->withPivot('votes');
    }
}
