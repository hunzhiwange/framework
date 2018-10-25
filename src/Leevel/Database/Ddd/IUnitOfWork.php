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

namespace Leevel\Database\Ddd;

/**
 * 工作单元接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 *
 * @version 1.0
 */
interface IUnitOfWork
{
    /**
     * 已经被管理的实体状态.
     *
     * @var int
     */
    public const STATE_MANAGED = 1;

    /**
     * 尚未被管理的实体状态.
     *
     * @var int
     */
    public const STATE_NEW = 2;

    /**
     * 已经持久化并且脱落管理的实体状态.
     *
     * @var int
     */
    public const STATE_DETACHED = 3;

    /**
     * 被标识为删除的实体状态.
     *
     * @var int
     */
    public const STATE_REMOVED = 4;

    // /**
    //  * 启动事物.
    //  */
    // public function beginTransaction();
    //
    // /**
    //  * 事务回滚.
    //  */
    // public function rollback();
    //
    // /**
    //  * 事务自动提交.
    //  */
    // public function commit();
    //
    // /**
    //  * 执行数据库事务.
    //  *
    //  * @return mixed
    //  */
    // public function flush();
    //
    // /**
    //  * 是否已经提交事务
    //  *
    //  * @return bool
    //  */
    // public function committed();
    //
    // // /**
    // //  * 注册事务提交.
    // //  */
    // // public function registerCommit();
    //
    // /**
    //  * 注册新建.
    //  *
    //  * @param \Leevel\Database\Ddd\IEntity     $entity
    //  * @param \Leevel\Database\Ddd\IRepository $repository
    //  *
    //  * @return $this
    //  */
    // public function registerCreate(IEntity $entity, IRepository $repository);
    //
    // /**
    //  * 注册更新.
    //  *
    //  * @param \Leevel\Database\Ddd\IEntity     $entity
    //  * @param \Leevel\Database\Ddd\IRepository $repository
    //  *
    //  * @return $this
    //  */
    // public function registerUpdate(IEntity $entity, IRepository $repository);
    //
    // /**
    //  * 注册删除.
    //  *
    //  * @param \Leevel\Database\Ddd\IEntity     $entity
    //  * @param \Leevel\Database\Ddd\IRepository $repository
    //  *
    //  * @return $this
    //  */
    // public function registerDelete(IEntity $entity, IRepository $repository);
}
