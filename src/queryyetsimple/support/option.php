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
    public function option(string $strName, $mixValue)
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
    public function optionArray(string $strName, array $arrValue)
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
    public function options(array $arrOption = [])
    {
        if (! $arrOption) {
            return $this;
        }
        foreach ($arrOption as $strName => $mixValue) {
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
    public function getOption(string $strName, $mixDefault = null)
    {
        return $this->arrOption[$strName] ?? $mixDefault;
    }

    /**
     * 获取所有配置
     *
     * @param array $arrOption
     * @return mixed
     */
    public function getOptions(array $arrOption = [])
    {
        return $arrOption ? array_merge($this->arrOption, $arrOption) : $this->arrOption;
    }

    /**
     * 删除单个配置
     *
     * @param string $strName
     * @return $this
     */
    public function deleteOption(string $strName)
    {
        if (! is_string($strName)) {
            throw new InvalidArgumentException('Option set name must be a string.');
        }
        if(isset($this->arrOption[$strName])) {
            unset($this->arrOption[$strName]);
        }
        return $this;
    }

    /**
     * 删除多个配置
     *
     * @param array $arrOption
     * @return $this
     */
    public function deleteOptions(array $arrOption = [])
    {
        if (! $arrOption) {
            return $this;
        }
        foreach ($arrOption as $strOption) {
            $this->deleteOption($strOption);
        }
        return $this;
    }
}
