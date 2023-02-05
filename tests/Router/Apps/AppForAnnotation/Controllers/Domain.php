<?php

declare(strict_types=1);

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

use Leevel\Http\Request;

class Domain
{
    #[Route(
        path: '/domain/test2',
        domain: 'queryphp.com',
    )]
    public function barMatchedDomain(): string
    {
        return 'barMatchedDomain';
    }

    #[Route(
        path: '/domain/test3',
        domain: '{subdomain:[A-Za-z]+}-vip.{domain}.queryphp.com',
    )]
    public function barMatchedDomainWithVar(Request $request): string
    {
        return 'barMatchedDomainWithVar and attributes are '.
            json_encode($request->attributes->all());
    }

    #[Route(
        path: '/domain/test4',
        domain: 'api',
    )]
    public function barMatchedDomainWithoutExtend(): string
    {
        return 'barMatchedDomainWithoutExtend';
    }

    #[Route(
        path: '/domain/test',
        domain: 'queryphp.com',
    )]
    private function fooNotMatchedDomain(): void
    {
    }
}
