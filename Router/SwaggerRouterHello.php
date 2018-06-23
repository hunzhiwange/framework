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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router;

use Leevel\Router\SwaggerRouter;
use Tests\TestCase;

/**
 * swagger 生成注解路由组件测试.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.10
 *
 * @version 1.0
 */
class SwaggerRouterHello extends TestCase
{
    public function tes2tAddSwaggerScanCheckDir()
    {
        $this->expectException(\InvalidArgumentException::class);

        $swaggerRouter = new SwaggerRouter();

        $scanDir = __DIR__.'/Petstore___';

        $swaggerRouter->addSwaggerScan($scanDir);
    }

    public function tes2tSwaggerHandle()
    {
        $swaggerRouter = new SwaggerRouter('Router');

        $scanDir = __DIR__.'/Petstore';

        $swaggerRouter->addSwaggerScan($scanDir);
        $result = $swaggerRouter->handle();

        $this->assertSame($result, include $scanDir.'/cache.php');
    }
}
