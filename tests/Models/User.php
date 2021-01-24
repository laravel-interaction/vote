<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Zing\LaravelVote\Concerns\Voter;

/**
 * @method static \Zing\LaravelVote\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Voter;

    public function votedChannels(): MorphToMany
    {
        return $this->votedItems(Channel::class);
    }

    public function upvotedChannels(): MorphToMany
    {
        return $this->upvotedItems(Channel::class);
    }

    public function downvotedChannels(): MorphToMany
    {
        return $this->downvotedItems(Channel::class);
    }
}
