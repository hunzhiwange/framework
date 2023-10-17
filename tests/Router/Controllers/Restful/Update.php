<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Restful;

use Leevel\Router\IRouter;

class Update
{
    public function handle(): string
    {
        return 'hello for restful '.IRouter::RESTFUL_UPDATE;
    }
}
