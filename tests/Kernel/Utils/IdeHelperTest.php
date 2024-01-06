<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils;

use Leevel\Kernel\Utils\IdeHelper as UtilsIdeHelper;
use Tests\Kernel\Utils\Assert\DemoClass;
use Tests\Kernel\Utils\Assert\DemoClass2;
use Tests\Kernel\Utils\Assert\DemoClass3;
use Tests\Kernel\Utils\Assert\DemoClass4;
use Tests\Kernel\Utils\Assert\Helper\Demo1;
use Tests\Kernel\Utils\Assert\Helper\Demo2;
use Tests\Kernel\Utils\Assert\Helper\Demo3;
use Tests\Kernel\Utils\Assert\Helper\Demo4;
use Tests\Kernel\Utils\Assert\Helper\DemoHelloWorld;
use Tests\TestCase;

final class IdeHelperTest extends TestCase
{
    public function testBaseUse(): void
    {
        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handle(DemoClass::class);
        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo1()'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo2(string $hello, int $world)'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static string demo3(string $hello, ?int $world = null) demo3'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo4(...$hello)'),
            $result,
        );
    }

    public function testHandleClassFunction(): void
    {
        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handleClassFunction([
            Demo1::class,
            Demo2::class,
            Demo3::class,
            Demo4::class,
            DemoHelloWorld::class,
        ]);

        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo1()'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo2(string $hello, int $world)'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static string demo3(string $hello, ?int $world = null) demo3'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo4(...$hello)'),
            $result,
        );
        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demoHelloWorld()'),
            $result,
        );
    }

    public function test1(): void
    {
        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handle(DemoClass2::class);
        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('* @method static void demo1()'),
            $result,
        );
    }

    public function test2(): void
    {
        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handle(DemoClass3::class);
        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('* @method static mixed demo1()'),
            $result,
        );
    }

    public function test3(): void
    {
        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handle(DemoClass3::class);
        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('* @method static mixed demo1()'),
            $result,
        );
    }

    public function test4(): void
    {
        $ideHelper = new UtilsIdeHelper();
        $result = $ideHelper->handle(DemoClass4::class);
        $result = $this->normalizeContent($result);

        static::assertStringContainsString(
            $this->normalizeContent('* @method static string|bool demo1(bool $isBool = false)'),
            $result,
        );

        static::assertStringContainsString(
            $this->normalizeContent('* @method static string|bool demo2(string|bool $isBool)'),
            $result,
        );

        static::assertStringContainsString(
            $this->normalizeContent('* @method static string demo3(\\Tests\\Kernel\\Utils\\Assert\\DemoClass3 $demoClass3)'),
            $result,
        );

        static::assertStringContainsString(
            $this->normalizeContent('* @method static string demo4(\\?Tests\\Kernel\\Utils\\Assert\\DemoClass3 $demoClass3 = null)'),
            $result,
        );
    }
}
