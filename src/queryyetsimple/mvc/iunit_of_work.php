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
namespace queryyetsimple\mvc;

/**
 * 工作单元接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.10.14
 * @version 1.0
 */
interface iunit_of_work
{

    /**
     * 启动事物
     *
     * @return void
     */
    public function beginTransaction();

    /**
     * 事务回滚
     *
     * @return void
     */
    public function rollback();

    /**
     * 事务自动提交
     *
     * @return void
     */
    public function commit();

    /**
     * 事务回滚
     *
     * @param callable $calAction
     * @return mixed
     */
    public function transaction($calAction);

    /**
     * 是否已经提交事务
     *
     * @return boolean
     */
    public function committed();

    /**
     * 注册事务提交
     *
     * @return void
     */
    public function registerCommit();

    /**
     * 注册新建
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity
     * @param \queryyetsimple\mvc\irepository $objRepository
     * @return $this
     */
    public function registerCreate(iaggregate_root $objEntity, irepository $objRepository);

    /**
     * 注册更新
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity
     * @param \queryyetsimple\mvc\irepository $objRepository
     * @return $this
     */
    public function registerUpdate(iaggregate_root $objEntity, irepository $objRepository);

    /**
     * 注册删除
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity
     * @param \queryyetsimple\mvc\irepository $objRepository
     * @return $this
     */
    public function registerDelete(iaggregate_root $objEntity, irepository $objRepository);
}
