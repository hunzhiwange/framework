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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Option;

use Leevel\Di\Container;
use Leevel\Option\IOption;
use Tests\TestCase;

/**
 * helper test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.10
 *
 * @version 1.0
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testOption(): void
    {
        $option = $this->createMock(IOption::class);
        $this->assertNull($option->set(['foo' => 'bar']));
        $option->method('get')->willReturn('bar');
        $this->assertSame('bar', $option->get('foo'));

        $container = $this->createContainer();
        $container->singleton('option', function () use ($option) {
            return $option;
        });

        $this->assertInstanceof(IOption::class, f('Leevel\\Option\\Helper\\option'));
        $this->assertNull(f('Leevel\\Option\\Helper\\option_set', ['foo' => 'bar']));
        $this->assertSame('bar', f('Leevel\\Option\\Helper\\option_get', 'foo'));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
