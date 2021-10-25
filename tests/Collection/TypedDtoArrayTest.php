<?php

declare(strict_types=1);

namespace Tests\Collection;

use Leevel\Collection\TypedDtoArray;
use Tests\Collection\DemoProject\Template;
use Tests\Collection\DemoProject\TemplateData;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="整数集合 collection",
 *     path="component/collection/typedint",
 *     zh-CN:description="",
 * )
 */
class TypedDtoArrayTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本使用",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Collection\DemoProject\TemplateData::class)]}
     * ```
     * 
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Collection\DemoProject\Template::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testBaseUse(): void
    {
        $templateData = TypedDtoArray::fromRequest($sourceTemplateData = [
            [
                'title' => 'hello',
                'tag' => 'world',
                'description' => 'foo',
            ],
            [
                'title' => 'hello1',
                'tag' => 'world1',
                'description' => 'foo1',
            ],
        ], TemplateData::class);
        $data = [
            'key' => 'hello',
            'title' => 'world',
            'data' => $templateData,
        ];

        $collection = new Template($data);
        $this->assertSame($collection['key'], 'hello');
        $this->assertSame($collection['title'], 'world');
        $this->assertInstanceOf(TypedDtoArray::class, $collection['data']);
        $this->assertSame($collection['data'], $templateData);
        $this->assertSame($collection['data']->toArray(), $sourceTemplateData);
    }

    public function testError(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('The value of a collection value type requires the following types `Leevel\\Support\\Dto`');

        $data = [
            1, 'string', 3, 4,
        ];

        new TypedDtoArray($data);
    }
}
