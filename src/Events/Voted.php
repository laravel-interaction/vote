<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Events;

use Illuminate\Database\Eloquent\Model;

class Voted
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $vote;

    /**
     * Voted constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $vote
     */
    public function __construct(Model $vote)
    {
        $this->vote = $vote;
    }
}
