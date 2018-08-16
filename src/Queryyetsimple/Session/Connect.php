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

namespace Leevel\Session;

use Leevel\Cache\ICache;
use SessionHandlerInterface;

/**
 * connect 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.06
 * @see http://php.net/manual/zh/class.sessionhandlerinterface.php
 *
 * @version 1.0
 */
abstract class Connect implements SessionHandlerInterface
{
    /**
     * 缓存仓储.
     *
     * @var \Leevel\Cache\ICache
     */
    protected $cache;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Cache\ICache $cache
     */
    public function __construct(ICache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionid)
    {
        return $this->cache->get(
            $this->getSessionName($sessionid), []
        );
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionid, $sessiondata)
    {
        $this->cache->set(
            $this->getSessionName($sessionid),
            $sessiondata
        );
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionid)
    {
        $this->cache->delele(
            $this->getSessionName($sessionid)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * 获取 session 名字.
     *
     * @param string $sessionid
     *
     * @return string
     */
    protected function getSessionName($sessionid)
    {
        return $this->option['prefix'].$sessionid;
    }
}
