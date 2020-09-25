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

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Router\IView;
use Leevel\Router\Proxy\View as ProxyView;
use Leevel\Router\View;
use Leevel\View\Html;
use Tests\TestCase;

class ViewTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $view = new View(
            $html = new Html()
        );
        $this->assertInstanceof(IView::class, $view);

        $container = $this->createContainer();
        $container->singleton('view', function () use ($view): View {
            return $view;
        });

        $view->setVar('hello', 'world');
        $this->assertSame('world', $view->getVar('hello'));
        $this->assertSame('world', $html->getVar('hello'));
    }

    public function testProxy(): void
    {
        $view = new View(
            $html = new Html()
        );
        $this->assertInstanceof(IView::class, $view);

        $container = $this->createContainer();
        $container->singleton('view', function () use ($view): View {
            return $view;
        });

        ProxyView::setVar('hello', 'world');
        $this->assertSame('world', ProxyView::getVar('hello'));
        $this->assertSame('world', $html->getVar('hello'));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
