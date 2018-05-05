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
namespace Leevel\Throttler;

/**
 * IRateLimiter 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
interface IRateLimiter
{

    /**
     * 数据存储分隔符
     *
     * @var string
     */
    const SEPARATE = "\t";

    /**
     * 验证请求
     *
     * @return boolean
     */
    public function attempt();

    /**
     * 判断资源是否被耗尽
     *
     * @return bool
     */
    public function tooManyAttempt();

    /**
     * 执行请求
     *
     * @return $this
     */
    public function hit();

    /**
     * 清理记录
     *
     * @return $this
     */
    public function clear();

    /**
     * 下次重置时间
     *
     * @return $this
     */
    public function endTime();

    /**
     * 请求返回 HEADER
     *
     * @return array
     */
    public function header();

    /**
     * 距离下一次请求等待时间
     *
     * @return int
     */
    public function retryAfter();

    /**
     * 指定时间内剩余请求次数
     *
     * @return int
     */
    public function remaining();

    /**
     * 指定时间长度
     *
     * @param int $intXRateLimitLimit
     * @return $this
     */
    public function limitLimit($intXRateLimitLimit = 60);

    /**
     * 指定时间内允许的最大请求次数
     *
     * @param int $intXRateLimitTime
     * @return $this
     */
    public function limitTime($intXRateLimitTime = 60);

    /**
     * 返回缓存组件
     *
     * @return \Leevel\Cache\ICache
     */
    public function getCache();
}
