<?php

declare(strict_types=1);

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

namespace Leevel\Mail;

/**
 * IMail 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 */
interface IMail
{
    /**
     * 设置邮件发送来源.
     *
     * @param string      $strAddress
     * @param null|string $mixName
     *
     * @return $this
     */
    public function globalFrom($strAddress, $mixName = null);

    /**
     * 设置邮件发送地址
     *
     * @param string      $strAddress
     * @param null|string $mixName
     *
     * @return $this
     */
    public function globalTo($strAddress, $mixName = null);

    /**
     * 视图 html 邮件内容.
     *
     * @param string $sFile
     * @param array  $arrData
     *
     * @return $this
     */
    public function view($sFile, array $arrData = []);

    /**
     * html 邮件内容.
     *
     * @param string $strContent
     *
     * @return $this
     */
    public function html($strContent);

    /**
     * 纯文本邮件内容.
     *
     * @param string $strContent
     *
     * @return $this
     */
    public function plain($strContent);

    /**
     * 视图纯文本邮件内容.
     *
     * @param string $sFile
     * @param array  $arrData
     *
     * @return $this
     */
    public function viewPlain($sFile, array $arrData = []);

    /**
     * 消息回调处理.
     *
     * @param callable|string $mixCallback
     *
     * @return $this
     */
    public function message($mixCallback);

    /**
     * 添加附件.
     *
     * @param string        $strFile
     * @param null|callable $mixCallback
     *
     * @return $this
     */
    public function attach($strFile, $mixCallback = null);

    /**
     * 添加内存内容附件
     * file_get_content( path ).
     *
     * @param string        $strData
     * @param string        $strName
     * @param null|callable $mixCallback
     *
     * @return $this
     */
    public function attachData($strData, $strName, $mixCallback = null);

    /**
     * 图片嵌入邮件.
     *
     * @param string $file
     * @param mixed  $strFile
     *
     * @return string
     */
    public function attachView($strFile);

    /**
     * 内存内容图片嵌入邮件.
     *
     * @param string      $strData
     * @param string      $strName
     * @param null|string $contentType
     * @param null|mixed  $strContentType
     *
     * @return string
     */
    public function attachDataView($strData, $strName, $strContentType = null);

    /**
     * 格式化中文附件名字.
     *
     * @param string $strFile
     *
     * @return string
     */
    public function attachChinese($strFile);

    /**
     * 发送邮件.
     *
     * @param callable|string $mixCallback
     * @param bool            $booHtmlPriority
     *
     * @return int
     */
    public function send($mixCallback = null, $booHtmlPriority = true);

    /**
     * 错误消息.
     *
     * @return array
     */
    public function failedRecipients();
}
