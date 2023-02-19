<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Router\MiddlewareParser;
use Leevel\Router\Router;
use Tests\TestCase;

final class MiddlewareParserTest extends TestCase
{
    public function testBaseUse(): void
    {
        $middlewareParser = $this->createMiddlewareParser();

        static::assertSame([], $middlewareParser->handle([]));

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

        static::assertSame(
            $data,
            $this->varJson(
                $middlewareParser->handle(['group1'])
            )
        );
    }

    public function testGounpWithNormal(): void
    {
        $middlewareParser = $this->createMiddlewareParser();

        static::assertSame([], $middlewareParser->handle([]));

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

        static::assertSame(
            $data,
            $this->varJson(
                $middlewareParser->handle(['group1', 'demo3:20,foo'])
            )
        );
    }

    public function testNormals(): void
    {
        $middlewareParser = $this->createMiddlewareParser();

        static::assertSame([], $middlewareParser->handle([]));

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

        static::assertSame(
            $data,
            $this->varJson(
                $middlewareParser->handle(['demo1', 'demo3:20,foo'])
            )
        );
    }

    public function testInvalidMiddlewareException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Middleware only allowed string.'
        );

        $middlewareParser = $this->createMiddlewareParser();

        static::assertSame([], $middlewareParser->handle([]));

        $middlewareParser->handle([[]]);
    }

    public function testMiddlewareNotFound(): void
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
