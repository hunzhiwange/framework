<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\ColonActionSingle\Action\Foo;

/**
 * bar.
 */
class Bar
{
    public function handle(): string
    {
        return 'hello colon with action and action is not single class and action is single';
    }
}
