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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
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
    public function setOption(string $name, $value): self;

    /**
     * 设置邮件发送来源.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return $this
     */
    public function globalFrom(string $address, ?string $name = null): self;

    /**
     * 设置邮件发送地址
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return $this
     */
    public function globalTo(string $address, ?string $name = null): self;

    /**
     * 视图 html 邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return $this
     */
    public function view(string $file, array $data = []): self;

    /**
     * html 邮件内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function html(string $content): self;

    /**
     * 纯文本邮件内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function plain(string $content): self;

    /**
     * 视图纯文本邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return $this
     */
    public function viewPlain(string $file, array $data = []): self;

    /**
     * 消息回调处理.
     *
     * @param \Closure $callbacks
     *
     * @return $this
     */
    public function message(Closure $callbacks): self;

    /**
     * 添加附件.
     *
     * @param string        $file
     * @param null|\Closure $callbacks
     *
     * @return $this
     */
    public function attach(string $file, Closure $callbacks = null): self;

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
    public function attachData(string $data, string $name, Closure $callbacks = null): self;

    /**
     * 图片嵌入邮件.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachView(string $file): string;

    /**
     * 内存内容图片嵌入邮件.
     *
     * @param string      $data
     * @param string      $name
     * @param null|string $contentType
     *
     * @return string
     */
    public function attachDataView(string $data, string $name, ?string $contentType = null): string;

    /**
     * 格式化中文附件名字.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachChinese(string $file): string;

    /**
     * 发送邮件.
     *
     * @param \Closure $callbacks
     * @param bool     $htmlPriority
     *
     * @return int
     */
    public function send(Closure $callbacks = null, bool $htmlPriority = true): int;

    /**
     * 错误消息.
     *
     * @return array
     */
    public function failedRecipients(): array;
}
