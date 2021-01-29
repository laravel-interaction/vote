<?php

declare(strict_types=1);

namespace Zing\LaravelVote;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Zing\LaravelVote\Events\VoteCanceled;
use Zing\LaravelVote\Events\Voted;

/**
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $voter
 * @property \Illuminate\Database\Eloquent\Model $voteable
 * @property bool $upvote
 *
 * @method static \Zing\LaravelVote\Vote|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \Zing\LaravelVote\Vote|\Illuminate\Database\Eloquent\Builder query()
 */
class Vote extends MorphPivot
{
    public $incrementing = true;

    protected $dispatchesEvents = [
        'saved' => Voted::class,
        'deleted' => VoteCanceled::class,
    ];

    protected $casts = [
        'upvote' => 'bool',
    ];

    public function getTable()
    {
        return config('vote.table_names.votes') ?: parent::getTable();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function voteable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('vote.models.user'), config('vote.column_names.user_foreign_key'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voter(): BelongsTo
    {
        return $this->user();
    }

    public function isVotedBy(Model $user): bool
    {
        return $user->is($this->voter);
    }

    public function isVotedTo(Model $object): bool
    {
        return $object->is($this->voteable);
    }

    public function isUpvote(): bool
    {
        return $this->upvote;
    }

    public function isDownvote(): bool
    {
        return ! $this->isUpvote();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('voteable_type', app($type)->getMorphClass());
    }
}
