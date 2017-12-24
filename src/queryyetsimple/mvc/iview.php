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
namespace queryyetsimple\mvc;

/**
 * iview 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface iview
{

    /**
     * 变量赋值
     *
     * @param mixed $mixName
     * @param mixed $mixValue
     * @return $this
     */
    public function assign($mixName, $mixValue = null);

    /**
     * 获取变量赋值
     *
     * @param string|null $sName
     * @return mixed
     */
    public function getAssign($sName = null);

    /**
     * 删除变量值
     *
     * @param mixed $mixName
     * @return $this
     */
    public function deleteAssign($mixName);

    /**
     * 清空变量值
     *
     * @return $this
     */
    public function clearAssign();

    /**
     * 加载视图文件
     *
     * @param string $sFile
     * @param array $arrOption
     * @sub string charset 编码
     * @sub string content_type 内容类型
     * @return string
     */
    public function display($sFile = null, array $arrOption = null);
}
