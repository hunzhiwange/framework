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

/**
 * session.nulls.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.17
 *
 * @version 1.0
 */
class Nulls extends Session implements ISession
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'id'         => null,
        'name'       => null,
    ];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);

        $this->setName($this->option['name']);
    }

    /**
     * open.
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * close.
     *
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * read.
     *
     * @param string $sessionId
     *
     * @return string
     */
    public function read(string $sessionId): string
    {
        return serialize([]);
    }

    /**
     * write.
     *
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    public function write(string $sessionId, string $sessionData): bool
    {
        return true;
    }

    /**
     * destroy.
     *
     * @param string $sessionId
     *
     * @return bool
     */
    public function destroy(string $sessionId): bool
    {
        return true;
    }

    /**
     * gc.
     *
     * @param int $maxLifetime
     *
     * @return int
     */
    public function gc(int $maxLifetime): int
    {
        return 0;
    }
}
