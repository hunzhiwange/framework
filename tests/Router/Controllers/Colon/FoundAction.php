<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Colon;

/**
 * foundAction.
 */
class FoundAction
{
    public function handle(): string
    {
        return 'hello colon with controller with foundAction';
    }
}
