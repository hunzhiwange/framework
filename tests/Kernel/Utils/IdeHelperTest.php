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

namespace Tests\Kernel\Utils;

use Leevel\Kernel\Utils\IdeHelper as UtilsIdeHelper;
use Tests\Kernel\Utils\Assert\DemoClass;
use Tests\Kernel\Utils\Assert\Helper\demo1;
use Tests\Kernel\Utils\Assert\Helper\demo2;
use Tests\Kernel\Utils\Assert\Helper\demo3;
use Tests\Kernel\Utils\Assert\Helper\demo4;
use Tests\Kernel\Utils\Assert\Helper\demo_hello_world;
use Tests\TestCase;

class IdeHelperTest extends TestCase
{
    public function testBaseUse(): void
    {
        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handle(DemoClass::class);
        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void Demo1()'),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void Demo2(string $hello, int $world)'),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static string Demo3(string $hello, ?int $world = null) demo3'),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void Demo4(...$hello)'),
            $result,
        );
    }

    public function testHandleFunction(): void
    {
        // import fn.
        class_exists(demo1::class);
        class_exists(demo2::class);
        class_exists(demo3::class);
        class_exists(demo4::class);
        class_exists(demo_hello_world::class);

        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handleFunction([
            demo1::class,
            demo2::class,
            demo3::class,
            demo4::class,
            demo_hello_world::class,
        ]);

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void demo1()'),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void demo2(string $hello, int $world)'),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static string demo3(string $hello, ?int $world = null) demo3'),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void demo4(...$hello)'),
            $result,
        );
        $this->assertStringContainsString(
            $this->normalizeContent('* @method static void demoHelloWorld()'),
            $result,
        );
    }
}
