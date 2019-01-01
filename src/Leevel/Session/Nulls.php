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

namespace Leevel\Session;

use SessionHandlerInterface;

/**
 * session.nulls.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.17
 *
 * @version 1.0
 */
class Nulls implements SessionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionid): string
    {
        return serialize([]);
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionid, $sessiondata): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionid): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime): int
    {
        return 0;
    }

    /**
     * 返回缓存仓储.
     */
    public function getCache()
    {
    }
}
