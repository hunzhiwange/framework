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

use Leevel\Di\Container;
use Leevel\Router\MiddlewareParser;
use Leevel\Router\Router;
use Tests\TestCase;

/**
 * middlewareParser test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.13
 *
 * @version 1.0
 */
class MiddlewareParserTest extends TestCase
{
    public function testBaseUse()
    {
        $middlewareParser = $this->createMiddlewareParser();

        $this->assertSame([], $middlewareParser->handle([]));

        $data = <<<'eot'
{
    "handle": [
        "Tests\\Router\\Middlewares\\Demo2@handle"
    ],
    "terminate": [
        "Tests\\Router\\Middlewares\\Demo1@terminate",
        "Tests\\Router\\Middlewares\\Demo2@terminate"
    ]
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $middlewareParser->handle(['group1'])
            )
        );
    }

    public function testGounpWithNormal()
    {
        $middlewareParser = $this->createMiddlewareParser();

        $this->assertSame([], $middlewareParser->handle([]));

        $data = <<<'eot'
{
    "handle": [
        "Tests\\Router\\Middlewares\\Demo2@handle",
        "Tests\\Router\\Middlewares\\Demo3@handle:20,foo"
    ],
    "terminate": [
        "Tests\\Router\\Middlewares\\Demo1@terminate",
        "Tests\\Router\\Middlewares\\Demo2@terminate"
    ]
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $middlewareParser->handle(['group1', 'demo3:20,foo'])
            )
        );
    }

    public function testNormals()
    {
        $middlewareParser = $this->createMiddlewareParser();

        $this->assertSame([], $middlewareParser->handle([]));

        $data = <<<'eot'
{
    "handle": [
        "Tests\\Router\\Middlewares\\Demo3@handle:20,foo"
    ],
    "terminate": [
        "Tests\\Router\\Middlewares\\Demo1@terminate"
    ]
}
eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $middlewareParser->handle(['demo1', 'demo3:20,foo'])
            )
        );
    }

    public function testInvalidMiddlewareException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Middleware only allowed string.'
        );

        $middlewareParser = $this->createMiddlewareParser();

        $this->assertSame([], $middlewareParser->handle([]));

        $middlewareParser->handle([[]]);
    }

    public function testMiddlewareNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Middleware Tests\\Router\\Middlewares\\NotFound was not found.'
        );

        $middlewareParser = $this->createMiddlewareParserNotFound();

        $middlewareParser->handle(['notFound']);
    }

    protected function createMiddlewareParser(): MiddlewareParser
    {
        return new MiddlewareParser($this->createRouter());
    }

    protected function createRouter(): Router
    {
        $router = new Router(new Container());

        $router->setMiddlewareGroups([
            'group1' => [
                'demo1',
                'demo2',
            ],

            'group2' => [
                'demo1',
                'demo3:10,world',
            ],

            'group3' => [
                'demo1',
                'demo2',
                'demo3:10,world',
            ],
        ]);

        $router->setMiddlewareAlias([
            'demo1' => 'Tests\\Router\\Middlewares\\Demo1',
            'demo2' => 'Tests\\Router\\Middlewares\\Demo2',
            'demo3' => 'Tests\\Router\\Middlewares\\Demo3',
        ]);

        return $router;
    }

    protected function createMiddlewareParserNotFound(): MiddlewareParser
    {
        return new MiddlewareParser($this->createRouterNotFound());
    }

    protected function createRouterNotFound(): Router
    {
        $router = new Router(new Container());

        $router->setMiddlewareAlias([
            'notFound' => 'Tests\\Router\\Middlewares\\NotFound',
        ]);

        return $router;
    }
}
