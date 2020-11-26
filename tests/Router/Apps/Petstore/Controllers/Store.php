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

namespace Tests\Router\Apps\Petstore\Controllers;

use Leevel\Http\Request;

class Store
{
    #[Route(
        path: "/store",
    )]
    private function getInventory(): void
    {
    }

    #[Route(
        path: "/store/order",
        method: Request::METHOD_POST,
    )]
    private function placeOrder(): void
    {
    }

    #[Route(
        path: "/store/order/{orderId}",
    )]
    private function getOrderById(): void
    {
    }

    #[Route(
        path: "/store/order/{orderId}",
        method: Request::METHOD_DELETE,
    )]
    private function deleteOrder(): void
    {
    }
}
