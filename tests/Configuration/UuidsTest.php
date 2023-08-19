<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Tests\Configuration;

use LaravelInteraction\Vote\Tests\Models\Channel;
use LaravelInteraction\Vote\Tests\Models\User;
use LaravelInteraction\Vote\Tests\TestCase;
use LaravelInteraction\Vote\Vote;

/**
 * @internal
 */
final class UuidsTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config([
            'vote.uuids' => true,
        ]);
    }

    public function testKeyType(): void
    {
        $vote = new Vote();
        $this->assertSame('string', $vote->getKeyType());
    }

    public function testIncrementing(): void
    {
        $vote = new Vote();
        $this->assertFalse($vote->getIncrementing());
    }

    public function testKeyName(): void
    {
        $vote = new Vote();
        $this->assertSame('uuid', $vote->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->vote($channel);
        $this->assertIsString($user->voterVotes()->firstOrFail()->getKey());
    }
}
