<?php

declare(strict_types=1);

namespace Zing\LaravelVote\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelVote\Concerns\Voteable;

/**
 * @method static \Zing\LaravelVote\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Voteable;
}
