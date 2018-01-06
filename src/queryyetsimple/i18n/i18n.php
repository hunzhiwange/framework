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
namespace queryyetsimple\i18n;

use InvalidArgumentException;

/**
 * 国际化组件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.18
 * @version 1.0
 */
class i18n implements ii18n
{

    /**
     * 当前语言上下文
     *
     * @var string
     */
    protected $sI18n;

    /**
     * 默认语言上下文
     *
     * @var string
     */
    protected $sDefault = 'zh-cn';

    /**
     * 语言数据
     *
     * @var array
     */
    protected $arrText = [];

    /**
     * 构造函数
     *
     * @param string $sI18n
     * @return void
     */
    public function __construct(string $sI18n)
    {
        $this->sI18n = $sI18n;
    }

    /**
     * 获取语言 text
     *
     * @param array $arr
     * @return string
     */
    public function getText(...$arr)
    {
        if(count($arr) == 0) {
            return '';
        }

        $sValue = $arr[0];
        $sValue = $this->arrText[$this->getI18n()][$sValue] ?? $sValue;
        if (count($arr) > 1) {
            $arr[0] = $sValue;
            $sValue = sprintf(...$arr);
        }
        
        return $sValue;
    }

    /**
     * 获取语言 text
     *
     * @param array $arr
     * @return string
     */
    public function __(...$arr)
    {
        return $this->{'getText'}(...$arr);
    }

    /**
     * 添加语言包语句
     *
     * @param string $sI18nName 语言名字
     * @param array $arrData 语言包数据
     * @return void
     */
    public function addText(string $sI18n, array $arrData = [])
    {
        if (array_key_exists($sI18n, $this->arrText)) {
            $this->arrText[$sI18n] = array_merge($this->arrText[$sI18n], $arrData);
        } else {
            $this->arrText[$sI18n] = $arrData;
        }
    }

    /**
     * 设置当前语言包
     *
     * @param string $sI18n
     * @return void
     */
    public function setI18n(string $sI18n)
    {
        $this->sI18n = $sI18n;
    }

    /**
     * 获取当前语言包
     *
     * @return string
     */
    public function getI18n()
    {
        return $this->sI18n;
    }

    /**
     * 设置默认语言包
     *
     * @param string $sI18nName
     * @return void
     */
    public function setDefault(string $sI18n)
    {
        $this->sDefault = $sI18n;
    }

    /**
     * 获取默认语言包
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->sDefault;
    }
}
