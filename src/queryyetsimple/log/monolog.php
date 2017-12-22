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
use queryyetsimple\support\{
    str,
    option
};

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
    protected $objMonolog;

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
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
    protected $arrSupportLevel = [
        istore::DEBUG => Logger::DEBUG,
        istore::INFO => Logger::INFO,
        istore::NOTICE => Logger::NOTICE,
        istore::WARNING => Logger::WARNING,
        istore::ERROR => Logger::ERROR,
        istore::CRITICAL => Logger::CRITICAL,
        istore::ALERT => Logger::ALERT,
        istore::EMERGENCY => Logger::EMERGENCY
    ];

    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        parent::__construct($arrOption);
        $this->objMonolog = new Logger($this->getOption('channel'));

        foreach ($this->getOption('type') as $strType) {
            $this->{'make' . ucwords(str::camelize($strType)) . 'Handler'}();
        }
    }

    /**
     * 注册文件 handler
     *
     * @param string $strPath
     * @param string $strLevel
     * @return void
     */
    public function file($strPath, $strLevel = istore::DEBUG)
    {
        $this->objMonolog->pushHandler($objHandler = new StreamHandler($strPath, $this->parseMonologLevel($strLevel)));
        $objHandler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * 注册每日文件 handler
     *
     * @param string $strPath
     * @param int $intDays
     * @param string $level
     * @return void
     */
    public function dailyFile($strPath, $intDays = 0, $strLevel = istore::DEBUG)
    {
        $this->objMonolog->pushHandler($objHandler = new RotatingFileHandler($strPath, $intDays, $this->parseMonologLevel($strLevel)));
        $objHandler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * 注册系统 handler
     *
     * @param string $strName
     * @param string $strLevel
     * @return \Psr\Log\LoggerInterface
     */
    public function syslog($strName = 'queryphp', $strLevel = istore::DEBUG)
    {
        return $this->objMonolog->pushHandler(new SyslogHandler($strName, LOG_USER, $strLevel));
    }

    /**
     * 注册 error_log handler
     *
     * @param string $strLevel
     * @param int $intMessageType
     * @return void
     */
    public function errorLog($strLevel = istore::DEBUG, $intMessageType = ErrorLogHandler::OPERATING_SYSTEM)
    {
        $this->objMonolog->pushHandler($objHandler = new ErrorLogHandler($intMessageType, $this->parseMonologLevel($strLevel)));
        $objHandler->setFormatter($this->getDefaultFormatter());
    }

    /**
     * 回调
     *
     * @param callable|null $mixCallback
     * @return $this
     */
    public function monolog($mixCallback = null)
    {
        if (is_callable($mixCallback)) {
            call_user_func_array($mixCallback, [
                $this
            ]);
            $this->objMessage->attach($objAttachment);
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
        return $this->objMonolog;
    }

    /**
     * 日志写入接口
     *
     * @param array $arrData
     * @return void
     */
    public function save(array $arrData)
    {
        foreach ($arrData as $arrItem) {
            $this->objMonolog->{$arrItem[0]}($arrItem[1], $arrItem[2]);
        }
    }

    /**
     * 初始化文件 handler
     *
     * @return void
     */
    protected function makeFileHandler()
    {
        $this->checkSize($strPath = $this->getPath());
        $this->file($strPath);
    }

    /**
     * 初始化每日文件 handler
     *
     * @return void
     */
    protected function makeDailyFileHandler()
    {
        $strPath = $this->getPath();
        $this->checkSize($this->getDailyFilePath($strPath));
        $this->dailyFile($strPath);
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
     * @param string $strPath
     * @return void
     */
    protected function getDailyFilePath($strPath)
    {
        $strExt = pathinfo($strPath, PATHINFO_EXTENSION);
        if ($strExt) {
            $strPath = substr($strPath, 0, strrpos($strPath, '.' . $strExt));
        }
        return $strPath . date('-Y-m-d') . ($strExt ? '.' . $strExt : '');
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
     * @param string $strLevel
     * @return int
     */
    protected function parseMonologLevel($strLevel)
    {
        if (isset($this->arrSupportLevel [$strLevel])) {
            return $this->arrSupportLevel [$strLevel];
        }
        return $this->arrSupportLevel [istore::DEBUG];
    }
}
