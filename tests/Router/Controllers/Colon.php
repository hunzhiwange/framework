<?php

declare(strict_types=1);

namespace Tests\Router\Controllers;

class Colon
{
    public function foundAction(): string
    {
        return 'hello colon with controller with foundAction';
    }
}
