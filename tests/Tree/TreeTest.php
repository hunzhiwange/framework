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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
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
 */
class TreeTest extends TestCase
{
    public function testBaseUse()
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 2,
        'data' => 'world',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        // 尾部插入节点
        $tree->setNode(5, 1, 'foo');

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 2,
        'data' => 'world',
      ),
      1 => 
      array (
        'value' => 5,
        'data' => 'foo',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        // 头部插入节点
        $tree->setNode(6, 1, 'bar', true);

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 6,
        'data' => 'bar',
      ),
      1 => 
      array (
        'value' => 2,
        'data' => 'world',
      ),
      2 => 
      array (
        'value' => 5,
        'data' => 'foo',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        // 子节点测试
        $tree->setNode(8, 6, 'subbar');

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 6,
        'data' => 'bar',
        'children' => 
        array (
          0 => 
          array (
            'value' => 8,
            'data' => 'subbar',
          ),
        ),
      ),
      1 => 
      array (
        'value' => 2,
        'data' => 'world',
      ),
      2 => 
      array (
        'value' => 5,
        'data' => 'foo',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        $json = <<<'eot'
[{"value":1,"data":"hello","children":[{"value":6,"data":"bar","children":[{"value":8,"data":"subbar"}]},{"value":2,"data":"world"},{"value":5,"data":"foo"}]}]
eot;

        $this->assertSame(
            $json,
            $tree->toJson()
        );
    }

    public function testChildrenUse()
    {
        $tree = $this->providerTree();

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 2,
        'data' => 'world',
      ),
      1 => 
      array (
        'value' => 3,
        'data' => 'foo',
        'children' => 
        array (
          0 => 
          array (
            'value' => 5,
            'data' => 'subfoo',
            'children' => 
            array (
              0 => 
              array (
                'value' => 6,
                'data' => 'subsubfoo',
              ),
            ),
          ),
        ),
      ),
      2 => 
      array (
        'value' => 4,
        'data' => 'bar',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 5,
    'data' => 'subfoo',
    'children' => 
    array (
      0 => 
      array (
        'value' => 6,
        'data' => 'subsubfoo',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getChildrenTree(3)
            )
        );

        $nodes = <<<'eot'
array (
  1 => 1,
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getChild(0)
            )
        );

        $nodes = <<<'eot'
array (
  0 => 5,
  1 => 6,
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getChildren(3)
            )
        );

        $this->assertTrue(
            $tree->hasChild(3)
        );

        $this->assertFalse(
            $tree->hasChild(6)
        );

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

    public function testParentsUse()
    {
        $tree = $this->providerTree();

        $nodes = <<<'eot'
array (
  0 => 1,
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getParent(3)
            )
        );

        $nodes = <<<'eot'
array (
  0 => 3,
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getParent(5)
            )
        );

        $nodes = <<<'eot'
array (
  0 => 3,
  1 => 5,
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getParent(5, true)
            )
        );

        $nodes = <<<'eot'
array (
  0 => 1,
  1 => 3,
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getParents(5)
            )
        );

        $nodes = <<<'eot'
array (
  0 => 1,
  1 => 3,
  2 => 5,
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->getParents(5, true)
            )
        );

        // 不存对应的节点查询父节点
        $this->assertSame(
            [],
            $tree->getParent(400000000)
        );
    }

    public function testGetLevel()
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

    public function testGetSetData()
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 2,
        'data' => 'world',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        $this->assertSame(
            'world',
            $tree->getData(2)
        );

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 2,
        'data' => 'world => foo',
      ),
    ),
  ),
)
eot;

        $tree->setData(2, 'world => foo');

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        $this->assertSame(
            'world => foo',
            $tree->getData(2)
        );
    }

    public function testNormalize()
    {
        $tree = new Tree([
            [1, 0, 'hello'],
            [2, 1, 'world'],
        ]);

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 2,
        'data' => 'world',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->toArray()
            )
        );

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value1' => 1,
    'data1' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value1' => 2,
        'data1' => 'world',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->normalize(null, [
                    'value' => 'value1',
                    'data'  => 'data1',
                ])
            )
        );

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 1,
    'data' => 'hello',
    'label' => 'hello',
    'children' => 
    array (
      0 => 
      array (
        'value' => 2,
        'data' => 'world',
        'label' => 'world',
      ),
    ),
  ),
)
eot;

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->normalize(function ($item) {
                    $item['label'] = $item['data'];

                    return $item;
                })
            )
        );

        $nodes = <<<'eot'
array (
  0 => 
  array (
    'value' => 5,
    'data' => 'subfoo',
    'children' => 
    array (
      0 => 
      array (
        'value' => 6,
        'data' => 'subsubfoo',
      ),
    ),
  ),
)
eot;

        // 可以返回子树
        $tree = $this->providerTree();

        $this->assertSame(
            $nodes,
            $this->varExport(
                $tree->normalize(null, [], 3)
            )
        );
    }

    public function testNodeWithException()
    {
        $this->expectException(\RuntimeException::class);

        $tree = new Tree([
            1,
        ]);
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
