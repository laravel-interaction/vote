<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Tests\Events;

use Illuminate\Support\Facades\Event;
use Zing\LaravelVote\Events\VoteCanceled;
use Zing\LaravelVote\Events\Voted;
use Zing\LaravelVote\Tests\Models\Channel;
use Zing\LaravelVote\Tests\Models\User;
use Zing\LaravelVote\Tests\TestCase;

class VoteCanceledTest extends TestCase
{
    public function testCancelVote(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->vote($channel);
        $user->cancelVote($channel);
        Event::assertDispatchedTimes(VoteCanceled::class);
    }

    public function testCancelVoteTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake();
        $user->vote($channel);
        $user->cancelVote($channel);
        $user->cancelVote($channel);
        Event::assertDispatchedTimes(Voted::class);
    }
}
