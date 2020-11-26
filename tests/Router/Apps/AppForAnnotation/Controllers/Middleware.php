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

class Middleware
{
    #[Route(
        path: "/middleware/test",
        middlewares: "group1",
    )]
    public function foo(): string
    {
        return 'Middleware matched';
    }

    #[Route(
        path: "/middleware/test2",
        middlewares: ["group1", "group2"],
    )]
    public function bar(): string
    {
        return 'Middleware matched 2';
    }

    #[Route(
        path: "/middleware/test3",
        middlewares: ["group1", "group2", "demo_for_base_path"],
    )]
    public function hello(): string
    {
        return 'Middleware matched 3';
    }

    #[Route(
        path: "/middleware/test4",
        middlewares: ["Tests\\Router\\Middlewares\\Demo1"],
    )]
    public function world(): string
    {
        return 'Middleware matched 4';
    }
}
