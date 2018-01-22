<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Session;

use SessionHandler;
use Queryyetsimple\Option\TClass;

/**
 * aconnect 驱动抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.06
 * @version 1.0
 */
abstract class Connect
{
    use TClass;

    /**
     * 缓存仓储
     *
     * @var \Queryyetsimple\Cache\ICache
     */
    protected $cache;

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        $this->options($option);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->gc(ini_get('session.gc_maxlifetime'));
        $this->cache->close();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionid)
    {
        return $this->cache->get($this->getSessionName($sessionid));
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionid, $sessiondata)
    {
        $this->cache->set($this->getSessionName($sessionid), $sessiondata);
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionid)
    {
        $this->cache->delele($this->getSessionName($sessionid));
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * 获取 session 名字
     *
     * @param string $sessionid
     * @return string
     */
    protected function getSessionName($sessionid)
    {
        return $this->option['prefix'] . $sessionid;
    }
}
