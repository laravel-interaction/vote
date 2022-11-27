<?php

declare(strict_types=1);

namespace LaravelInteraction\Vote\Events;

use Illuminate\Database\Eloquent\Model;

class Voted
{
    public function __construct(
        public Model $model
    ) {
    }
}
