<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonActionSingle\Action;

/**
 * beforeButFirst.
 */
class BeforeButFirst
{
    public function handle(): string
    {
        return 'hello colon with action and action is not single class before but first and action is single';
    }
}
