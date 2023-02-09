<?php

declare(strict_types=1);

namespace Tests\Router\Apps\Petstore\Controllers;

use Leevel\Http\Request;
use Leevel\Router\Route;

class Store
{
    #[Route(
        path: '/store',
    )]
    private function getInventory(): void
    {
    }

    #[Route(
        path: '/store/order',
        method: Request::METHOD_POST,
    )]
    private function placeOrder(): void
    {
    }

    #[Route(
        path: '/store/order/{orderId}',
    )]
    private function getOrderById(): void
    {
    }

    #[Route(
        path: '/store/order/{orderId}',
        method: Request::METHOD_DELETE,
    )]
    private function deleteOrder(): void
    {
    }
}
