<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Restful;

use Leevel\Router\IRouter;

/**
 * store.
 */
class Store
{
    public function handle()
    {
        return 'hello for restful '.IRouter::RESTFUL_STORE;
    }
}
