<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class VoteServiceProvider extends InteractionServiceProvider
{
    /**
     * @var string
     */
    protected $interaction = InteractionList::VOTE;
}
