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
namespace queryyetsimple\support;

/**
 * itree 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.13
 * @version 1.0
 */
interface itree
{

    /**
     * 设置节点数据
     *
     * @param int $nId
     * @param int $nParent
     * @param mixed $mixValue
     * @return void
     */
    public function setNode($nId, $nParent, $mixValue);

    /**
     * 取得给定 ID 子树
     *
     * @param int $nId
     * @return array
     */
    public function getChildrenTree($nId = 0);

    /**
     * 取得给定 ID 一级子树 ID
     *
     * @param int $nId
     * @return array
     */
    public function getChild($nId);

    /**
     * 取得给定 ID 所有子树 ID
     *
     * @param int $nId
     * @return array
     */
    public function getChildren($nId = 0);

    /**
     * 取得给定 ID 是否包含子树
     *
     * @param int $nId
     * @return boolean
     */
    public function hasChild($nId);

    /**
     * 验证是否存在子菜单
     *
     * @param int $intId
     * @param array $arrCheckChildren
     * @param boolean $booStrict
     * @return boolean
     */
    public function hasChildren($intId, array $arrCheckChildren = [], $booStrict = true);

    /**
     * 取得给定 ID 上级父级 ID
     *
     * @param int $nId
     * @param boolean $booWithItSelf
     * @return array
     */
    public function getParent($nId, $booWithItSelf = false);

    /**
     * 取得给定 ID 所有父级 ID
     *
     * @param int $nId
     * @param boolean $booWithItSelf
     * @return array
     */
    public function getParents($nId, $booWithItSelf = true);

    /**
     * 判断级别
     *
     * @param int $nId
     * @return string
     */
    public function getLevel($nId);

    /**
     * 取得节点的值
     *
     * @param int $nId
     * @return mixed
     */
    public function getData($nId, $mixDefault = null);

    /**
     * 设置节点的值
     *
     * @param int $nId
     * @param mixed $mixValue
     * @return void
     */
    public function setData($nId, $mixValue);

    /**
     * 树转化为数组
     *
     * @param mixed $mixCallable
     * @param array $arrKey
     * @param int $nId
     * @return array
     */
    public function treeToArray($mixCallable = null, $arrKey = [], $nId = 0);
}
