<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Tests\Concerns;

use LaravelInteraction\Vote\Tests\Models\Channel;
use LaravelInteraction\Vote\Tests\Models\User;
use LaravelInteraction\Vote\Tests\TestCase;

/**
 * @internal
 */
final class VoteableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Vote\Tests\Models\Channel|\LaravelInteraction\Vote\Tests\Models\User>>>
     */
    public static function provideModelClasses(): \Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVotes(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame(1, $model->voteableVotes()->count());
        $this->assertSame(1, $model->voteableVotes->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVotersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame(1, $model->votersCount());
        $user->cancelVote($model);
        $this->assertSame(1, $model->votersCount());
        $model->loadCount('voters');
        $this->assertSame(0, $model->votersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testUpvotersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        $this->assertSame(1, $model->upvotersCount());
        $user->cancelVote($model);
        $this->assertSame(1, $model->upvotersCount());
        $model->loadCount('upvoters');
        $this->assertSame(0, $model->upvotersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testDownvotersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        $this->assertSame(1, $model->downvotersCount());
        $user->cancelVote($model);
        $this->assertSame(1, $model->downvotersCount());
        $model->loadCount('downvoters');
        $this->assertSame(0, $model->downvotersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVotersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame('1', $model->votersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testUpvotersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        $this->assertSame('1', $model->upvotersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testDownvotersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        $this->assertSame('1', $model->downvotersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertFalse($model->isVotedBy($model));
        $user->vote($model);
        $this->assertTrue($model->isVotedBy($user));
        $model->load('voters');
        $user->cancelVote($model);
        $this->assertTrue($model->isVotedBy($user));
        $model->load('voters');
        $this->assertFalse($model->isVotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsNotVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertTrue($model->isNotVotedBy($model));
        $user->vote($model);
        $this->assertFalse($model->isNotVotedBy($user));
        $model->load('voters');
        $user->cancelVote($model);
        $this->assertFalse($model->isNotVotedBy($user));
        $model->load('voters');
        $this->assertTrue($model->isNotVotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertFalse($model->isVotedBy($model));
        $user->upvote($model);
        $this->assertTrue($model->isUpvotedBy($user));
        $model->load('upvoters');
        $user->cancelVote($model);
        $this->assertTrue($model->isUpvotedBy($user));
        $model->load('upvoters');
        $this->assertFalse($model->isUpvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsNotUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertTrue($model->isNotUpvotedBy($model));
        $user->vote($model);
        $this->assertFalse($model->isNotUpvotedBy($user));
        $model->load('upvoters');
        $user->cancelVote($model);
        $this->assertFalse($model->isNotUpvotedBy($user));
        $model->load('upvoters');
        $this->assertTrue($model->isNotUpvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertFalse($model->isDownvotedBy($model));
        $user->downvote($model);
        $this->assertTrue($model->isDownvotedBy($user));
        $model->load('downvoters');
        $user->cancelVote($model);
        $this->assertTrue($model->isDownvotedBy($user));
        $model->load('downvoters');
        $this->assertFalse($model->isDownvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testIsNotDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertTrue($model->isNotDownvotedBy($model));
        $user->downvote($model);
        $this->assertFalse($model->isNotDownvotedBy($user));
        $model->load('downvoters');
        $user->cancelVote($model);
        $this->assertFalse($model->isNotDownvotedBy($user));
        $model->load('downvoters');
        $this->assertTrue($model->isNotDownvotedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testVoters(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame(1, $model->voters()->count());
        $user->cancelVote($model);
        $this->assertSame(0, $model->voters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testUpvoters(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        $this->assertSame(1, $model->upvoters()->count());
        $user->cancelVote($model);
        $this->assertSame(0, $model->upvoters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testDownvoters(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        $this->assertSame(1, $model->downvoters()->count());
        $user->cancelVote($model);
        $this->assertSame(0, $model->downvoters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame(1, $modelClass::query()->whereVotedBy($user)->count());
        $this->assertSame(0, $modelClass::query()->whereVotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotVotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotVotedBy($user)->count()
        );
        $this->assertSame($modelClass::query()->count(), $modelClass::query()->whereNotVotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        $this->assertSame(1, $modelClass::query()->whereUpvotedBy($user)->count());
        $this->assertSame(0, $modelClass::query()->whereUpvotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotUpvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        $this->assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotUpvotedBy($user)->count()
        );
        $this->assertSame($modelClass::query()->count(), $modelClass::query()->whereNotUpvotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        $this->assertSame(1, $modelClass::query()->whereDownVotedBy($user)->count());
        $this->assertSame(0, $modelClass::query()->whereDownVotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotDownvotedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        $this->assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotDownvotedBy($user)->count()
        );
        $this->assertSame($modelClass::query()->count(), $modelClass::query()->whereNotDownvotedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumVotes(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();

        /** @var \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel $model */
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame(1, $model->sumVotes());
        $user->cancelVote($model);
        $this->assertSame(1, $model->sumVotes());
        $model->loadSum('voteableVotes', 'votes');
        $this->assertSame(0, $model->sumVotes());
        $user->upvote($model, 3);
        $model->loadSum('voteableVotes', 'votes');
        $this->assertSame(3, $model->sumVotes());
        $other->downvote($model, 2);
        $model->loadSum('voteableVotes', 'votes');
        $this->assertSame(1, $model->sumVotes());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumUpvotes(string $modelClass): void
    {
        $user = User::query()->create();

        /** @var \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel $model */
        $model = $modelClass::query()->create();
        $user->upvote($model);
        $this->assertSame(1, $model->sumUpvotes());
        $user->cancelVote($model);
        $this->assertSame(1, $model->sumUpvotes());
        $model->loadSum([
            'voteableVotes as voteable_votes_sum_upvotes' => static fn ($query) => $query->where('votes', '>', 0),
        ], 'votes');
        $this->assertSame(0, $model->sumUpvotes());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumDownvotes(string $modelClass): void
    {
        $user = User::query()->create();

        /** @var \LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel $model */
        $model = $modelClass::query()->create();
        $user->downvote($model);
        $this->assertSame(-1, $model->sumDownvotes());
        $user->cancelVote($model);
        $this->assertSame(-1, $model->sumDownvotes());
        $model->loadSum([
            'voteableVotes as voteable_votes_sum_downvotes' => static fn ($query) => $query->where('votes', '<', 0),
        ], 'votes');
        $this->assertSame(0, $model->sumDownvotes());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumVotesForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->vote($model);
        $this->assertSame('1', $model->sumVotesForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumUpvotesForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->upvote($model);
        $this->assertSame('1', $model->sumUpvotesForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Vote\Tests\Models\User|\LaravelInteraction\Vote\Tests\Models\Channel> $modelClass
     */
    public function testSumDownvotesForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->downvote($model);
        $this->assertSame('-1', $model->sumDownvotesForHumans());
    }
}
