<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Tests\Concerns;

use LaravelInteraction\Vote\Tests\Models\Channel;
use LaravelInteraction\Vote\Tests\Models\User;
use LaravelInteraction\Vote\Tests\TestCase;
use Mockery;

class VoteableTest extends TestCase
{
    public function modelClasses(): array
    {
        return[
            [Channel::class],
            [User::class],
        ];
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testVotes(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $model->voteableVotes()->count());
        self::assertSame(1, $model->voteableVotes->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testVotersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $model->votersCount());
        $user->cancelVote($model);
        self::assertSame(1, $model->votersCount());
        $model->loadCount('voters');
        self::assertSame(0, $model->votersCount());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testUpvotersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(1, $model->upvotersCount());
        $user->cancelVote($model);
        self::assertSame(1, $model->upvotersCount());
        $model->loadCount('upvoters');
        self::assertSame(0, $model->upvotersCount());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testDownvotersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(1, $model->downvotersCount());
        $user->cancelVote($model);
        self::assertSame(1, $model->downvotersCount());
        $model->loadCount('downvoters');
        self::assertSame(0, $model->downvotersCount());
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

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testIsVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isVotedBy($model));
        $user->vote($model);
        self::assertTrue($model->isVotedBy($user));
        $model->load('voters');
        $user->cancelVote($model);
        self::assertTrue($model->isVotedBy($user));
        $model->load('voters');
        self::assertFalse($model->isVotedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testIsNotVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotVotedBy($model));
        $user->vote($model);
        self::assertFalse($model->isNotVotedBy($user));
        $model->load('voters');
        $user->cancelVote($model);
        self::assertFalse($model->isNotVotedBy($user));
        $model->load('voters');
        self::assertTrue($model->isNotVotedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testIsUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isVotedBy($model));
        $user->upvote($model);
        self::assertTrue($model->isUpvotedBy($user));
        $model->load('upvoters');
        $user->cancelVote($model);
        self::assertTrue($model->isUpvotedBy($user));
        $model->load('upvoters');
        self::assertFalse($model->isUpvotedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testIsNotUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotUpvotedBy($model));
        $user->vote($model);
        self::assertFalse($model->isNotUpvotedBy($user));
        $model->load('upvoters');
        $user->cancelVote($model);
        self::assertFalse($model->isNotUpvotedBy($user));
        $model->load('upvoters');
        self::assertTrue($model->isNotUpvotedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testIsDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isDownvotedBy($model));
        $user->downvote($model);
        self::assertTrue($model->isDownvotedBy($user));
        $model->load('downvoters');
        $user->cancelVote($model);
        self::assertTrue($model->isDownvotedBy($user));
        $model->load('downvoters');
        self::assertFalse($model->isDownvotedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testIsNotDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotDownvotedBy($model));
        $user->downvote($model);
        self::assertFalse($model->isNotDownvotedBy($user));
        $model->load('downvoters');
        $user->cancelVote($model);
        self::assertFalse($model->isNotDownvotedBy($user));
        $model->load('downvoters');
        self::assertTrue($model->isNotDownvotedBy($user));
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testVoters(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $model->voters()->count());
        $user->cancelVote($model);
        self::assertSame(0, $model->voters()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testUpvoters(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(1, $model->upvoters()->count());
        $user->cancelVote($model);
        self::assertSame(0, $model->upvoters()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testDownvoters(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(1, $model->downvoters()->count());
        $user->cancelVote($model);
        self::assertSame(0, $model->downvoters()->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame(1, $modelClass::query()->whereVotedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereVotedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereNotVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        self::assertSame($modelClass::query()->whereKeyNot($model->getKey())->count(), $modelClass::query()->whereNotVotedBy($user)->count());
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotVotedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame(1, $modelClass::query()->whereUpvotedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereUpvotedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereNotUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        self::assertSame($modelClass::query()->whereKeyNot($model->getKey())->count(), $modelClass::query()->whereNotUpvotedBy($user)->count());
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotUpvotedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame(1, $modelClass::query()->whereDownVotedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereDownVotedBy($other)->count());
    }

    /**
     * @dataProvider modelClasses
     *
     * @param \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel|string $modelClass
     */
    public function testScopeWhereNotDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        self::assertSame($modelClass::query()->whereKeyNot($model->getKey())->count(), $modelClass::query()->whereNotDownvotedBy($user)->count());
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotDownvotedBy($other)->count());
    }
}
