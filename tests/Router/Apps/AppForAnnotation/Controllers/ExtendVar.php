<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

use Leevel\Http\Request;
use Leevel\Router\Route;

class ExtendVar
{
    #[Route(
        path: '/extendVar/test',
        attributes: ['args1' => 'hello', 'args2' => 'world'],
    )]
    public function withExtendVar(Request $request): string
    {
        return 'withExtendVar and attributes are '.
            json_encode($request->attributes->all());
    }
}
