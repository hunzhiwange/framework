<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonActionSingle;

/**
 * foundAction.
 */
class FoundAction
{
    public function handle(): string
    {
        return 'hello colon with controller with foundAction and action is single';
    }
}
