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
namespace queryyetsimple\support;

use InvalidArgumentException;

/**
 * 类配置复用
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
trait option
{

    /**
     * 修改单个配置
     *
     * @param string $strName
     * @param mixed $mixValue
     * @return $this
     */
    public function option($strName, $mixValue)
    {
        if (! is_string($strName)) {
            throw new InvalidArgumentException('Option set name must be a string.');
        }
        $this->arrOption[$strName] = $mixValue;
        return $this;
    }

    /**
     * 修改单个数组性配置
     *
     * @param string $strName
     * @param array $arrValue
     * @return $this
     */
    public function optionArray($strName, array $arrValue)
    {
        return $this->option($strName, array_merge($this->getOption($strName), $arrValue));
    }

    /**
     * 修改多个配置
     *
     * @param string $strName
     * @param mixed $mixValue
     * @return $this
     */
    public function options($arrOption = [])
    {
        if (! $arrOption) {
            return $this;
        }
        foreach (( array ) $arrOption as $strName => $mixValue) {
            $this->option($strName, $mixValue);
        }
        return $this;
    }

    /**
     * 获取单个配置
     *
     * @param string $strName
     * @param mixed $mixDefault
     * @return mixed
     */
    public function getOption($strName, $mixDefault = null)
    {
        return isset($this->arrOption[$strName]) ? $this->arrOption[$strName] : $mixDefault;
    }

    /**
     * 获取所有配置
     *
     * @param array $arrOption
     * @return mixed
     */
    public function getOptions($arrOption = [])
    {
        return $arrOption ? array_merge($this->arrOption, $arrOption) : $this->arrOption;
    }
}
