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
    protected $i18n;

    /**
     * 语言数据
     *
     * @var array
     */
    protected $text = [];

    /**
     * 构造函数
     *
     * @param string $i18n
     * @return void
     */
    public function __construct(string $i18n)
    {
        $this->i18n = $i18n;
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

        $value = $arr[0];
        $value = $this->text[$this->getI18n()][$value] ?? $value;
        if (count($arr) > 1) {
            $arr[0] = $value;
            $value = sprintf(...$arr);
        }
        
        return $value;
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
     * @param string $i18nName 语言名字
     * @param array $data 语言包数据
     * @return void
     */
    public function addText(string $i18n, array $data = [])
    {
        if (array_key_exists($i18n, $this->text)) {
            $this->text[$i18n] = array_merge($this->text[$i18n], $data);
        } else {
            $this->text[$i18n] = $data;
        }
    }

    /**
     * 设置当前语言包
     *
     * @param string $i18n
     * @return void
     */
    public function setI18n(string $i18n)
    {
        $this->i18n = $i18n;
    }

    /**
     * 获取当前语言包
     *
     * @return string
     */
    public function getI18n()
    {
        return $this->i18n;
    }
}
