<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Tests\Concerns;

use LaravelInteraction\Vote\Tests\Models\Channel;
use LaravelInteraction\Vote\Tests\Models\User;
use LaravelInteraction\Vote\Tests\TestCase;
use LaravelInteraction\Vote\Vote;

/**
 * @internal
 */
final class VoterTest extends TestCase
{
    public function testVote(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertDatabaseHas(
            Vote::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'voteable_type' => $channel->getMorphClass(),
                'voteable_id' => $channel->getKey(),
            ]
        );
        $user->load('voterVotes');
        $user->cancelVote($channel);
        $user->load('voterVotes');
        $user->vote($channel);
    }

    public function testUpvote(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->upvote($channel);
        $this->assertDatabaseHas(
            Vote::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'voteable_type' => $channel->getMorphClass(),
                'voteable_id' => $channel->getKey(),
                'votes' => 1,
            ]
        );
    }

    public function testDownvote(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        $this->assertDatabaseHas(
            Vote::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'voteable_type' => $channel->getMorphClass(),
                'voteable_id' => $channel->getKey(),
                'votes' => -1,
            ]
        );
    }

    public function testCancelVote(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertDatabaseHas(
            Vote::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'voteable_type' => $channel->getMorphClass(),
                'voteable_id' => $channel->getKey(),
            ]
        );
        $user->cancelVote($channel);
        $this->assertDatabaseMissing(
            Vote::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'voteable_type' => $channel->getMorphClass(),
                'voteable_id' => $channel->getKey(),
            ]
        );
    }

    public function testVotes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertSame(1, $user->voterVotes()->count());
        $this->assertSame(1, $user->voterVotes->count());
    }

    public function testHasVoted(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertTrue($user->hasVoted($channel));
        $user->cancelVote($channel);
        $user->load('voterVotes');
        $this->assertFalse($user->hasVoted($channel));
    }

    public function testHasUpvoted(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertTrue($user->hasUpvoted($channel));
        $user->cancelVote($channel);
        $user->load('voterVotes');
        $this->assertFalse($user->hasUpvoted($channel));
    }

    public function testHasDownvoted(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        $this->assertTrue($user->hasDownvoted($channel));
        $user->cancelVote($channel);
        $user->load('voterVotes');
        $this->assertFalse($user->hasDownvoted($channel));
    }

    public function testHasNotVoted(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertFalse($user->hasNotVoted($channel));
        $user->cancelVote($channel);
        $this->assertTrue($user->hasNotVoted($channel));
    }

    public function testHasNotUpvoted(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->upvote($channel);
        $this->assertFalse($user->hasNotUpvoted($channel));
        $user->cancelVote($channel);
        $this->assertTrue($user->hasNotUpvoted($channel));
    }

    public function testHasNotDownvoted(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        $this->assertFalse($user->hasNotDownvoted($channel));
        $user->cancelVote($channel);
        $this->assertTrue($user->hasNotDownvoted($channel));
    }

    public function testVotedItems(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertTrue($channel->is($user->votedChannels()->first()));
        $user->cancelVote($channel);
        $this->assertNull($user->votedChannels()->first());
    }

    public function testUpvotedItems(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->upvote($channel);
        $this->assertTrue($channel->is($user->upvotedChannels()->first()));
        $user->cancelVote($channel);
        $this->assertNull($user->upvotedChannels()->first());
    }

    public function testDownvotedItems(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        $this->assertTrue($channel->is($user->downvotedChannels()->first()));
        $user->cancelVote($channel);
        $this->assertNull($user->downvotedChannels()->first());
    }
}
