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
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function option(string $name, $value)
    {
        $this->option[$name] = $value;
        return $this;
    }

    /**
     * 修改数组配置
     *
     * @param string $name
     * @param array $value
     * @return $this
     */
    public function optionArray(string $name, array $value)
    {
        return $this->option($name, array_merge($this->getOption($name), $value));
    }

    /**
     * 修改多个配置
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function options(array $option = [])
    {
        if (! $option) {
            return $this;
        }
        foreach ($option as $name => $value) {
            $this->option($name, $value);
        }
        return $this;
    }

    /**
     * 获取单个配置
     *
     * @param string $name
     * @param mixed $defaults
     * @return mixed
     */
    public function getOption(string $name, $defaults = null)
    {
        return $this->option[$name] ?? $defaults;
    }

    /**
     * 获取所有配置
     *
     * @param array $option
     * @return mixed
     */
    public function getOptions(array $option = [])
    {
        return $option ? array_merge($this->option, $option) : $this->option;
    }

    /**
     * 删除单个配置
     *
     * @param string $name
     * @return $this
     */
    public function deleteOption(string $name)
    {
        if(isset($this->option[$name])) {
            unset($this->option[$name]);
        }
        return $this;
    }

    /**
     * 删除多个配置
     *
     * @param array $option
     * @return $this
     */
    public function deleteOptions(array $option = [])
    {
        if (! $option) {
            return $this;
        }

        foreach ($option as $key) {
            $this->deleteOption($key);
        }
        
        return $this;
    }
}
