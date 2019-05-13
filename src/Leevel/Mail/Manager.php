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

use Leevel\Manager\Manager as Managers;

/**
 * mail 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'mail';
    }

    /**
     * 创建连接对象
     *
     * @param object $connect
     *
     * @return object
     */
    protected function createConnect(object $connect): object
    {
        return $connect;
    }

    /**
     * 创建 nulls 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Mail\Nulls
     */
    protected function makeConnectNulls(array $options = []): Nulls
    {
        return new Nulls(
            $this->container['view'],
            $this->container['event'],
            $this->normalizeConnectOption('nulls', $options)
        );
    }

    /**
     * 创建 smtp 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Mail\Smtp
     */
    protected function makeConnectSmtp(array $options = []): Smtp
    {
        return new Smtp(
            $this->container['view'],
            $this->container['event'],
            $this->normalizeConnectOption('smtp', $options)
        );
    }

    /**
     * 创建 sendmail 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Mail\Sendmail
     */
    protected function makeConnectSendmail(array $options = []): Sendmail
    {
        return new Sendmail(
            $this->container['view'],
            $this->container['event'],
            $this->normalizeConnectOption('sendmail', $options)
        );
    }
}
