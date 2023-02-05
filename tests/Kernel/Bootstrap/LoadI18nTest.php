<?php

declare(strict_types=1);

namespace Tests\Kernel\Bootstrap;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\Bootstrap\LoadI18n;
use Leevel\Kernel\IApp;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="初始化载入语言包",
 *     path="architecture/kernel/bootstrap/loadi18n",
 *     zh-CN:description="
 * QueryPHP 在内核执行过程中会执行初始化，分为 4 个步骤，载入配置、载入语言包、注册异常运行时和遍历服务提供者注册服务。
 *
 * 内核初始化，包括 `\Leevel\Kernel\IKernel::bootstrap` 和 `\Leevel\Kernel\IKernelConsole::bootstrap` 均会执行上述 4 个步骤。
 * ",
 *     zh-CN:note="",
 * )
 *
 * @internal
 *
 * @coversNothing
 */
final class LoadI18nTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    /**
     * @api(
     *     zh-CN:title="基本使用方法",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $bootstrap = new LoadI18n();

        $container = Container::singletons();
        $app = new App($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $option = new Option([
            'app' => [
                ':composer' => [
                    'i18ns' => [
                        'extend',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertSame('en-US', $container['option']['i18n\\default']);
        static::assertSame($appPath.'/storage/bootstrap/i18n/en-US.php', $app->i18nCachedPath('en-US'));
        static::assertFalse($app->isCachedI18n('en-US'));
        static::assertSame($appPath.'/assets/i18n', $app->i18nPath());

        static::assertNull($bootstrap->handle($app));

        $i18n = $container->make('i18n');

        static::assertSame('Bad Request', $i18n->gettext('错误请求'));
        static::assertSame('Unprocessable Entity', $i18n->gettext('无法处理的实体'));
        static::assertSame('Total 5', $i18n->gettext('共 %d 条', 5));
        static::assertSame('Go to', $i18n->gettext('前往'));
    }

    /**
     * @api(
     *     zh-CN:title="语言支持缓存",
     *     zh-CN:description="
     * 语言支持缓存，通过缓存可以降低开销提高性能，适合生产环境。
     *
     * **fixture 定义**
     *
     * **语言缓存文件 tests/Kernel/Bootstrap/app/assert/en-US.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Kernel/Bootstrap/app/assert/en-US.php')]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testLoadCached(): void
    {
        $bootstrap = new LoadI18n();

        $container = Container::singletons();
        $app = new App($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $option = new Option([
            'app' => [
                ':composer' => [
                    'i18ns' => [
                        'extend',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        static::assertSame('en-US', $container['option']['i18n\\default']);
        static::assertSame($appPath.'/storage/bootstrap/i18n/en-US.php', $app->i18nCachedPath('en-US'));
        static::assertFalse($app->isCachedI18n('en-US'));
        static::assertSame($appPath.'/assets/i18n', $app->i18nPath());

        mkdir($appPath.'/storage/bootstrap/i18n', 0o777, true);
        file_put_contents($appPath.'/storage/bootstrap/i18n/en-US.php', file_get_contents($appPath.'/assert/en-US.php'));

        static::assertTrue($app->isCachedI18n('en-US'));

        static::assertNull($bootstrap->handle($app));

        $i18n = $container->make('i18n');

        static::assertSame('Bad Request', $i18n->gettext('错误请求'));
        static::assertSame('Unprocessable Entity', $i18n->gettext('无法处理的实体'));
        static::assertSame('Total 5', $i18n->gettext('共 %d 条', 5));
        static::assertSame('Go to', $i18n->gettext('前往'));

        Helper::deleteDirectory($appPath.'/storage');
    }

    public function testExtendI18nDirNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            sprintf('I18n dir %s is not exist.', __DIR__.'/app/extend/notFound')
        );

        $bootstrap = new LoadI18n();

        $container = Container::singletons();
        $app = new App($container, $appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        $option = new Option([
            'app' => [
                ':composer' => [
                    'i18ns' => [
                        'extend/notFound',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $bootstrap->handle($app);
    }
}

class App extends Apps
{
    protected function registerBaseProvider(): void
    {
    }
}
