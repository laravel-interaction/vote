<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Vote\Tests\Models\Channel;
use LaravelInteraction\Vote\Tests\Models\User;
use LaravelInteraction\Vote\Vote;

/**
 * @internal
 */
final class VoteTest extends TestCase
{
    private User $user;

    private Channel $channel;

    private Vote $vote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->vote($this->channel);
        $this->vote = Vote::query()->firstOrFail();
    }

    public function testVoteTimestamp(): void
    {
        $this->assertInstanceOf(Carbon::class, $this->vote->created_at);
        $this->assertInstanceOf(Carbon::class, $this->vote->updated_at);
    }

    public function testScopeWithType(): void
    {
        $this->assertSame(1, Vote::query()->withType(Channel::class)->count());
        $this->assertSame(0, Vote::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        $this->assertSame(config('vote.table_names.pivot'), $this->vote->getTable());
    }

    public function testVoter(): void
    {
        $this->assertInstanceOf(User::class, $this->vote->voter);
    }

    public function testVoteable(): void
    {
        $this->assertInstanceOf(Channel::class, $this->vote->voteable);
    }

    public function testUser(): void
    {
        $this->assertInstanceOf(User::class, $this->vote->user);
    }

    public function testIsVotedTo(): void
    {
        $this->assertTrue($this->vote->isVotedTo($this->channel));
        $this->assertFalse($this->vote->isVotedTo($this->user));
    }

    public function testIsVotedBy(): void
    {
        $this->assertFalse($this->vote->isVotedBy($this->channel));
        $this->assertTrue($this->vote->isVotedBy($this->user));
    }

    public function testIsUpvote(): void
    {
        $this->assertTrue($this->vote->isUpvote());
    }

    public function testIsDownvote(): void
    {
        $this->assertFalse($this->vote->isDownvote());
    }
}
