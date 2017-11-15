<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace tests\option;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use tests\testcase;
use queryyetsimple\option;

/**
 * option 组件测试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.09
 * @version 1.0
 */
class Option_test extends testcase
{

    /**
     * 测试配置
     *
     * @var array
     */
    private static $arrTest = [
            'hello' => 'world',
            'router\name' => '小牛仔',
            'test\child' => [
                    'sub1' => 'hello',
                    'sub2' => 'world',
                    'sub3' => '新式软件',
                    'goods' => [
                            'world' => 'new'
                    ]
            ]
    ];

    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp()
    {
        option::reset();
        option::set(self::$arrTest);
    }

    /**
     * 读取生成的命名空间配置结构
     *
     * @return void
     */
    public function testGetsAll()
    {
        $this->assertTrue(option::all() === [
                'app' => [
                        'hello' => 'world'
                ],
                'router' => [
                        'name' => '小牛仔'
                ],
                'test' => [
                        'child' => [
                                'sub1' => 'hello',
                                'sub2' => 'world',
                                'sub3' => '新式软件',
                                'goods' => [
                                        'world' => 'new'
                                ]
                        ]
                ]
        ]);
    }

    /**
     * 读取生成的命名空间配置结构
     *
     * @return void
     */
    public function testGetsOneNamespace()
    {
        $this->assertTrue(option::get('router\\') === [
                'name' => '小牛仔'
        ]);
    }

    /**
     * 获取信息
     *
     * @return void
     */
    public function testGets()
    {
        $this->assertEquals('小牛仔', option::get('router\name'));
        $this->assertEquals('new', option::get('test\child.goods.world'));
        $this->assertEquals('default value', option::get('not_found', 'default value'));
    }

    /**
     * 设置信息
     *
     * @return void
     */
    public function testSets()
    {
        option::set('hello', '技术成就未来');
        $this->assertEquals('技术成就未来', option::get('hello'));
        option::set('router\child.sub2', '卧槽');
        $this->assertEquals('卧槽', option::get('router\child.sub2'));
    }
}
