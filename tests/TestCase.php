<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Zing\LaravelVote\Tests\Models\User;
use Zing\LaravelVote\VoteServiceProvider;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        Schema::create(
            'users',
            function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->timestamps();
            }
        );
        Schema::create(
            'channels',
            function (Blueprint $table): void {
                $table->bigIncrements('id');
                $table->timestamps();
            }
        );
    }

    protected function getEnvironmentSetUp($app): void
    {
        config(
            [
                'database.default' => 'testing',
                'vote.models.user' => User::class,
            ]
        );
    }

    protected function getPackageProviders($app): array
    {
        return [
            VoteServiceProvider::class,
        ];
    }
}
