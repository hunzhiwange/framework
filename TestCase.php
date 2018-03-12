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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Tests;

use PHPUnit_Framework_TestCase;

/**
 * phpunit 测试用例
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.09
 * @version 1.0
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{

    /**
     * setUpBeforeClass
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
    }

    /**
     * tearDownAfterClass
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
    }

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp()
    {
    }

    /**
     * tearDown
     *
     * @return void
     */
    protected function tearDown()
    {
    }
}
