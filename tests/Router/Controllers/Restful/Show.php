<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Restful;

use Leevel\Router\IRouter;

/**
 * show.
 */
class Show
{
    public function handle()
    {
        return 'hello for restful '.IRouter::RESTFUL_SHOW;
    }
}
