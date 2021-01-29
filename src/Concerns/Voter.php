<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelVote\Vote[] $votes
 * @property-read int|null $votes_count
 */
trait Voter
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     * @param bool $upvote
     */
    public function vote(Model $object, $upvote = true): void
    {
        // compatible with laravel 6
        $vote = ($this->relationLoaded('votes') ? $this->votes : $this->votes())
            ->where('voteable_id', $object->getKey())
            ->where('voteable_type', $object->getMorphClass())
            ->first();
        if ($vote && $vote->upvote === $upvote) {
            return;
        }

        $this->votedItems(get_class($object))->syncWithoutDetaching(
            [
                $object->getKey() => [
                    'upvote' => $upvote,
                ],
            ]
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function upvote(Model $object): void
    {
        $this->vote($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function downvote(Model $object): void
    {
        $this->vote($object, false);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @throws \Exception
     */
    public function cancelVote(Model $object): void
    {
        $this->votedItems(get_class($object))->detach($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasVoted(Model $object): bool
    {
        return ($this->relationLoaded('votes') ? $this->votes : $this->votes())
            ->where('voteable_id', $object->getKey())
            ->where('voteable_type', $object->getMorphClass())
            ->count() > 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasUpvoted(Model $object): bool
    {
        return ($this->relationLoaded('votes') ? $this->votes : $this->votes())
            ->where('voteable_id', $object->getKey())
            ->where('voteable_type', $object->getMorphClass())
            ->where('upvote', true)
            ->count() > 0;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasDownvoted(Model $object): bool
    {
        return ($this->relationLoaded('votes') ? $this->votes : $this->votes())
            ->where('voteable_id', $object->getKey())
            ->where('voteable_type', $object->getMorphClass())
            ->where('upvote', false)
            ->count() > 0;
    }

    public function hasNotVoted(Model $object): bool
    {
        return ! $this->hasVoted($object);
    }

    public function hasNotUpvoted(Model $object): bool
    {
        return ! $this->hasUpvoted($object);
    }

    public function hasNotDownvoted(Model $object): bool
    {
        return ! $this->hasDownvoted($object);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(config('vote.models.vote'), config('vote.column_names.user_foreign_key'), $this->getKeyName());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function votedItems(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'voteable', config('vote.models.vote'), config('vote.column_names.user_foreign_key'), 'voteable_id')->withTimestamps()->withPivot('upvote');
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function upvotedItems(string $class): MorphToMany
    {
        return $this->votedItems($class)->wherePivot('upvote', true);
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function downvotedItems(string $class): MorphToMany
    {
        return $this->votedItems($class)->wherePivot('upvote', false);
    }
}
