<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert;

use Leevel\Kernel\App;

class Demo6
{
    public function doc1(): string
    {
        return App::class;
    }
}
