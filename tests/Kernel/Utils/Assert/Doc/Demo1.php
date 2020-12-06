<?php

declare(strict_types=1);

namespace Tests\Kernel\Utils\Assert\Doc;

/**
 * @api(
 *     zh-CN:title="demo1",
 *     path="demo1",
 *     zh-CN:description="
 * demo doc
 * just test
 * ",
 * )
 */
class Demo1
{
    /**
     * @api(
     *     zh-CN:title="title",
     *     zh-CN:description="
     * hello
     * world
     * ",
     *     zh-CN:note="",
     * )
     */
    public function test1(): void
    {
    }
}
