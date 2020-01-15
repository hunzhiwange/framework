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

use Leevel\Router\IView;
use Leevel\Router\View;
use Leevel\View\Html;
use Leevel\View\Phpui;
use Tests\TestCase;

/**
 * @api(
 *     title="View",
 *     path="component/view",
 *     description="
 * 视图统一由视图组件完成，通常我们使用代理 `\Leevel\Router\Proxy\View` 类进行静态调用。
 *
 * 内置支持的视图驱动类型包括 html、phpui，未来可能增加其他驱动。
 *
 * ## 使用方式
 *
 * 使用容器 view 服务
 *
 * ``` php
 * \App::make('view')->setVar($name, $value = null): \Leevel\Router\IView;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private \Leevel\Router\IView $view;
 *
 *     public function __construct(\Leevel\Router\IView $view)
 *     {
 *         $this->view = $view;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Router\Proxy\View::->setVar($name, $value = null): \Leevel\Router\IView;
 * ```
 *
 * ## view 配置
 *
 * 系统的 view 配置位于应用下面的 `option/view.php` 文件。
 *
 * 可以定义多个视图连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/view.php')]}
 * ```
 *
 * mail 参数根据不同的连接会有所区别，通用的 view 参数如下：
 *
 * |配置项|配置描述|
 * |:-|:-|
 * |fail|错误模板|
 * |success|成功模板|
 * ",
 * note="值得注意的是，系统的视图组件是经过了路由的视图层做了一层封装。",
 * )
 */
class ViewTest extends TestCase
{
    /**
     * @api(
     *     title="视图基本使用",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $view = new View(
            $html = new Html()
        );
        $this->assertInstanceof(IView::class, $view);

        $view->setVar('hello', 'world');
        $this->assertSame('world', $view->getVar('hello'));
        $this->assertSame('world', $html->getVar('hello'));
    }

    /**
     * @api(
     *     title="deleteVar 删除变量值",
     *     description="",
     *     note="",
     * )
     */
    public function testDeleteVar(): void
    {
        $view = new View(
            $html = new Html()
        );

        $view->setVar('hello', 'world');
        $this->assertSame('world', $view->getVar('hello'));
        $this->assertSame('world', $html->getVar('hello'));

        $view->deleteVar(['hello']);
        $this->assertNull($view->getVar('hello'));
        $this->assertNull($html->getVar('hello'));
    }

    /**
     * @api(
     *     title="clearVar 清空变量值",
     *     description="",
     *     note="",
     * )
     */
    public function testClearVar(): void
    {
        $view = new View(
            $html = new Html()
        );

        $view->setVar('foo', 'bar');
        $this->assertSame('bar', $view->getVar('foo'));
        $this->assertSame('bar', $html->getVar('foo'));

        $view->clearVar();
        $this->assertNull($view->getVar('foo'));
        $this->assertNull($html->getVar('foo'));
    }

    /**
     * @api(
     *     title="display 加载视图文件",
     *     description="",
     *     note="",
     * )
     */
    public function testDisplay(): void
    {
        $view = new View(
            new Phpui([
                'theme_path' => __DIR__,
            ])
        );

        $view->setVar('foo', 'bar');

        $this->assertSame(
            'Hi here! bar',
            $view->display(__DIR__.'/assert/hello.php')
        );
    }

    /**
     * @api(
     *     title="switchView 切换视图",
     *     description="",
     *     note="",
     * )
     */
    public function testSwitchView(): void
    {
        $view = new View(
            $phpui = new Phpui()
        );

        $view->setVar('foo', 'bar');
        $this->assertSame('bar', $view->getVar('foo'));
        $this->assertSame('bar', $phpui->getVar('foo'));

        $view->switchView($html = new Html());
        $this->assertSame('bar', $view->getVar('foo'));
        $this->assertSame('bar', $html->getVar('foo'));
    }
}
