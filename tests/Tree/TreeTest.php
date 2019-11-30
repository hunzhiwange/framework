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

namespace Tests\Tree;

use Leevel\Tree\Tree;
use Tests\TestCase;

/**
 * tree test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.06.21
 *
 * @version 1.0
 *
 * @api(
 *     title="树 tree",
 *     path="component/tree",
 *     description="树组件 `tree` 提供了一些实用方法，用于整理数据为一棵树，并提供一些方法来获取树相关节点的信息。",
 * )
 */
class TreeTest extends TestCase
{
    /**
     * @api(
     *     title="Tree 基本使用",
     *     description="将子父节点整理为树结构。",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );
    }

    /**
     * @api(
     *     title="Tree.toJson 树结构输出为 JSON 格式字符串",
     *     description="",
     *     note="",
     * )
     */
    public function testToJson(): void
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        $nodes = <<<'eot'
            [{"value":1,"data":"hello","children":[{"value":2,"data":"world"}]}]
            eot;

        $this->assertSame(
            $nodes,
            $tree->toJson(),
        );
    }

    /**
     * @api(
     *     title="Tree.setNode 设置节点",
     *     description="",
     *     note="",
     * )
     */
    public function testSetNode(): void
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        // 尾部插入节点
        $tree->setNode(5, 1, 'foo');

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world"
                        },
                        {
                            "value": 5,
                            "data": "foo"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );
    }

    /**
     * @api(
     *     title="Tree.setNode 在头部设置节点",
     *     description="",
     *     note="",
     * )
     */
    public function testSetNodeAtHeader(): void
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        // 头部插入节点
        $tree->setNode(6, 1, 'bar', true);

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 6,
                            "data": "bar"
                        },
                        {
                            "value": 2,
                            "data": "world"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );
    }

    /**
     * @api(
     *     title="Tree.setNode 设置子节点",
     *     description="",
     *     note="",
     * )
     */
    public function testSetNodeAsChildren(): void
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        // 子节点测试
        $tree->setNode(8, 2, 'subbar');

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world",
                            "children": [
                                {
                                    "value": 8,
                                    "data": "subbar"
                                }
                            ]
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );

        $json = <<<'eot'
            [{"value":1,"data":"hello","children":[{"value":2,"data":"world","children":[{"value":8,"data":"subbar"}]}]}]
            eot;

        $this->assertSame(
            $json,
            $tree->toJson()
        );
    }

    /**
     * @api(
     *     title="Tree.getChildrenTree 获取节点子树",
     *     description="
     *
     * `测试树数据`
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Tree\TreeTest::class, 'providerTree')]}
     * ```
     *
     * ::: warning
     * 后面的测试，也会用到这个测试树数据。
     * :::
     * ",
     *     note="",
     * )
     */
    public function testGetChildrenTree(): void
    {
        $tree = $this->providerTree();

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world"
                        },
                        {
                            "value": 3,
                            "data": "foo",
                            "children": [
                                {
                                    "value": 5,
                                    "data": "subfoo",
                                    "children": [
                                        {
                                            "value": 6,
                                            "data": "subsubfoo"
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "value": 4,
                            "data": "bar"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );

        $nodes = <<<'eot'
            [
                {
                    "value": 5,
                    "data": "subfoo",
                    "children": [
                        {
                            "value": 6,
                            "data": "subsubfoo"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getChildrenTree(3)
            )
        );
    }

    /**
     * @api(
     *     title="Tree.getChild 获取一级子节点 ID",
     *     description="",
     *     note="",
     * )
     */
    public function testGetChild(): void
    {
        $tree = $this->providerTree();

        $nodes = <<<'eot'
            {
                "1": 1
            }
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getChild(0)
            )
        );
    }

    /**
     * @api(
     *     title="Tree.getChildren 获取所有子节点 ID",
     *     description="",
     *     note="",
     * )
     */
    public function testGetChildren(): void
    {
        $tree = $this->providerTree();

        $nodes = <<<'eot'
            [
                5,
                6
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getChildren(3)
            )
        );
    }

    /**
     * @api(
     *     title="Tree.hasChild 是否存在一级子节点 ID",
     *     description="",
     *     note="",
     * )
     */
    public function testHasChild(): void
    {
        $tree = $this->providerTree();

        $this->assertTrue(
            $tree->hasChild(3)
        );

        $this->assertFalse(
            $tree->hasChild(6)
        );
    }

    /**
     * @api(
     *     title="Tree.hasChildren 是否存在子节点 ID",
     *     description="",
     *     note="",
     * )
     */
    public function testHasChildren(): void
    {
        $tree = $this->providerTree();

        // hasChildren 存在严格和不严格校验
        $this->assertFalse(
            $tree->hasChildren(3, [5, 100000])
        );

        $this->assertTrue(
            $tree->hasChildren(3, [5, 100000], false)
        );

        // 第二个元素为空
        $this->assertFalse(
            $tree->hasChildren(3, [], false)
        );

        $this->assertFalse(
            $tree->hasChildren(1, [], true)
        );

        // 非严格模式不存在
        $this->assertFalse(
            $tree->hasChildren(100000, [2, 3], false)
        );
    }

    /**
     * @api(
     *     title="Tree.getParent 获取一级父节点 ID",
     *     description="",
     *     note="",
     * )
     */
    public function testGetParent(): void
    {
        $tree = $this->providerTree();

        $nodes = <<<'eot'
            [
                1
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getParent(3)
            )
        );

        $nodes = <<<'eot'
            [
                3
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getParent(5)
            )
        );

        $nodes = <<<'eot'
            [
                3,
                5
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getParent(5, true)
            )
        );
    }

    /**
     * @api(
     *     title="Tree.getParent 不存在的节点父级 ID 为空数组",
     *     description="",
     *     note="",
     * )
     */
    public function testGetParentButNodeNotFound(): void
    {
        $tree = $this->providerTree();

        // 不存对应的节点查询父节点
        $this->assertSame(
            [],
            $tree->getParent(400000000)
        );
    }

    /**
     * @api(
     *     title="Tree.getParents 获取所有父节点 ID",
     *     description="",
     *     note="",
     * )
     */
    public function testGetParents(): void
    {
        $tree = $this->providerTree();

        $nodes = <<<'eot'
            [
                1,
                3
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getParents(5)
            )
        );

        $nodes = <<<'eot'
            [
                1,
                3,
                5
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->getParents(5, true)
            )
        );
    }

    /**
     * @api(
     *     title="Tree.getLevel 获取节点所在的层级",
     *     description="",
     *     note="",
     * )
     */
    public function testGetLevel(): void
    {
        $tree = $this->providerTree();

        $this->assertSame(
            1,
            $tree->getLevel(3)
        );

        $this->assertSame(
            3,
            $tree->getLevel(6)
        );

        $this->assertSame(
            0,
            $tree->getLevel(0)
        );

        $this->assertSame(
            0,
            $tree->getLevel(1)
        );

        $this->assertSame(
            0,
            $tree->getLevel(100000)
        );
    }

    /**
     * @api(
     *     title="Tree.getData.setData 设置和获取节点数据",
     *     description="",
     *     note="",
     * )
     */
    public function testGetSetData(): void
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );

        $this->assertSame(
            'world',
            $tree->getData(2)
        );

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world => foo"
                        }
                    ]
                }
            ]
            eot;

        $tree->setData(2, 'world => foo');

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );

        $this->assertSame(
            'world => foo',
            $tree->getData(2)
        );
    }

    /**
     * @api(
     *     title="Tree.normalize 返回整理树节点数据结构",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalize(): void
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );

        $nodes = <<<'eot'
            [
                {
                    "value1": 1,
                    "data1": "hello",
                    "children": [
                        {
                            "value1": 2,
                            "data1": "world"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->normalize(null, [
                    'value' => 'value1',
                    'data'  => 'data1',
                ])
            )
        );

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "label": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world",
                            "label": "world"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->normalize(function ($item) {
                    $item['label'] = $item['data'];

                    return $item;
                })
            )
        );
    }

    /**
     * @api(
     *     title="Tree.normalize 返回整理树某个节点及下级的数据结构",
     *     description="",
     *     note="",
     * )
     */
    public function testNormalizeNode(): void
    {
        // 可以返回子树
        $tree = $this->providerTree();

        $nodes = <<<'eot'
            [
                {
                    "value": 5,
                    "data": "subfoo",
                    "children": [
                        {
                            "value": 6,
                            "data": "subsubfoo"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->normalize(null, [], 3)
            )
        );
    }

    public function testNodeWithException(): void
    {
        $this->expectException(\RuntimeException::class);

        new Tree([
            1,
        ]);
    }

    public function testButNotIsStringInt(): void
    {
        $tree = new Tree([
            ['1', '0', 'hello'],
            ['2', '1', 'world'],
        ]);

        $nodes = <<<'eot'
            [
                {
                    "value": 1,
                    "data": "hello",
                    "children": [
                        {
                            "value": 2,
                            "data": "world"
                        }
                    ]
                }
            ]
            eot;

        $this->assertSame(
            $nodes,
            $this->varJson(
                $tree->toArray()
            )
        );
    }

    protected function providerTree(): Tree
    {
        return new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
            [3, 1, 'foo'],
            [4, 1, 'bar'],
            [5, 3, 'subfoo'],
            [6, 5, 'subsubfoo'],
        ]);
    }
}
