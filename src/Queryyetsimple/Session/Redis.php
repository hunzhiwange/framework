<?php declare(strict_types=1);
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

use SessionHandlerInterface;
use Leevel\Cache\Redis as CacheRedis;

/**
 * session.redis
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class Redis extends Connect implements SessionHandlerInterface
{

    /**
     * 配置
     *
     * @var array
     */
    protected $option = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'persistent' => false,
        'serialize' => true,
        'prefix' => null,
        'expire' => null
    ];

    /**
     * {@inheritdoc}
     */
    public function open($savepath, $sessionname)
    {
        $this->cache = new CacheRedis($this->option);
        return true;
    }
}
