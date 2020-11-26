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

namespace Tests\Router;

use Leevel\Router\IRouter;
use Leevel\Router\MiddlewareParser;
use Leevel\Router\AnnotationRouter;
use Tests\TestCase;

class AnnotationRouterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $annotationRouter = new AnnotationRouter(
            $this->createMiddlewareParser(),
            'queryphp.cn',
            [
                '*' => [
                    'middlewares'=> 'common',
                ],
                'foo/*world' => [
                    'middlewares' => 'custom',
                ],
                'api/test' => [
                    'middlewares' => 'api',
                ],
                '/api/v1' => [
                    'middlewares' => 'api',
                ],
                'api/v2' => [
                    'middlewares' => 'api',
                ],
                '/web/v1' => [
                    'middlewares' => 'web',
                ],
                'web/v2' => [
                    'middlewares' => 'web',
                ],
            ],
            [
                'pet'   => [],
                'store' => [],
                'user'  => [],
            ],
        );

        $scandir = __DIR__.'/Apps/Petstore';

        $annotationRouter->addScandir($scandir);
        $result = $annotationRouter->handle();

        $data = file_get_contents($scandir.'/router.json');

        $this->assertSame(
            trim($data),
            $this->varJson(
                $result
            )
        );
    }

    public function testWithoutBasePaths(): void
    {
        $annotationRouter = new AnnotationRouter(
            $this->createMiddlewareParser(),
            'queryphp.cn',
            [],
            [
                'pet'   => [],
                'store' => [],
                'user'  => [],
            ],
        );

        $scandir = __DIR__.'/Apps/AppWithoutBasePaths';

        $annotationRouter->addScandir($scandir);
        $result = $annotationRouter->handle();

        $data = file_get_contents($scandir.'/router.json');

        $this->assertSame(
            trim($data),
            $this->varJson(
                $result
            )
        );
    }

    public function testAppWithControllerDirMatche(): void
    {
        $annotationRouter = new AnnotationRouter(
            $this->createMiddlewareParser(),
            'queryphp.cn',
            [],
            [
                'pet'   => [],
                'store' => [],
                'user'  => [],
            ],
        );

        $scandir = __DIR__.'/Apps/AppWithControllerDirNotMatche';

        $annotationRouter->addScandir($scandir);
        $result = $annotationRouter->handle();

        $data = file_get_contents($scandir.'/router.json');

        $this->assertSame(
            trim($data),
            $this->varJson(
                $result
            )
        );
    }

    public function testWithoutBasePathsAndGroups(): void
    {
        $annotationRouter = new AnnotationRouter(
            $this->createMiddlewareParser(),
            'queryphp.cn',
            [],
            [],
        );

        $scandir = __DIR__.'/Apps/AppWithoutBasePaths';

        $annotationRouter->addScandir($scandir);
        $result = $annotationRouter->handle();

        $data = file_get_contents($scandir.'/router_without_base_paths_and_groups.json');

        $this->assertSame(
            trim($data),
            $this->varJson(
                $result
            )
        );
    }

    public function testAppGroup(): void
    {
        $annotationRouter = new AnnotationRouter(
            $this->createMiddlewareParser(),
            'queryphp.cn',
            [],
            [
                'pet'   => [],
                'store' => [],
                'user'  => [],
            ],
        );

        $scandir = __DIR__.'/Apps/AppGroup';

        $annotationRouter->addScandir($scandir);
        $result = $annotationRouter->handle();

        $data = file_get_contents($scandir.'/router.json');

        $this->assertSame(
            trim($data),
            $this->varJson(
                $result
            )
        );
    }

    public function testAddScandirButNotFound(): void
    {
        $scandir = __DIR__.'/Apps/PetstorenNotFound';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Annotation routing scandir %s is not exits.', $scandir));

        $annotationRouter = new AnnotationRouter($this->createMiddlewareParser());
        $annotationRouter->addScandir($scandir);
    }

    public function testBasePathsIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Router base paths and groups must be array:string:array.');

        $annotationRouter = new AnnotationRouter(
            $this->createMiddlewareParser(),
            'queryphp.cn',
            ['middlewares' => 5],
            [],
        );

        $scandir = __DIR__.'/Apps/AppWithoutBasePaths';

        $annotationRouter->addScandir($scandir);
        $annotationRouter->handle();
    }

    public function testGroupsIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Router base paths and groups must be array:string:array.');

        $annotationRouter = new AnnotationRouter(
            $this->createMiddlewareParser(),
            'queryphp.cn',
            [],
            ['middlewares' => 'hello world'],
        );

        $scandir = __DIR__.'/Apps/AppWithoutBasePaths';

        $annotationRouter->addScandir($scandir);
        $annotationRouter->handle();
    }

    protected function createMiddlewareParser(): MiddlewareParser
    {
        $router = $this->createMock(IRouter::class);
        $this->assertInstanceof(IRouter::class, $router);

        return new MiddlewareParser($router);
    }
}
