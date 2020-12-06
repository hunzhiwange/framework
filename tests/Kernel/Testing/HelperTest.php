<?php

declare(strict_types=1);

namespace Tests\Kernel\Testing;

use Tests\TestCase;

class HelperTest extends TestCase
{
    public function testInvokeTestMethod(): void
    {
        $this->assertSame('world1', $this->invokeTestMethod(new DemoObject(), 'hello1'));
        $this->assertSame('world1arg1', $this->invokeTestMethod(new DemoObject(), 'hello1', ['arg1']));
    }

    public function testInvokeTestStaticMethod(): void
    {
        $this->assertSame('world2', $this->invokeTestStaticMethod(DemoObject::class, 'hello2'));
        $this->assertSame('world2arg1', $this->invokeTestStaticMethod(DemoObject::class, 'hello2', ['arg1']));
    }

    public function testGetTestProperty(): void
    {
        $this->assertSame('hello', $this->getTestProperty(new DemoObject(), 'prop1'));
        $this->assertSame('world', $this->getTestProperty(DemoObject::class, 'prop2'));
    }

    public function testSetTestProperty(): void
    {
        $this->assertSame('hello', $this->getTestProperty($demoObject = new DemoObject(), 'prop1'));
        $this->assertSame('world', $this->getTestProperty(DemoObject::class, 'prop2'));
        $this->setTestProperty($demoObject, 'prop1', 'hellonew');
        $this->setTestProperty(DemoObject::class, 'prop2', 'worldnew');
        $this->assertSame('hellonew', $this->getTestProperty($demoObject, 'prop1'));
        $this->assertSame('worldnew', $this->getTestProperty(DemoObject::class, 'prop2'));
        $this->setTestProperty(DemoObject::class, 'prop2', 'world');
        $this->assertSame('world', $this->getTestProperty(DemoObject::class, 'prop2'));
    }

    public function testNormalizeContent(): void
    {
        $this->assertSame('helloworld', $this->normalizeContent('hello world'));
        $this->assertSame('hellloworldhaha', $this->normalizeContent("helllo \t world".PHP_EOL.'haha'));
    }

    public function testVarJson(): void
    {
        $data = <<<'eot'
            {
                "hello": "world"
            }
            eot;
        $this->assertSame($data, $this->varJson(['hello' => 'world']));
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
        $this->assertSame('hello', $this->obGetContents(function (): void {
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
