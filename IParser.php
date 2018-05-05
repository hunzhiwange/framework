<?php declare(strict_types=1);
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\View;

/**
 * iparser 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface iparser
{

    /**
     * 注册视图编译器
     *
     * @return $this
     */
    public function registerCompilers();

    /**
     * 注册视图分析器
     *
     * @return $this
     */
    public function registerParsers();

    /**
     * 执行编译
     *
     * @param string $sFile
     * @param string $sCachePath
     * @param boolean $bReturn
     * @return string
     */
    public function doCombile($sFile, $sCachePath, $bReturn = false);

    /**
     * code 编译编码，后还原
     *
     * @param string $sContent
     * @return string
     */
    public static function revertEncode($sContent);

    /**
     * tagself 编译编码，后还原
     *
     * @param string $sContent
     * @return string
     */
    public static function globalEncode($sContent);
}
