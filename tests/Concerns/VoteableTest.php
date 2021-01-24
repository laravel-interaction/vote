<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Tests\Concerns;

use Mockery;
use Zing\LaravelVote\Tests\Models\Channel;
use Zing\LaravelVote\Tests\Models\User;
use Zing\LaravelVote\Tests\TestCase;

class VoteableTest extends TestCase
{
    public function testVotes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        self::assertSame(1, $channel->votes()->count());
        self::assertSame(1, $channel->votes->count());
    }

    public function testVotersCount(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        self::assertSame(1, $channel->votersCount());
        $user->cancelVote($channel);
        self::assertSame(1, $channel->votersCount());
        $channel->loadCount('voters');
        self::assertSame(0, $channel->votersCount());
    }

    public function testUpvotersCount(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->upvote($channel);
        self::assertSame(1, $channel->upvotersCount());
        $user->cancelVote($channel);
        self::assertSame(1, $channel->upvotersCount());
        $channel->loadCount('upvoters');
        self::assertSame(0, $channel->upvotersCount());
    }

    public function testDownvotersCount(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        self::assertSame(1, $channel->downvotersCount());
        $user->cancelVote($channel);
        self::assertSame(1, $channel->downvotersCount());
        $channel->loadCount('downvoters');
        self::assertSame(0, $channel->downvotersCount());
    }

    public function data(): array
    {
        return [
            [0, '0', '0', '0'],
            [1, '1', '1', '1'],
            [12, '12', '12', '12'],
            [123, '123', '123', '123'],
            [12345, '12.3K', '12.35K', '12.34K'],
            [1234567, '1.2M', '1.23M', '1.23M'],
            [123456789, '123.5M', '123.46M', '123.46M'],
            [12345678901, '12.3B', '12.35B', '12.35B'],
            [1234567890123, '1.2T', '1.23T', '1.23T'],
            [1234567890123456, '1.2Qa', '1.23Qa', '1.23Qa'],
            [1234567890123456789, '1.2Qi', '1.23Qi', '1.23Qi'],
        ];
    }

    /**
     * @dataProvider data
     *
     * @param mixed $actual
     * @param mixed $onePrecision
     * @param mixed $twoPrecision
     * @param mixed $halfDown
     */
    public function testVotersCountForHumans($actual, $onePrecision, $twoPrecision, $halfDown): void
    {
        $channel = Mockery::mock(Channel::class);
        $channel->shouldReceive('votersCountForHumans')->passthru();
        $channel->shouldReceive('votersCount')->andReturn($actual);
        self::assertSame($onePrecision, $channel->votersCountForHumans());
        self::assertSame($twoPrecision, $channel->votersCountForHumans(2));
        self::assertSame($halfDown, $channel->votersCountForHumans(2, PHP_ROUND_HALF_DOWN));
    }

    /**
     * @dataProvider data
     *
     * @param mixed $actual
     * @param mixed $onePrecision
     * @param mixed $twoPrecision
     * @param mixed $halfDown
     */
    public function testUpvotersCountForHumans($actual, $onePrecision, $twoPrecision, $halfDown): void
    {
        $channel = Mockery::mock(Channel::class);
        $channel->shouldReceive('upvotersCountForHumans')->passthru();
        $channel->shouldReceive('upvotersCount')->andReturn($actual);
        self::assertSame($onePrecision, $channel->upvotersCountForHumans());
        self::assertSame($twoPrecision, $channel->upvotersCountForHumans(2));
        self::assertSame($halfDown, $channel->upvotersCountForHumans(2, PHP_ROUND_HALF_DOWN));
    }

    /**
     * @dataProvider data
     *
     * @param mixed $actual
     * @param mixed $onePrecision
     * @param mixed $twoPrecision
     * @param mixed $halfDown
     */
    public function testDownvotersCountForHumans($actual, $onePrecision, $twoPrecision, $halfDown): void
    {
        $channel = Mockery::mock(Channel::class);
        $channel->shouldReceive('downvotersCountForHumans')->passthru();
        $channel->shouldReceive('downvotersCount')->andReturn($actual);
        self::assertSame($onePrecision, $channel->downvotersCountForHumans());
        self::assertSame($twoPrecision, $channel->downvotersCountForHumans(2));
        self::assertSame($halfDown, $channel->downvotersCountForHumans(2, PHP_ROUND_HALF_DOWN));
    }

    public function testIsVotedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertFalse($channel->isVotedBy($channel));
        $user->vote($channel);
        self::assertTrue($channel->isVotedBy($user));
        $channel->load('voters');
        $user->cancelVote($channel);
        self::assertTrue($channel->isVotedBy($user));
        $channel->load('voters');
        self::assertFalse($channel->isVotedBy($user));
    }

    public function testIsNotVotedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertTrue($channel->isNotVotedBy($channel));
        $user->vote($channel);
        self::assertFalse($channel->isNotVotedBy($user));
        $channel->load('voters');
        $user->cancelVote($channel);
        self::assertFalse($channel->isNotVotedBy($user));
        $channel->load('voters');
        self::assertTrue($channel->isNotVotedBy($user));
    }

    public function testIsUpvotedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertFalse($channel->isVotedBy($channel));
        $user->upvote($channel);
        self::assertTrue($channel->isUpvotedBy($user));
        $channel->load('upvoters');
        $user->cancelVote($channel);
        self::assertTrue($channel->isUpvotedBy($user));
        $channel->load('upvoters');
        self::assertFalse($channel->isUpvotedBy($user));
    }

    public function testIsNotUpvotedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertTrue($channel->isNotUpvotedBy($channel));
        $user->vote($channel);
        self::assertFalse($channel->isNotUpvotedBy($user));
        $channel->load('upvoters');
        $user->cancelVote($channel);
        self::assertFalse($channel->isNotUpvotedBy($user));
        $channel->load('upvoters');
        self::assertTrue($channel->isNotUpvotedBy($user));
    }

    public function testIsDownvotedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertFalse($channel->isDownvotedBy($channel));
        $user->downvote($channel);
        self::assertTrue($channel->isDownvotedBy($user));
        $channel->load('downvoters');
        $user->cancelVote($channel);
        self::assertTrue($channel->isDownvotedBy($user));
        $channel->load('downvoters');
        self::assertFalse($channel->isDownvotedBy($user));
    }

    public function testIsNotDownvotedBy(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        self::assertTrue($channel->isNotDownvotedBy($channel));
        $user->downvote($channel);
        self::assertFalse($channel->isNotDownvotedBy($user));
        $channel->load('downvoters');
        $user->cancelVote($channel);
        self::assertFalse($channel->isNotDownvotedBy($user));
        $channel->load('downvoters');
        self::assertTrue($channel->isNotDownvotedBy($user));
    }

    public function testVoters(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        self::assertSame(1, $channel->voters()->count());
        $user->cancelVote($channel);
        self::assertSame(0, $channel->voters()->count());
    }

    public function testUpvoters(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->upvote($channel);
        self::assertSame(1, $channel->upvoters()->count());
        $user->cancelVote($channel);
        self::assertSame(0, $channel->upvoters()->count());
    }

    public function testDownvoters(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        self::assertSame(1, $channel->downvoters()->count());
        $user->cancelVote($channel);
        self::assertSame(0, $channel->downvoters()->count());
    }

    public function testScopeWhereVotedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        self::assertSame(1, Channel::query()->whereVotedBy($user)->count());
        self::assertSame(0, Channel::query()->whereVotedBy($other)->count());
    }

    public function testScopeWhereNotVotedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        self::assertSame(0, Channel::query()->whereNotVotedBy($user)->count());
        self::assertSame(1, Channel::query()->whereNotVotedBy($other)->count());
    }

    public function testScopeWhereUpvotedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->upvote($channel);
        self::assertSame(1, Channel::query()->whereUpvotedBy($user)->count());
        self::assertSame(0, Channel::query()->whereUpvotedBy($other)->count());
    }

    public function testScopeWhereNotUpvotedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->upvote($channel);
        self::assertSame(0, Channel::query()->whereNotUpvotedBy($user)->count());
        self::assertSame(1, Channel::query()->whereNotUpvotedBy($other)->count());
    }

    public function testScopeWhereDownvotedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        self::assertSame(1, Channel::query()->whereDownVotedBy($user)->count());
        self::assertSame(0, Channel::query()->whereDownVotedBy($other)->count());
    }

    public function testScopeWhereNotDownvotedBy(): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $channel = Channel::query()->create();
        $user->downvote($channel);
        self::assertSame(0, Channel::query()->whereNotDownvotedBy($user)->count());
        self::assertSame(1, Channel::query()->whereNotDownvotedBy($other)->count());
    }
}
