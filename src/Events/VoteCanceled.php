<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Events;

use Illuminate\Database\Eloquent\Model;

class VoteCanceled
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $vote;

    public function __construct(Model $vote)
    {
        $this->vote = $vote;
    }
}
