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

use RuntimeException;
use InvalidArgumentException;
use Monolog\{
    Logger,
    Handler\StreamHandler,
    Handler\SyslogHandler,
    Handler\ErrorLogHandler,
    Formatter\LineFormatter,
    Handler\RotatingFileHandler
};
use queryyetsimple\support\str;

/**
 * log.monolog
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.01
 * @version 1.0
 */
class monolog extends aconnect implements iconnect
{

    /**
     * Monolog
     *
     * @var \Monolog\Logger
     */
    protected $monolog;

    /**
     * 配置
     *
     * @var array
     */
    protected $option = [
        'type' => [
            'file'
        ],
        'channel' => 'Q',
        'name' => 'Y-m-d H',
        'size' => 2097152,
        'path' => ''
    ];

    /**
     * Monolog 支持日志级别
     *
     * @var array
     */
    protected $supportLevel = [
        ilog::DEBUG => Logger::DEBUG,
        ilog::INFO => Logger::INFO,
        ilog::NOTICE => Logger::NOTICE,
        ilog::WARNING => Logger::WARNING,
        ilog::ERROR => Logger::ERROR,
        ilog::CRITICAL => Logger::CRITICAL,
        ilog::ALERT => Logger::ALERT,
        ilog::EMERGENCY => Logger::EMERGENCY
    ];

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        parent::__construct($option);
        
        $this->monolog = new Logger($this->getOption('channel'));

        foreach ($this->getOption('type') as $type) {
            $this->{'make' . ucwords(str::camelize($type)) . 'Handler'}();
        }
    }

    /**
     * 注册文件 handler
     *
     * @param string $path
     * @param string $level
     * @return void
     */
    public function file($path, $level = ilog::DEBUG)
    {
        $handler = new StreamHandler($path, $this->parseMonologLevel($level));
        $this->monolog->pushHandler($handler);
        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * 注册每日文件 handler
     *
     * @param string $path
     * @param int $days
     * @param string $level
     * @return void
     */
    public function dailyFile($path, $days = 0, $level = ilog::DEBUG)
    {
        $handler = new RotatingFileHandler($path, $days, $this->parseMonologLevel($level));
        $this->monolog->pushHandler($handler);
        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * 注册系统 handler
     *
     * @param string $name
     * @param string $level
     * @return \Psr\Log\LoggerInterface
     */
    public function syslog($name = 'queryphp', $level = ilog::DEBUG)
    {
        $handler = new SyslogHandler($name, LOG_USER, $level);
        return $this->monolog->pushHandler($handler);
    }

    /**
     * 注册 error_log handler
     *
     * @param string $level
     * @param int $messageType
     * @return void
     */
    public function errorLog($level = ilog::DEBUG, $messageType = ErrorLogHandler::OPERATING_SYSTEM)
    {
        $handler = new ErrorLogHandler($messageType, $this->parseMonologLevel($level));
        $this->monolog->pushHandler($handler);
        $handler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * monolog 回调
     *
     * @param callable|null $callback
     * @return $this
     */
    public function monolog($callback = null)
    {
        if (is_callable($callback)) {
            call_user_func_array($callback, [
                $this
            ]);
        }

        return $this;
    }

    /**
     * 取得 Monolog
     *
     * @return \Monolog\Logger
     */
    public function getMonolog()
    {
        return $this->monolog;
    }

    /**
     * 日志写入接口
     *
     * @param array $data
     * @return void
     */
    public function save(array $data)
    {
        $level = array_keys($this->supportLevel);

        foreach ($data as $item) {
            if (! in_array($item[0], $level)) {
                $item[0] = ilog::DEBUG;
            }
            $this->monolog->{$item[0]}($item[1], $item[2]);
        }
    }

    /**
     * 初始化文件 handler
     *
     * @return void
     */
    protected function makeFileHandler()
    {
        $path = $this->getPath();
        $this->checkSize($path);
        $this->file($path);
    }

    /**
     * 初始化每日文件 handler
     *
     * @return void
     */
    protected function makeDailyFileHandler()
    {
        $path = $this->getPath();
        $this->checkSize($this->getDailyFilePath($path));
        $this->dailyFile($path);
    }

    /**
     * 初始化系统 handler
     *
     * @return void
     */
    protected function makeSyslogHandler()
    {
        $this->syslog();
    }

    /**
     * 初始化 error_log handler
     *
     * @return void
     */
    protected function makeErrorLogHandler()
    {
        $this->errorLog();
    }

    /**
     * 每日文件真实路径
     *
     * @param string $path
     * @return void
     */
    protected function getDailyFilePath($path)
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if ($ext) {
            $path = substr($path, 0, strrpos($path, '.' . $ext));
        }
        return $path . date('-Y-m-d') . ($ext ? '.' . $ext : '');
    }

    /**
     * 默认格式化
     *
     * @return \Monolog\Formatter\LineFormatter
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter(null, null, true, true);
    }

    /**
     * 获取 Monolog 级别
     * 不支持级别归并到 DEBUG
     *
     * @param string $level
     * @return int
     */
    protected function parseMonologLevel($level)
    {
        if (isset($this->supportLevel[$level])) {
            return $this->supportLevel[$level];
        }
        return $this->supportLevel[ilog::DEBUG];
    }
}
