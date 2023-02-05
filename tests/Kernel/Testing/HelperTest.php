<?php

declare(strict_types=1);

namespace Tests\Kernel\Testing;

use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class HelperTest extends TestCase
{
    public function testInvokeTestMethod(): void
    {
        static::assertSame('world1', $this->invokeTestMethod(new DemoObject(), 'hello1'));
        static::assertSame('world1arg1', $this->invokeTestMethod(new DemoObject(), 'hello1', ['arg1']));
    }

    public function testInvokeTestStaticMethod(): void
    {
        static::assertSame('world2', $this->invokeTestStaticMethod(DemoObject::class, 'hello2'));
        static::assertSame('world2arg1', $this->invokeTestStaticMethod(DemoObject::class, 'hello2', ['arg1']));
    }

    public function testGetTestProperty(): void
    {
        static::assertSame('hello', $this->getTestProperty(new DemoObject(), 'prop1'));
        static::assertSame('world', $this->getTestProperty(DemoObject::class, 'prop2'));
    }

    public function testSetTestProperty(): void
    {
        static::assertSame('hello', $this->getTestProperty($demoObject = new DemoObject(), 'prop1'));
        static::assertSame('world', $this->getTestProperty(DemoObject::class, 'prop2'));
        $this->setTestProperty($demoObject, 'prop1', 'hellonew');
        $this->setTestProperty(DemoObject::class, 'prop2', 'worldnew');
        static::assertSame('hellonew', $this->getTestProperty($demoObject, 'prop1'));
        static::assertSame('worldnew', $this->getTestProperty(DemoObject::class, 'prop2'));
        $this->setTestProperty(DemoObject::class, 'prop2', 'world');
        static::assertSame('world', $this->getTestProperty(DemoObject::class, 'prop2'));
    }

    public function testNormalizeContent(): void
    {
        static::assertSame('helloworld', $this->normalizeContent('hello world'));
        static::assertSame('hellloworldhaha', $this->normalizeContent("helllo \t world".PHP_EOL.'haha'));
    }

    public function testVarJson(): void
    {
        $data = <<<'eot'
            {
                "hello": "world"
            }
            eot;
        static::assertSame($data, $this->varJson(['hello' => 'world']));
    }

    public function testAssertTimeRange(): void
    {
        $this->assertTimeRange('2020-03-24 10:05:50', '2020-03-24 10:05:50');
        $this->assertTimeRange('2020-03-24 10:05:50', '2020-03-24 10:05:49', '2020-03-24 10:05:50', '2020-03-24 10:05:51');
    }

    public function testAssert(): void
    {
        $this->assert(true);
    }

    public function testObGetContents(): void
    {
        static::assertSame('hello', $this->obGetContents(function (): void {
            echo 'hello';
        }));
    }
}

class DemoObject
{
    private string $prop1 = 'hello';

    private static string $prop2 = 'world';

    private function hello1(string $extend = ''): string
    {
        return 'world1'.$extend;
    }

    private static function hello2(string $extend = ''): string
    {
        return 'world2'.$extend;
    }
}
