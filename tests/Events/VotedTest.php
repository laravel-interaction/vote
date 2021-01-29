<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelVote\Events\Voted;
use Zing\LaravelVote\Tests\Models\Channel;
use Zing\LaravelVote\Tests\Models\User;
use Zing\LaravelVote\Tests\TestCase;

class VotedTest extends TestCase
{
    public function testVote(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->vote($channel);
        Event::assertDispatchedTimes(Voted::class);
    }

    public function testVoteTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->vote($channel);
        $user->vote($channel);
        $user->vote($channel);
        Event::assertDispatchedTimes(Voted::class);
    }

    public function testDownVoteAfterUpvote(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->vote($channel);
        $user->downvote($channel);
        Event::assertDispatchedTimes(Voted::class, 2);
    }
}
