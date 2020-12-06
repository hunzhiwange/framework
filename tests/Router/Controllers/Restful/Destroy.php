<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Restful;

use Leevel\Router\IRouter;

/**
 * destroy.
 */
class Destroy
{
    public function handle()
    {
        return 'hello for restful '.IRouter::RESTFUL_DESTROY;
    }
}
