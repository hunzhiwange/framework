<?php

declare(strict_types=1);

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
    
    public function testHandleClassFunction(): void
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
