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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Session;

/**
 * session.test.
 */
class Test extends Session implements ISession
{
    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'id'         => null,
        'name'       => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
        $this->setName($this->option['name']);
    }

    /**
     * open.
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * close.
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * read.
     */
    public function read(string $sessionId): string
    {
        return serialize([]);
    }

    /**
     * write.
     */
    public function write(string $sessionId, string $sessionData): bool
    {
        return true;
    }

    /**
     * destroy.
     */
    public function destroy(string $sessionId): bool
    {
        return true;
    }

    /**
     * gc.
     */
    public function gc(int $maxLifetime): int
    {
        return 0;
    }
}
