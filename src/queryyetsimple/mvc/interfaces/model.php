<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc\interfaces;

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
 * model 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.10
 * @version 1.0
 */
interface model {
    
    /**
     * 批量查找前事件
     *
     * @var string
     */
    const BEFORE_SELECT_EVENT = 'selecting';
    
    /**
     * 批量查找后事件
     *
     * @var string
     */
    const AFTER_SELECT_EVENT = 'selected';
    
    /**
     * 查找前事件
     *
     * @var string
     */
    const BEFORE_FIND_EVENT = 'finding';
    
    /**
     * 查找后事件
     *
     * @var string
     */
    const AFTER_FIND_EVENT = 'finded';
    
    /**
     * 保存前事件
     *
     * @var string
     */
    const BEFORE_SAVE_EVENT = 'saveing';
    
    /**
     * 保存后事件
     *
     * @var string
     */
    const AFTER_SAVE_EVENT = 'saved';
    
    /**
     * 新建前事件
     *
     * @var string
     */
    const BEFORE_CREATE_EVENT = 'creating';
    
    /**
     * 新建后事件
     *
     * @var string
     */
    const AFTER_CREATE_EVENT = 'created';
    
    /**
     * 更新前事件
     *
     * @var string
     */
    const BEFORE_UPDATE_EVENT = 'updating';
    
    /**
     * 更新后事件
     *
     * @var string
     */
    const AFTER_UPDATE_EVENT = 'updated';
    
    /**
     * 删除前事件
     *
     * @var string
     */
    const BEFORE_DELETE_EVENT = 'deleting';
    
    /**
     * 删除后事件
     *
     * @var string
     */
    const AFTER_DELETE_EVENT = 'deleted';
    
    /**
     * 软删除前事件
     *
     * @var string
     */
    const BEFORE_SOFT_DELETE_EVENT = 'softDeleting';
    
    /**
     * 软删除后事件
     *
     * @var string
     */
    const AFTER_SOFT_DELETE_EVENT = 'softDeleted';
    
    /**
     * 软删除恢复前事件
     *
     * @var string
     */
    const BEFORE_SOFT_RESTORE_EVENT = 'softRestoring';
    
    /**
     * 软删除恢复后事件
     *
     * @var string
     */
    const AFTER_SOFT_RESTORE_EVENT = 'softRestored';
    
    /**
     * 新建时间字段
     *
     * @var string
     */
    const CREATED_AT = 'created_at';
    
    /**
     * 更新时间字段
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';
}
