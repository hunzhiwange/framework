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
namespace queryyetsimple\seccode;

/**
 * iseccode 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
interface iseccode
{

    /**
     * 图像最大宽度
     *
     * @var int
     */
    const MAX_WIDTH = 9999;

    /**
     * 图像最大高度
     *
     * @var int
     */
    const MAX_HEIGHT = 9999;

    /**
     * 图像最小宽度
     *
     * @var int
     */
    const MIN_WIDTH = 1;

    /**
     * 图像最小高度
     *
     * @var int
     */
    const MIN_HEIGHT = 1;

    /**
     * 随机字母数字
     *
     * @var string
     */
    const ALPHA_NUM = 'alpha_num';

    /**
     * 随机小写字母数字
     *
     * @var string
     */
    const ALPHA_NUM_LOWERCASE = 'alpha_num_lowercase';

    /**
     * 随机大写字母数字
     *
     * @var string
     */
    const ALPHA_NUM_UPPERCASE = 'alpha_num_uppercase';

    /**
     * 随机字母
     *
     * @var string
     */
    const ALPHA = 'alpha';

    /**
     * 随机小写字母
     *
     * @var string
     */
    const ALPHA_LOWERCASE = 'alpha_lowercase';

    /**
     * 随机大写字母
     *
     * @var string
     */
    const ALPHA_UPPERCASE = 'alpha_uppercase';

    /**
     * 随机数字
     *
     * @var string
     */
    const NUM = 'num';

    /**
     * 随机字中文
     *
     * @var string
     */
    const CHINESE = 'chinese';

    /**
     * 设置验证码
     *
     * @param mixed $mixCode
     * @param string $strAutoType
     * @param boolean $booAutoCode
     * @return $this
     */
    public function display($mixCode = null, $strAutoType = self::ALPHA_UPPERCASE, $booAutoCode = true);

    /**
     * 设置验证码
     *
     * @param string $strCode
     * @return $this
     */
    public function code($strCode);

    /**
     * 返回验证码
     *
     * @return $this
     */
    public function getCode();

    /**
     * 返回宽度
     *
     * @return int
     */
    public function getWidth();

    /**
     * 返回高度
     *
     * @return int
     */
    public function getHeight();

    /**
     * 返回英文字体路径
     *
     * @return string
     */
    public function getFontPath();

    /**
     * 返回中文字体路径
     *
     * @return string
     */
    public function getChineseFontPath();

    /**
     * 返回背景图路径
     *
     * @return string
     */
    public function getBackgroundPath();
}
