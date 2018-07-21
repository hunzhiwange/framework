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

use Swift_Events_EventListener;
use Swift_Mailer;
use Swift_Mime_Message;
use Swift_Transport;

/**
 * connect 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 */
abstract class Connect implements Swift_Transport
{
    /**
     * swift mailer.
     *
     * @var \Swift_Mailer
     */
    protected $swiftMailer;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);

        $this->swiftMailer();
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function stop()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        return $this->getSwiftMailer()->
        send($message, $failedRecipients);
    }

    /**
     * 返回 swift mailer.
     *
     * @return \Swift_Mailer
     */
    public function getSwiftMailer()
    {
        return $this->swiftMailer;
    }

    /**
     * 生成 swift mailer.
     *
     * @return \Swift_Mailer
     */
    protected function swiftMailer()
    {
        return $this->swiftMailer = new Swift_Mailer(
            $this->makeTransport()
        );
    }
}
