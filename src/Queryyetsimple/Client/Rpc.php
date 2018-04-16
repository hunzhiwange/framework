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
namespace Leevel\Client;

/**
 * Rpc 客户端 
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.03
 * @version 1.0
 */
class Rpc
{
    /**
     * 正确响应为 200
     * 对应 HTTP 状态码 
     *
     * @var int
     */
    const OK = 200;

    /**
     * 默认上下文
     *
     * @var string
     */
    const DEFAULT_CONTEXT = 'dafault';

    /**
     * 对象实例
     *
     * @var array
     */
    protected static $instances = [];
    
    /**
     * 服务端和客户端的享元数据
     * 
     * @var array
     */
    protected $metas = [];  

    /**
     * 构造函数
     *
     * @param string $id
     */
    public function __construct($id = null) 
    {
        
    }

    /**
     * 获取 RPC 客户端实例
     *
     * @param string $id
     * @return static
     */
    public static function instance($id = null) 
    {
        $key = $id ?: self:DEFAULT_CONTEXT;

        if (isset(static::$instances[$key])) {
            return static::$instances[$key];
        } else {
            return static::$instances[$key] = new static($key);
        }
    }  

    /**
     * 设置享元数据
     *
     * @param array $metas;
     * @return void
     */
    public function setMetas(array $metas) 
    {
        $this->metas = $metas;
    }
    
    /**
     * 返回享元数据
     *
     * @return array
     */
    public function getMetas()
    {
        return $this->metas;
    }

    /**
     * 添加享元数据
     *
     * @param string|array $key
     * @param mixed $value
     * @return void
     */
    public function addMetas($key, $value = null)
    {
        $key = is_array($key) ? $key : [
            $key => $value
        ];

        foreach ($key as $k => $v) {
            $this->metas[$k] = $v;
        }
    }
}

