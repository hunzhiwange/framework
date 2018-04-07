<?php
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
namespace Leevel\Cache;

use Redis as Rediss;
use RuntimeException;
use Leevel\Option\TClass;

/**
 * redis 扩展缓存
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class Redis extends Connect implements IConnect
{
    use TClass;

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'nocache_force' => '_nocache_force',
        'time_preset' => [],
        'prefix' => '_',
        'expire' => 86400,
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => '',
        'select' => 0,
        'timeout' => 0,
        'persistent' => false,
        'serialize' => true
    ];

    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        if (! extension_loaded('redis')) {
            throw new RuntimeException('Redis extension must be loaded before use.');
        }

        parent::__construct($arrOption);

        $this->hHandle = $this->getRedis();
        $this->hHandle->{$this->arrOption['persistent'] ? 'pconnect' : 'connect'}($this->arrOption['host'], $this->arrOption['port'], $this->arrOption['timeout']);

        if ($this->arrOption['password']) {
            $this->hHandle->auth($this->arrOption['password']);
        }

        if ($this->arrOption['select']) {
            $this->hHandle->select($this->arrOption['select']);
        }
    }

    /**
     * 获取缓存
     *
     * @param string $sCacheName
     * @param mixed $mixDefault
     * @param array $arrOption
     * @return mixed
     */
    public function get($sCacheName, $mixDefault = false, array $arrOption = [])
    {
        if ($this->checkForce()) {
            return $mixDefault;
        }

        $arrOption = $this->getOptions($arrOption);
        $mixData = $this->hHandle->get($this->getCacheName($sCacheName, $arrOption['prefix']));
        if (is_null($mixData)) {
            return $mixDefault;
        }
        if ($arrOption['serialize']) {
            $mixData = unserialize($mixData);
        }
        return $mixData;
    }

    /**
     * 设置缓存
     *
     * @param string $sCacheName
     * @param mixed $mixData
     * @param array $arrOption
     * @return void
     */
    public function set($sCacheName, $mixData, array $arrOption = [])
    {
        $arrOption = $this->getOptions($arrOption);
        if ($arrOption['serialize']) {
            $mixData = serialize($mixData);
        }

        $arrOption['expire'] = $this->cacheTime($sCacheName, $arrOption['expire']);

        if (( int ) $arrOption['expire']) {
            $this->hHandle->setex($this->getCacheName($sCacheName, $arrOption['prefix']), ( int ) $arrOption['expire'], $mixData);
        } else {
            $this->hHandle->set($this->getCacheName($sCacheName, $arrOption['prefix']), $mixData);
        }
    }

    /**
     * 清除缓存
     *
     * @param string $sCacheName
     * @param array $arrOption
     * @return void
     */
    public function delele($sCacheName, array $arrOption = [])
    {
        $this->hHandle->delete($this->getCacheName($sCacheName, $this->getOptions($arrOption)['prefix']));
    }

    /**
     * 关闭 redis
     *
     * @return void
     */
    public function close()
    {
        $this->hHandle->close();
        $this->hHandle = null;
    }

    /**
     * 返回 redis 对象
     *
     * @return \Redis
     */
    protected function getRedis()
    {
        return new Rediss();
    }
}
