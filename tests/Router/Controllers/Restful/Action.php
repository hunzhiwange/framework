<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Restful;

use Leevel\Router\IRouter;

class Action
{
    public function handle(): string
    {
        return 'hello action for restful '.IRouter::RESTFUL_SHOW;
    }
}
