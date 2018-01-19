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
namespace queryyetsimple\log;

use Psr\Log\LoggerInterface;

/**
 * ilog 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface ilog extends LoggerInterface
{

    /**
     * debug
     *
     * @var string
     */
    const DEBUG = 'debug';

    /**
     * info
     *
     * @var string
     */
    const INFO = 'info';

    /**
     * notice
     *
     * @var string
     */
    const NOTICE = 'notice';

    /**
     * warning
     *
     * @var string
     */
    const WARNING = 'warning';

    /**
     * error
     *
     * @var string
     */
    const ERROR = 'error';

    /**
     * critical
     *
     * @var string
     */
    const CRITICAL = 'critical';

    /**
     * alert
     *
     * @var string
     */
    const ALERT = 'alert';

    /**
     * emergency
     *
     * @var string
     */
    const EMERGENCY = 'emergency';

    /**
     * sql
     *
     * @var string
     */
    const SQL = 'sql';

    /**
     * 记录错误消息并写入
     *
     * @param string $level 日志类型
     * @param string $message 应该被记录的错误信息
     * @param array $context
     * @return void
     */
    public function write($level, $message, array $context = []);

    /**
     * 保存日志信息
     *
     * @return void
     */
    public function save();

    /**
     * 注册日志过滤器
     *
     * @param callable $filter
     * @return void
     */
    public function registerFilter(callable $filter);

    /**
     * 注册日志处理器
     *
     * @param callable $processor
     * @return void
     */
    public function registerProcessor(callable $processor);

    /**
     * 清理日志记录
     *
     * @param string $level
     * @return int
     */
    public function clear($level = null);

    /**
     * 获取日志记录
     *
     * @param string $level
     * @return array
     */
    public function get($level = null);

    /**
     * 获取日志记录数量
     *
     * @param string $level
     * @return int
     */
    public function count($level = null);
}
