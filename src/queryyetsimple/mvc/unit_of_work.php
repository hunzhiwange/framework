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
 * 工作单元
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.10.14
 * @version 1.0
 */
class unit_of_work implements iunit_of_work
{

    /**
     * 基础仓储
     *
     * @var \queryyetsimple\mvc\irepository
     */
    protected $objRepository;

    /**
     * 是否提交事务
     *
     * @var boolean
     */
    protected $booCommitted = false;

    /**
     * 新建对象
     *
     * @var array
     */
    protected $arrCreates = [ ];

    /**
     * 更新对象
     *
     * @var array
     */
    protected $arrUpdates = [ ];

    /**
     * 删除对象
     *
     * @var array
     */
    protected $arrDeletes = [ ];

    /**
     * 注册对象数量
     *
     * @var int
     */
    protected $intCount = 0;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\mvc\irepository $objRepository
     * @return $this
     */
    public function __construct(irepository $objRepository)
    {
        $this->objRepository = $objRepository;
    }

    /**
     * 启动事物
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->objRepository->beginTransaction();
        $this->booCommitted = false;
    }

    /**
     * 事务回滚
     *
     * @return void
     */
    public function rollback()
    {
        $this->objRepository->rollback();
        $this->booCommitted = false;
    }

    /**
     * 事务自动提交
     *
     * @return void
     */
    public function commit()
    {
        if ($this->booCommitted) {
            return;
        }
        $this->objRepository->commit();
        $this->booCommitted = true;
    }

    /**
     * 事务回滚
     *
     * @param callable $calAction
     * @return mixed
     */
    public function transaction($calAction)
    {
        if ($this->booCommitted) {
            return;
        }
        $this->booCommitted = true;
        return $this->objRepository->transaction($calAction);
    }

    /**
     * 是否已经提交事务
     *
     * @return boolean
     */
    public function committed()
    {
        return $this->booCommitted;
    }

    /**
     * 注册事务提交
     *
     * @return void
     */
    public function registerCommit()
    {
        if ($this->booCommitted && $this->intCount == 0) {
            return;
        }

        if ($this->intCount > 1) {
            $this->transaction(function () {
                $this->handleRepository();
            });
        } else {
            $this->handleRepository();
        }

        $this->booCommitted = true;
    }

    /**
     * 注册新建
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity
     * @param \queryyetsimple\mvc\irepository $objRepository
     * @return $this
     */
    public function registerCreate(iaggregate_root $objEntity, irepository $objRepository)
    {
        $strHash = spl_object_hash($objEntity);
        if (! isset($this->arrCreates [$strHash])) {
            $this->arrCreates [$strHash] = [
                    $objEntity,
                    $objRepository
            ];
            $this->intCount ++;
        }

        return $this;
    }

    /**
     * 注册更新
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity
     * @param \queryyetsimple\mvc\irepository $objRepository
     * @return $this
     */
    public function registerUpdate(iaggregate_root $objEntity, irepository $objRepository)
    {
        $strHash = spl_object_hash($objEntity);

        if (! isset($this->arrUpdates [$strHash])) {
            $this->arrUpdates [$strHash] = [
                    $objEntity,
                    $objRepository
            ];
            $this->intCount ++;
        }

        return $this;
    }

    /**
     * 注册删除
     *
     * @param \queryyetsimple\mvc\iaggregate_root $objEntity
     * @param \queryyetsimple\mvc\irepository $objRepository
     * @return $this
     */
    public function registerDelete(iaggregate_root $objEntity, irepository $objRepository)
    {
        $strHash = spl_object_hash($objEntity);
        if (! isset($this->arrDeletes [$strHash])) {
            $this->arrDeletes [$strHash] = [
                    $objEntity,
                    $objRepository
            ];
            $this->intCount ++;
        }

        return $this;
    }

    /**
     * 响应仓储
     *
     * @return void
     */
    protected function handleRepository()
    {
        foreach ($this->arrCreates as $arrCreate) {
            list($objEntity, $objRepository) = $arrCreate;
            $objRepository->handleCreate($objEntity);
        }

        foreach ($this->arrUpdates as $arrUpdate) {
            list($objEntity, $objRepository) = $arrUpdate;
            $objRepository->handleUpdate($objEntity);
        }

        foreach ($this->arrDeletes as $arrDelete) {
            list($objEntity, $objRepository) = $arrDelete;
            $objRepository->handleDelete($objEntity);
        }
    }
}
