<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace tests\database;

use tests\testcase;
use queryyetsimple\database;

/**
 * database.select 组件测试
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.04
 * @version 1.0
 */
class Select_test extends testcase
{

    /**
     * 测试表
     *
     * @var string
     */
    private static $strTable = 'test';

    /**
     * 初始化
     *
     * @return void
     */
    protected function setUp()
    {
    }

    /**
     * table 查询
     *
     * @see https://github.com/hunzhiwange/document/blob/master/database/database-constructor-table.md
     * @return void
     */
    public function testTable()
    {
        $this->assertEquals(database::table('test')->limit(2)->getAll(true), [
            'SELECT `test`.* FROM `test` LIMIT 0,2',
            [],
            false,
            5,
            null,
            []
        ]);

        $this->assertEquals(database::table('test as t')->limit(2)->getAll(true), [
            'SELECT `t`.* FROM `test`  `t` LIMIT 0,2',
            [],
            false,
            5,
            null,
            []
        ]);

        $this->assertEquals(database::table([
            't2' => 'test'
        ])->limit(2)->getAll(true), [
            'SELECT `t2`.* FROM `test`  `t2` LIMIT 0,2',
            [],
            false,
            5,
            null,
                [ ]
        ]);
    }
}
