# Laravel Vote

User upvote/downvote behaviour for Laravel.

<p align="center">
<a href="https://github.com/zingimmick/laravel-vote/actions"><img src="https://github.com/zingimmick/laravel-vote/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://codecov.io/gh/zingimmick/laravel-vote"><img src="https://codecov.io/gh/zingimmick/laravel-vote/branch/master/graph/badge.svg" alt="Code Coverage" /></a>
<a href="https://packagist.org/packages/zing/laravel-vote"><img src="https://poser.pugx.org/zing/laravel-vote/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-vote"><img src="https://poser.pugx.org/zing/laravel-vote/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/zing/laravel-vote"><img src="https://poser.pugx.org/zing/laravel-vote/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-vote"><img src="https://poser.pugx.org/zing/laravel-vote/license" alt="License"></a>
<a href="https://codeclimate.com/github/zingimmick/laravel-vote/maintainability"><img src="https://api.codeclimate.com/v1/badges/82036f5ecf894e9c395d/maintainability" alt="Code Climate" /></a>
</p>

> **Requires [PHP 7.2.0+](https://php.net/releases/)**

Require Laravel Vote using [Composer](https://getcomposer.org):

```bash
composer require zing/laravel-vote
```

## Usage

### Setup Voter

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelVote\Concerns\Voter;

class User extends Model
{
    use Voter;
}
```

### Setup Voteable

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelVote\Concerns\Voteable;

class Channel extends Model
{
    use Voteable;
}
```

### Voter

```php
use Zing\LaravelVote\Tests\Models\Channel;
/** @var \Zing\LaravelVote\Tests\Models\User $user */
/** @var \Zing\LaravelVote\Tests\Models\Channel $channel */
// Vote to Voteable
$user->vote($channel);
$user->upvote($channel);
$user->downvote($channel);
$user->cancelVote($channel);

// Compare Voteable
$user->hasVoted($channel);
$user->hasNotVoted($channel);
$user->hasUpvoted($channel);
$user->hasNotUpvoted($channel);
$user->hasDownvoted($channel);
$user->hasNotDownvoted($channel);

// Get voted info
$user->votes()->count(); 

// with type
$user->votes()->withType(Channel::class)->count(); 
$user->votedChannels()->count();
$user->upvotedChannels()->count();
$user->downvotedChannels()->count();

// get voted channels
Channel::query()->whereVotedBy($user)->get();
Channel::query()->whereUpvotedBy($user)->get();
Channel::query()->whereDownvotedBy($user)->get();

// get voted channels doesnt voted
Channel::query()->whereNotVotedBy($user)->get();
Channel::query()->whereNotUpvotedBy($user)->get();
Channel::query()->whereNotDownvotedBy($user)->get();
```

### Voteable

```php
use Zing\LaravelVote\Tests\Models\User;
use Zing\LaravelVote\Tests\Models\Channel;
/** @var \Zing\LaravelVote\Tests\Models\User $user */
/** @var \Zing\LaravelVote\Tests\Models\Channel $channel */
// Compare Voter
$channel->isVotedBy($user); 
$channel->isNotVotedBy($user);
$channel->isUpvotedBy($user); 
$channel->isNotUpvotedBy($user);
$channel->isDownvotedBy($user); 
$channel->isNotDownvotedBy($user);
// Get voters info
$channel->voters->each(function (User $user){
    echo $user->getKey();
});
$channel->upvoters->each(function (User $user){
    echo $user->getKey();
});
$channel->downvoters->each(function (User $user){
    echo $user->getKey();
});

$channels = Channel::query()->withCount('voters')->get();
$channels->each(function (Channel $channel){
    echo $channel->voters()->count(); // 1100
    echo $channel->voters_count; // "1100"
    echo $channel->votersCount(); // 1100
    echo $channel->votersCountForHumans(); // "1.1K"
    echo $channel->upvoters()->count(); // 1100
    echo $channel->upvoters_count; // "1100"
    echo $channel->upvotersCount(); // 1100
    echo $channel->upvotersCountForHumans(); // "1.1K"
    echo $channel->downvoters()->count(); // 1100
    echo $channel->downvoters_count; // "1100"
    echo $channel->downvotersCount(); // 1100
    echo $channel->downvotersCountForHumans(); // "1.1K"
});
```

## License

Laravel Vote is an open-sourced software licensed under the [MIT license](LICENSE).
