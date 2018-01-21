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
namespace Queryyetsimple\Mvc;

use RuntimeException;

/**
 * 模型未找到异常
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.10
 * @version 1.0
 */
class ModelNotFound extends RuntimeException
{

    /**
     * 模型名字
     *
     * @var string
     */
    protected $strModel;

    /**
     * 设置模型
     *
     * @param string $strModel
     * @return $this
     */
    public function model($strModel)
    {
        $this->strModel = $strModel;
        $this->message = "Can not find {$strModel} data";
        return $this;
    }

    /**
     * 取回模型
     *
     * @return string
     */
    public function getModel()
    {
        return $this->strModel;
    }
}
