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
use Swift_Events_EventListener;
use Swift_Mime_SimpleMessage;

/**
 * 代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.14
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait Proxy
{
    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Mail\IMail
     */
    public function setOption(string $name, $value): IMail
    {
        return $this->proxy()->setOption($name, $value);
    }

    /**
     * 设置邮件发送来源.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Leevel\Mail\IMail
     */
    public function globalFrom(string $address, ?string $name = null): IMail
    {
        return $this->proxy()->globalFrom($address, $name);
    }

    /**
     * 设置邮件发送地址
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Leevel\Mail\IMail
     */
    public function globalTo(string $address, ?string $name = null): IMail
    {
        return $this->proxy()->globalTo($address, $name);
    }

    /**
     * 视图 html 邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return \Leevel\Mail\IMail
     */
    public function view(string $file, array $data = []): IMail
    {
        return $this->proxy()->view($file, $data);
    }

    /**
     * html 邮件内容.
     *
     * @param string $content
     *
     * @return \Leevel\Mail\IMail
     */
    public function html(string $content): IMail
    {
        return $this->proxy()->html($content);
    }

    /**
     * 纯文本邮件内容.
     *
     * @param string $content
     *
     * @return \Leevel\Mail\IMail
     */
    public function plain(string $content): IMail
    {
        return $this->proxy()->plain($content);
    }

    /**
     * 视图纯文本邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return \Leevel\Mail\IMail
     */
    public function viewPlain(string $file, array $data = []): IMail
    {
        return $this->proxy()->viewPlain($file, $data);
    }

    /**
     * 消息回调处理.
     *
     * @param \Closure $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    public function message(Closure $callbacks): IMail
    {
        return $this->proxy()->message($callbacks);
    }

    /**
     * 添加附件.
     *
     * @param string        $file
     * @param null|\Closure $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    public function attachMail(string $file, Closure $callbacks = null): IMail
    {
        return $this->proxy()->attachMail($file, $callbacks);
    }

    /**
     * 添加内存内容附件
     * file_get_content(path).
     *
     * @param string        $data
     * @param string        $name
     * @param null|\Closure $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    public function attachData(string $data, string $name, Closure $callbacks = null): IMail
    {
        return $this->proxy()->attachData($data, $name, $callbacks);
    }

    /**
     * 图片嵌入邮件.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachView(string $file): string
    {
        return $this->proxy()->attachView($file);
    }

    /**
     * 内存内容图片嵌入邮件.
     *
     * @param string      $data
     * @param string      $name
     * @param null|string $contentType
     *
     * @return string
     */
    public function attachDataView(string $data, string $name, ?string $contentType = null): string
    {
        return $this->proxy()->attachDataView($data, $name, $contentType);
    }

    /**
     * 格式化中文附件名字.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachChinese(string $file): string
    {
        return $this->proxy()->attachChinese($file);
    }

    /**
     * 发送邮件.
     *
     * @param null|\Closure $callbacks
     * @param bool          $htmlPriority
     *
     * @return int
     */
    public function flush(Closure $callbacks = null, bool $htmlPriority = true): int
    {
        return $this->proxy()->flush($callbacks, $htmlPriority);
    }

    /**
     * 错误消息.
     *
     * @return array
     */
    public function failedRecipients(): array
    {
        return $this->proxy()->failedRecipients();
    }

    /**
     * 传输机制是否已经启动.
     *
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->proxy()->isStarted();
    }

    /**
     * 启动传输机制.
     */
    public function start(): void
    {
        $this->proxy()->start();
    }

    /**
     * 停止传输机制.
     */
    public function stop(): void
    {
        $this->proxy()->stop();
    }

    /**
     * 检查此传输机制是否处于活动状态.
     *
     * @return bool
     */
    public function ping(): bool
    {
        return $this->proxy()->ping();
    }

    /**
     * 发送消息.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @param null|array                $failedRecipients
     *
     * @return int
     */
    public function send(Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null): int
    {
        return $this->proxy()->send($message, $failedRecipients);
    }

    /**
     * 注册一个插件.
     *
     * @param \Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin): void
    {
        $this->proxy()->registerPlugin($plugin);
    }
}
