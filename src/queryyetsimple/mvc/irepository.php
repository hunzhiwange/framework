<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc;

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

/**
 * 仓储基础接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.10.14
 * @version 1.0
 */
interface irepository {
    
    /**
     * 取得一条数据
     *
     * @param int $intId            
     * @param array $arrColumn            
     * @return \queryyetsimple\mvc\ientity
     */
    public function find($intId, $arrColumn = ['*']);
    
    /**
     * 取得一条数据，未找到记录抛出异常
     *
     * @param int $intId            
     * @param array $arrColumn            
     * @return \queryyetsimple\mvc\ientity|void
     */
    public function findOrFail($intId, $arrColumn = ['*']);
    
    /**
     * 取得所有记录
     *
     * @param null|callback $mixCallback            
     * @return \queryyetsimple\support\collection
     */
    public function count($mixSpecification = null);
    
    /**
     * 取得所有记录
     *
     * @param null|callback $mixCallback            
     * @return \queryyetsimple\support\collection
     */
    public function all($mixSpecification = null);
    
    /**
     * 保存数据
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return \queryyetsimple\mvc\iaggregate_root
     */
    public function create(iaggregate_root $objEntity);
    
    /**
     * 更新数据
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return \queryyetsimple\mvc\iaggregate_root
     */
    public function update(iaggregate_root $objEntity);
    
    /**
     * 删除数据
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return int
     */
    public function delete(iaggregate_root $objEntity);
    
    /**
     * 注册保存数据
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return \queryyetsimple\mvc\unit_of_work
     */
    public function registerCreate(iaggregate_root $objEntity);
    
    /**
     * 注册更新数据
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return \queryyetsimple\mvc\unit_of_work
     */
    public function registerUpdate(iaggregate_root $objEntity);
    
    /**
     * 注册删除数据
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return \queryyetsimple\mvc\unit_of_work
     */
    public function registerDelete(iaggregate_root $objEntity);
    
    /**
     * 响应新建
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return \queryyetsimple\mvc\iaggregate_root
     */
    public function handleCreate(iaggregate_root $objEntity);
    
    /**
     * 响应修改
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return \queryyetsimple\mvc\iaggregate_root
     */
    public function handleUpdate(iaggregate_root $objEntity);
    
    /**
     * 响应删除
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity            
     * @return int
     */
    public function handleDelete(iaggregate_root $objEntity);
    
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
     * 执行数据库事务
     *
     * @param callable $calAction            
     * @return mixed
     */
    public function transaction($calAction);
    
    /**
     * 设置聚合根
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objAggregate            
     * @return void
     */
    public function setAggregate(iaggregate_root $objAggregate);
    
    /**
     * 返回聚合根
     *
     * @return \queryyetsimple\mvc\iaggregate_root
     */
    public function aggregate();
    
    /**
     * 返回工作单元
     *
     * @return \queryyetsimple\mvc\iunit_of_work
     */
    public function unitOfWork();
    
    /**
     * 返回数据库仓储
     *
     * @return \queryyetsimple\database\idatabase
     */
    public function databaseConnect();
    
    /**
     * 注册事务提交
     *
     * @return void
     */
    public function registerCommit();
}
