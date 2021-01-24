<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Tests;

use Illuminate\Support\Carbon;
use Zing\LaravelVote\Tests\Models\Channel;
use Zing\LaravelVote\Tests\Models\User;
use Zing\LaravelVote\Vote;

class VoteTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Zing\LaravelVote\Tests\Models\User
     */
    protected $user;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Zing\LaravelVote\Tests\Models\Channel
     */
    protected $channel;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|\Zing\LaravelVote\Vote|null
     */
    protected $vote;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->vote($this->channel);
        $this->vote = Vote::query()->first();
    }

    public function testVoteTimestamp(): void
    {
        self::assertInstanceOf(Carbon::class, $this->vote->created_at);
        self::assertInstanceOf(Carbon::class, $this->vote->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Vote::query()->withType(Channel::class)->count());
        self::assertSame(0, Vote::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('vote.table_names.votes'), $this->vote->getTable());
    }

    public function testVoter(): void
    {
        self::assertInstanceOf(User::class, $this->vote->voter);
    }

    public function testVoteable(): void
    {
        self::assertInstanceOf(Channel::class, $this->vote->voteable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->vote->user);
    }

    public function testIsVotedTo(): void
    {
        self::assertTrue($this->vote->isVotedTo($this->channel));
        self::assertFalse($this->vote->isVotedTo($this->user));
    }

    public function testIsVotedBy(): void
    {
        self::assertFalse($this->vote->isVotedBy($this->channel));
        self::assertTrue($this->vote->isVotedBy($this->user));
    }
}
