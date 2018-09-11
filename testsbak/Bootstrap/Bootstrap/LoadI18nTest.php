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

namespace Tests\Bootstrap\Bootstrap;

use Leevel\Bootstrap\Bootstrap\LoadI18n;
use Leevel\Bootstrap\Project as Projects;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * loadI18n test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.20
 *
 * @version 1.0
 */
class LoadI18nTest extends TestCase
{
    public function testBaseUse()
    {
        $bootstrap = new LoadI18n();

        $project = new Project($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $option = new Option([
            'app' => [
                '_composer' => [
                    'i18ns' => [
                        'extend',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertSame('en-US', $project['option']['i18n\\default']);
        $this->assertSame($appPath.'/runtime/i18n/en-US.php', $project->i18nCachedPath('en-US'));
        $this->assertFalse($project->isCachedI18n('en-US'));
        $this->assertSame($appPath.'/i18n', $project->i18nPath());

        $this->assertNull($bootstrap->handle($project));

        $i18n = $project->make('i18n');

        $this->assertSame('Bad Request', $i18n->gettext('错误请求'));
        $this->assertSame('Unprocessable Entity', $i18n->gettext('无法处理的实体'));
        $this->assertSame('Total 5', $i18n->gettext('共 %d 条', 5));
        $this->assertSame('Go to', $i18n->gettext('前往'));
    }

    public function testLoadCached()
    {
        $bootstrap = new LoadI18n();

        $project = new Project($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $option = new Option([
            'app' => [
                '_composer' => [
                    'i18ns' => [
                        'extend',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertSame('en-US', $project['option']['i18n\\default']);
        $this->assertSame($appPath.'/runtime/i18n/en-US.php', $project->i18nCachedPath('en-US'));
        $this->assertFalse($project->isCachedI18n('en-US'));
        $this->assertSame($appPath.'/i18n', $project->i18nPath());

        mkdir($appPath.'/runtime/i18n', 0777, true);
        file_put_contents($appPath.'/runtime/i18n/en-US.php', file_get_contents($appPath.'/assert/en-US.php'));

        $this->assertTrue($project->isCachedI18n('en-US'));

        $this->assertNull($bootstrap->handle($project));

        $i18n = $project->make('i18n');

        $this->assertSame('Bad Request', $i18n->gettext('错误请求'));
        $this->assertSame('Unprocessable Entity', $i18n->gettext('无法处理的实体'));
        $this->assertSame('Total 5', $i18n->gettext('共 %d 条', 5));
        $this->assertSame('Go to', $i18n->gettext('前往'));

        Fso::deleteDirectory($appPath.'/runtime', true);
    }

    public function testExtendI18nDirNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            sprintf('I18n dir %s is not exist.', __DIR__.'/app/extend/notFound')
        );

        $bootstrap = new LoadI18n();

        $project = new Project($appPath = __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $project);
        $this->assertInstanceof(Container::class, $project);

        $option = new Option([
            'app' => [
                '_composer' => [
                    'i18ns' => [
                        'extend/notFound',
                    ],
                ],
            ],
            'i18n' => [
                'default' => 'en-US',
            ],
        ]);

        $project->singleton('option', function () use ($option) {
            return $option;
        });

        $bootstrap->handle($project);
    }
}

class Project extends Projects
{
    protected function registerBaseProvider()
    {
    }
}
