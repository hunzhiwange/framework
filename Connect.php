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
namespace Leevel\Cache;

/**
 * 缓存抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
abstract class Connect
{

    /**
     * 缓存服务句柄
     *
     * @var handle
     */
    protected $handle;

    /**
     * 配置
     *
     * @var array
     */
    protected $option = [];

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * 批量插入
     *
     * @param string|array $keys
     * @param mixed $value
     * @return void
     */
    public function put($keys, $value = null)
    {
        if (! is_array($keys)) {
            $keys = [
                $keys => $value
            ];
        }

        foreach ($keys as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * 返回缓存句柄
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->handle;
    }

    /**
     * 关闭
     *
     * @return void
     */
    public function close()
    {
    }

    /**
     * 获取缓存名字
     *
     * @param string $name
     * @param string $prefix
     * @return string
     */
    protected function getCacheName($name, $prefix = '')
    {
        return $prefix . $name;
    }

    /**
     * 读取缓存时间配置
     *
     * @param string $id
     * @param int $defaultTime
     * @return number
     */
    protected function cacheTime($id, $defaultTime = 0)
    {
        if (! $this->option['time_preset']) {
            return $defaultTime;
        }

        if (isset($this->option['time_preset'][$id])) {
            return $this->option['time_preset'][$id];
        }

        foreach ($this->option['time_preset'] as $key => $value) {
            if (preg_match($this->prepareRegexForWildcard($key), $id, $res)) {
                return $this->option['time_preset'][$key];
            }
        }

        return $defaultTime;
    }

    /**
     * 通配符正则
     *
     * @param string $regex
     * @return string
     */
    protected function prepareRegexForWildcard($regex)
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^' . str_replace('\*', '(\S+)', $regex) . '$/';

        return $regex;
    }

    /**
     * 整理配置
     *
     * @param array $option
     * @return array
     */
    protected function normalizeOptions(array $option = [])
    {
        return $option ? array_merge($this->option, $option) : $this->option;
    }
}
