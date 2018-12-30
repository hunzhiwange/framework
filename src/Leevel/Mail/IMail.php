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

use Closure;

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
     * 邮件事件.
     *
     * @var string
     */
    const MAIL_EVENT = 'mail.mail';

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value);

    /**
     * 设置邮件发送来源.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return $this
     */
    public function globalFrom($address, $name = null);

    /**
     * 设置邮件发送地址
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return $this
     */
    public function globalTo($address, $name = null);

    /**
     * 视图 html 邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return $this
     */
    public function view($file, array $data = []);

    /**
     * html 邮件内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function html($content);

    /**
     * 纯文本邮件内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function plain($content);

    /**
     * 视图纯文本邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return $this
     */
    public function viewPlain($file, array $data = []);

    /**
     * 消息回调处理.
     *
     * @param \Closure $callbacks
     *
     * @return $this
     */
    public function message(Closure $callbacks);

    /**
     * 添加附件.
     *
     * @param string        $file
     * @param null|\Closure $callbacks
     *
     * @return $this
     */
    public function attach($file, Closure $callbacks = null);

    /**
     * 添加内存内容附件
     * file_get_content(path).
     *
     * @param string        $data
     * @param string        $name
     * @param null|\Closure $callbacks
     *
     * @return $this
     */
    public function attachData($data, $name, Closure $callbacks = null);

    /**
     * 图片嵌入邮件.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachView($file);

    /**
     * 内存内容图片嵌入邮件.
     *
     * @param string      $data
     * @param string      $name
     * @param null|string $contentType
     *
     * @return string
     */
    public function attachDataView($data, $name, $contentType = null);

    /**
     * 格式化中文附件名字.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachChinese($file);

    /**
     * 发送邮件.
     *
     * @param \Closure $callbacks
     * @param bool     $htmlPriority
     *
     * @return int
     */
    public function send(Closure $callbacks = null, bool $htmlPriority = true);

    /**
     * 错误消息.
     *
     * @return array
     */
    public function failedRecipients();
}
