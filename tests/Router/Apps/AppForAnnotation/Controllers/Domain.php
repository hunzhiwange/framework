<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

use Leevel\Http\Request;

class Domain
{
    #[Route(
        path: "/domain/test",
        domain: "queryphp.com",
    )]
    private function fooNotMatchedDomain(): void
    {
    }

    #[Route(
        path: "/domain/test2",
        domain: "queryphp.com",
    )]
    public function barMatchedDomain(): string
    {
        return 'barMatchedDomain';
    }

    #[Route(
        path: "/domain/test3",
        domain: "{subdomain:[A-Za-z]+}-vip.{domain}.queryphp.com",
    )]
    public function barMatchedDomainWithVar(Request $request): string
    {
        return 'barMatchedDomainWithVar and attributes are '.
            json_encode($request->attributes->all());
    }

    #[Route(
        path: "/domain/test4",
        domain: "api",
    )]
    public function barMatchedDomainWithoutExtend(): string
    {
        return 'barMatchedDomainWithoutExtend';
    }
}
