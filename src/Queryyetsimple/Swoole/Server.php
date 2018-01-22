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
namespace Queryyetsimple\Swoole;

use Exception;
use RuntimeException;
use Swoole\{
    Server as SwooleServer,
    Client as SwooleClient
};
use Queryyetsimple\{
    Option\TClass,
    Console\Command
};

/**
 * swoole 服务基类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.12.25
 * @see https://www.swoole.com/
 * @see https://www.cnblogs.com/luojianqun/p/5355439.html
 * @version 1.0
 */
class Server
{

    use TClass;

    /**
     * swoole 服务实例
     * 
     * @var \Swoole\Server
     */
    protected $objServer;

    /**
     * 命令行工具
     * 
     * @var \queryyetsimple\Console\Command
     */
    protected $objCommand;

    /**
     * 配置
     * 
     * @var array
     */
    protected $arrOption = [
        // 监听 IP 地址
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'host' => '127.0.0.1', 
        
        // 监听端口
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'port' => '9501', 
        
        // swoole 进程名称
        'process_name' => 'queryswoole', 
        
        // swoole 进程保存路径
        'pid_path' => '', 
        
        // 设置启动的 worker 进程数
        // see https://wiki.swoole.com/wiki/page/275.html
        'worker_num' => 8, 

        // 设置启动的 task worker 进程数
        // https://wiki.swoole.com/wiki/page/276.html
        'task_worker_num' => 4,
        
        // 守护进程化
        // see https://wiki.swoole.com/wiki/page/278.html
        'daemonize' => 0
    ];

    /**
     * 服务回调事件
     * 
     * @var array
     */
    protected $arrServerEvent = [
        'start', 
        'connect', 
        'workerStart', 
        'managerStart', 
        'workerStop',
        'request',
        'receive',
        'task',
        'finish',
        'shutdown',
        'close'
    ];

    /**
     * 构造函数
     * 
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        $this->options($arrOption);
    }

    /**
     * 设置命名行
     * 实现友好的屏幕信息输出
     *
     * @param queryyetsimple\Console\Command $objCommand
     * @return void
     */
    public function setCommand(command $objCommand)
    {
        $this->objCommand = $objCommand;
    }
    
    /**
     * 运行服务
     * 
     * @return void
     */
    public function startServer()
    {
        $this->checkBefore();
        $this->createServer();
        $this->eventServer();
        $this->startSwooleServer();
    }

    /**
     * 列出当前服务进程
     *
     * @see https://www.cnblogs.com/emanlee/p/3983678.html
     * @return void
     */
    public function listsServer()
    {
        $this->info('List swoole service process', true, '');

        $strCmd = "ps aux|grep " . $this->getOption('process_name') . "|grep -v grep|awk '{print $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11}'";
        exec($strCmd, $arrOut);
        if (empty($arrOut)) {
            $this->warn('No swoole service process was found', true);
        }

        foreach ($arrOut as &$sV) {
            $sV = explode(' ', $sV);
        }

        $this->info($strCmd, true);

        $this->objCommand->table([
            // 进程的用户
            'USER', 

            // 进程的 ID
            'PID', 

            // 进程占用的 CPU 百分比
            '%CPU', 

            // 占用内存的百分比
            '%MEM', 

            // 该进程使用的虚拟内存量（KB）
            'VSZ(kb)', 

            // 该进程占用的固定内存量（KB）
            'RSS(kb)', 

            // 该进程在哪个终端上运行
            'TTY', 

            // STAT 状态
            'STAT', 

            // 该进程被触发启动时间
            'START', 

            // 该进程实际使用CPU运行的时间
            'TIME', 

            // 命令的名称和参数
            'COMMAND' 
        ], $arrOut);
    }

    /**
     * 结束当前服务进程
     *
     * @return void
     */
    public function stopServer()
    {
        $this->info('Stop swoole service process...', true, '');

        $strPidFile = $this->getOption('pid_path');

        if (! is_file($strPidFile)) {
            $this->error(sprintf('Swoole pid file %s not exists.', $strPidFile), true);
            return;
        }

        $arrPid = explode("\n", file_get_contents($strPidFile));

        $arrBind = $this->portBind($this->getOption('port'));
        if (empty($arrBind) || !isset($arrBind[$arrPid[0]])) {
            $this->error(sprintf('Specified port occupancy process does not exist,port:%d, pid:%d.', $this->getOption('port'), $arrPid[0]), true);
            return;
        }

        $arrCmd = [];
        $strCmd = "kill {$arrPid[0]}";
        $arrCmd[] = $strCmd;
        exec($strCmd);
        do {
            $arrOut = [];
            $strCmd = "ps ax | awk '{ print $1 }' | grep -e \"^{$arrPid[0]}$\"";
            exec($strCmd, $arrOut);
            $arrCmd[] = $strCmd;
            if (empty($arrOut)) {
                break;
            }
        } while (true);

        if (is_file($strPidFile)) {
            unlink($strPidFile);
        }

        $this->info(sprintf('Execution command stop succeeds,port %s:%d process ends', $this->getOption('host'), $this->getOption('port')), true);

        foreach ($arrCmd as $strCmd) {
            $this->info($strCmd, true);
        }
    }

    /**
     * 重启当前服务进程
     *
     * @return void
     */
    public function restartServer()
    {
        $this->info('Restart swoole service...', true, '');

        try {
            $objCient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
            $booResult = $objCient->connect($this->getOption('host'), $this->getOption('port'));

            if (empty($booResult)) {
                $this->error(sprintf('%s:%d swoole service does not exist or has been closed.', $this->getOption('host'), $this->getOption('port')), true);
                return;
            } else {
                $objCient->send(json_encode(['action' => 'reload']));
            }

            $this->info(sprintf('Execution command reload success, port %s:%d process has restarted.', $this->getOption('host'), $this->getOption('port')), true);
        } catch (Exception $oE) {
            $this->error($oE->getMessage(), true);
            $this->error($oE->getTraceAsString(), true);
        }
    }

    /**
     * 关闭当前服务进程
     *
     * @return void
     */
    public function closeServer()
    {
        $this->info('Close swoole service...', true, '');

        try {
            $objCient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
            $booResult = $objCient->connect($this->getOption('host'), $this->getOption('port'));

            if (empty($booResult)) {
                $this->error(sprintf('%s:%d swoole service does not exist or has been closed.', $this->getOption('host'), $this->getOption('port')), true);
                return;
            } else {
                $objCient->send(json_encode(['action' => 'close']));
            }

            if (is_file($this->getOption('pid_path'))) {
                unlink($this->getOption('pid_path'));
            }

            $this->info(sprintf('Execution command close success, port %s:%d process has closed.', $this->getOption('host'), $this->getOption('port')), true);
        } catch (Exception $oE) {
            $this->error($oE->getMessage(), true);
            $this->error($oE->getTraceAsString(), true);
        }
    }

    /**
     * 查看当前服务进程
     *
     * @return void
     */
    public function statusServer()
    {
        $this->info('Status of swoole service...', true, '');

        $strPidFile = $this->getOption('pid_path');

        if (! is_file($strPidFile)) {
            $this->error(sprintf('Swoole pid file %s not exists.', $strPidFile), true);
            return;
        }

        $arrPid = explode("\n", file_get_contents($strPidFile));

        $arrBind = $this->portBind($this->getOption('port'));
        if (empty($arrBind) || !isset($arrBind[$arrPid[0]])) {
            $this->error(sprintf('Specified port occupancy process does not exist,port:%d, pid:%d.', $this->getOption('port'), $arrPid[0]), true);
            return;
        }

        $objCient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
        $booResult = $objCient->connect($this->getOption('host'), $this->getOption('port'));

        if (empty($booResult)) {
            $this->error(sprintf('%s:%d swoole service does not exist or has been closed.', $this->getOption('host'), $this->getOption('port')), true);
            return;
        } else {
            $objCient->send(json_encode(['action' => 'status']));

            $strOut = $objCient->recv();
            $arrResult = json_decode($strOut);

            // see https://wiki.swoole.com/wiki/page/288.html
            $arrDetail = [];
            foreach ($arrResult as $strKey => $mixVal) {
                if ($strKey == 'start_time') {
                    $mixVal = date("Y-m-d H:i:s", $mixVal);
                }
                
                $arrDetail[] = [
                    $strKey, 
                    $mixVal
                ];
            }
            
            $this->objCommand->table([
                'Item', 
                'Value'
            ], $arrDetail);
        }
    }

    /**
     * 在当前服务进程开启任务
     *
     * @return void
     */
    public function taskServer()
    {
        $this->info('Stask swoole service...', true, '');

        try {
            $objCient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
            $booResult = $objCient->connect($this->getOption('host'), $this->getOption('port'));

            if (empty($booResult)) {
                $this->error(sprintf('%s:%d swoole service does not exist or has been closed.', $this->getOption('host'), $this->getOption('port')), true);
                return;
            } else {
                // 发送数据
                // $objCient->send('test');
            }
        } catch (Exception $oE) {
            $this->error($oE->getMessage(), true);
            $this->error($oE->getTraceAsString(), true);
        }
    }

    /**
     * 返回服务
     * 
     * @return \Swoole\Server
     */
    public function getServer()
    {
        return $this->objServer;
    }
    
    /**
     * 主进程的主线程
     * 记录进程 id,脚本实现自动重启
     * 
     * @param \Swoole\Server $objServer
     * @see https://wiki.swoole.com/wiki/page/p-event/onStart.html
     * @return void
     */
    public function onStart(SwooleServer $objServer)
    {
        $this->info(sprintf('Swoole server is started at %s:%d', $this->getOption('host'), $this->getOption('port')), true, '');
        
        $this->info('Swoole server master worker start', true);
        
        $this->setProcessName($this->arrOption['process_name'] . '-master');
        
        $strPid = $objServer->master_pid . "\n" . $objServer->manager_pid;
        if (! file_put_contents($this->getOption('pid_path'), $strPid)) {
            $this->warn('Swoole pid saved failed', true);
        }
    }
    
    /**
     * 新的连接进入时
     * 
     * @param \Swoole\Server $objServer
     * @param int $intFd
     * @param int $intReactorId
     * @see https://wiki.swoole.com/wiki/page/49.html
     * @return void
     */
    public function onConnect(SwooleServer $objServer, int $intFd, int $intReactorId)
    {
        $this->line(sprintf('Swoole server connect, fd %d, reactorId %d.', $intFd, $intReactorId));
    }
    
    /**
     * worker start 加载业务脚本常驻内存
     * 由于服务端命令行也采用 QueryPHP,无需再次引入 QueryPHP
     * 
     * @param \Swoole\Server $objServer
     * @param int $intWorkeId
     * @see https://wiki.swoole.com/wiki/page/p-event/onWorkerStart.html
     * @return void
     */
    public function onWorkerStart(SwooleServer $objServer, int $intWorkeId)
    {
        if ($intWorkeId >= $this->getOption('worker_num')) {
            $this->setProcessName($this->getOption('process_name') . '-task');
        } else {
            $this->setProcessName($this->getOption('process_name') . '-event');
        }
    }
    
    /**
     * 当管理进程启动时调用
     * 
     * @param \Swoole\Server $objServer
     * @see https://wiki.swoole.com/wiki/page/190.html
     * @return void
     */
    public function onManagerStart(SwooleServer $objServer)
    {
        $this->info('Swoole server manager worker start', true);
        $this->setProcessName($this->arrOption['process_name'] . '-manager');
        $this->showStartOption($objServer);
    }
    
    /**
     * worker进程终止时发生
     * 
     * @param \Swoole\Server $objServer
     * @param int $intWorkerId
     * @return void
     */
    public function onWorkerStop(SwooleServer $objServer, int $intWorkerId)
    {
        $this->error(sprintf('Swoole server %s worker %d shutdown', $objServer->setting['process_name'], $intWorkerId));
    }

    /**
     * 监听数据发送事件
     * 
     * @param \Swoole\Server $objServer
     * @param int $intFd
     * @param int $intReactorId
     * @param string $strData
     * @see https://wiki.swoole.com/wiki/page/50.html
     * @return void
     */
    public function onReceive(SwooleServer $objServer, int $intFd, int $intReactorId, string $strData) {
        $this->line(sprintf('Get message from client %d:%s.', $intFd, $strData));

        if($this->isJson($strData)) {
            $arrResult = json_decode($strData, true);
            switch ($arrResult['action']) {
                case 'reload': // 重启
                    $objServer->reload();
                    break;
                case 'close': // 关闭
                    $objServer->shutdown();
                    break;
                case 'status': // 状态
                    // see https://wiki.swoole.com/wiki/page/288.html
                    $objServer->send($intFd, json_encode($objServer->stats()));
                    break;         
                default:
                    // 耗时任务放入 task
                    $arrResult['querytask_meta'] = [
                        'fd' => $intReactorId,
                        'reactor_id' => $intReactorId
                    ];
                    $objServer->task(json_encode($arrResult));
                    break;
            }
        } else {
            // 耗时任务放入 task
            $arrResult = [];
            $arrResult['data'] = $strData;
            $arrResult['querytask_meta'] = [
                'fd' => $intReactorId,
                'reactor_id' => $intReactorId
            ];
            $objServer->task(json_encode($arrResult));
        }
    }
    
    /**
     * 监听连接 Finish 事件
     * 
     * @param \Swoole\Server $objServer
     * @param int $intTaskId
     * @param string $strData
     * @see https://wiki.swoole.com/wiki/page/136.html
     * @return void
     */
    public function onFinish(SwooleServer $objServer, int $intTaskId, string $strData)
    {
        $this->info(sprintf('Task %d finish, the result is %s', $intTaskId, $strData));
    }

    /**
     * 监听连接 task 事件
     * 
     * @param \Swoole\Server $objServer
     * @param int $intTaskId
     * @param int $intFromId
     * @param string $strData
     * @see https://wiki.swoole.com/wiki/page/134.html
     * @return void
     */
    public function onTask(SwooleServer $objServer, int $intTaskId, int $intFromId, string $strData) {
        $this->info(sprintf('Task %d form workder %d, the result is %s', $intTaskId, $intFromId, $strData));

        $arrResult = json_decode($strData, true);

        // task 实际执行功能

        $objServer->finish($strData);
    }
    
    /**
     * Server 正常结束时发生
     * 
     * @param \Swoole\Server $objServer
     * @see https://wiki.swoole.com/wiki/page/p-event/onShutdown.html
     * @return void
     */
    public function onShutdown(SwooleServer $objServer)
    {
        if (is_file($this->getOption('pid_path'))) {
            unlink($this->getOption('pid_path'));
        }
        
        $this->error('Swoole server shutdown');
    }
    
    /**
     * 监听连接关闭事件
     * 
     * @param \Swoole\Server $objServer
     * @param int $intFd
     * @param int $intReactorId
     * @see https://wiki.swoole.com/wiki/page/p-event/onClose.html
     * @return void
     */
    public function onClose(SwooleServer $objServer, int $intFd, int $intReactorId)
    {
        $this->line(sprintf('Swoole server close, fd %d, reactorId %d.', $intFd, $intReactorId));
    }
    
    /**
     * 创建服务前环境验证
     * 
     * @return void
     */
    protected function checkBefore()
    {
        $this->checkEnvironment();
        $this->checkPort();
        $this->checkService();
        $this->checkPidPath();
    }
    
    /**
     * 验证 pid_path 是否可用
     * 
     * @return void
     */
    protected function checkPidPath()
    {
        if (! $this->getOption('pid_path')) {
            throw new Exception('Pid path is not set');
        }
        
        $strDir = dirname($this->getOption('pid_path'));
        
        if (! is_dir($strDir)) {
            mkdir($strDir, 0777, true);
        }
        
        if (! is_writable($strDir)) {
            throw new Exception(sprintf("swoole pid dir is not writable" . $strDir));
        }
    }
    
    /**
     * 验证服务是否被占用
     * 
     * @return void
     */
    protected function checkService()
    {
        $strFile = $this->getOption('pid_path');

        if (is_file($strFile)) {
            $arrPid = explode("\n", file_get_contents($strFile));
            
            $sCmd = "ps ax | awk '{ print $1 }' | grep -e \"^{$arrPid[0]}$\"";
            exec($sCmd, $arrOut);

            if (! empty($arrOut)) {
                throw new Exception(sprintf('Swoole pid file %s is already exists,pid is %d', $strFile, $arrPid[0]));
            } else {
                $this->warn(sprintf('Warning:swoole pid file is already exists.', $strFile) . PHP_EOL . 'It is possible that the swoole service was last unusual exited.' . PHP_EOL . 'The non daemon mode ctrl+c termination is the most possible.' . PHP_EOL);
                unlink($strFile);
            }
        }
    }
    
    /**
     * 验证端口是否被占用
     * 
     * @return void
     */
    protected function checkPort()
    {
        $arrBind = $this->portBind($this->getOption('port'));
        if ($arrBind) {
            foreach ($arrBind as $sK => $arrVal) {
                if ($arrVal['ip'] == '*' || $arrVal['ip'] == $this->getOption('host')) {
                    throw new Exception(sprintf('The port has been used %s:%s,the port process ID is %s', $arrVal['ip'], $arrVal['port'], $sK));
                }
            }
        }
    }
    
    /**
     * 获取端口占用情况
     *
     * @param int $intPort
     * @return array
     */
    protected function portBind(int $intPort)
    {
        $arrRet = [];
        
        $sCmd = "lsof -i :{$intPort}|awk '$1 != \"COMMAND\"  {print $1, $2, $9}'";
        exec($sCmd, $arrOut);
        
        if (! empty($arrOut)) {
            foreach ($arrOut as $sOut) {
                $arrTemp = explode(' ', $sOut);
                list($sIp, $nP) = explode(':', $arrTemp[2]);
                $arrRet[$arrTemp[1]] = [
                    'cmd' => $arrTemp[0], 
                    'ip' => $sIp, 
                    'port' => $nP
                ];
            }
        }
        
        return $arrRet;
    }
    
    /**
     * 创建 server
     * 
     * @return void
     */
    protected function createServer()
    {
        $this->objServer = new SwooleServer($this->getOption('host'), $this->getOption('port'));
        $this->initServer();
    }
    
    /**
     * 初始化 http server
     * 
     * @return void
     */
    protected function initServer()
    {
        $this->objServer->set($this->arrOption);
    }
    
    /**
     * http server 绑定事件
     *
     * @return void
     */
    protected function eventServer()
    {
        foreach ($this->arrServerEvent as $sEvent) {
            $this->objServer->on($sEvent, [
                $this, 
                'on' . ucfirst($sEvent)
            ]);
        }
    }
    
    /**
     * http server 启动
     *
     * @return void
     */
    protected function startSwooleServer()
    {
        $this->objServer->start();
    }
    
    /**
     * 显示服务启动配置
     *
     * @param \Swoole\Server $objServer
     * @return void
     */
    protected function showStartOption(SwooleServer $objServer)
    {
        $arrOption = [];
        foreach ($objServer->setting as $sKey => $mixVal) {
            if ($sKey == 'pid_path') {
                $mixVal = str_replace(path_swoole_cache(), '~@~/swoole', $mixVal);
            }
            
            $arrOption[] = [
                $sKey, 
                $mixVal
            ];
        }
        
        $this->objCommand->table([
            'Item', 
            'Value'
        ], $arrOption);
    }
    
    /**
     * 设置 swoole 进程名称
     * 
     * @param string $sName
     * @see http://php.net/manual/zh/function.cli-set-process-title.php
     * @see https://wiki.swoole.com/wiki/page/125.html
     * @return void
     */
    protected function setProcessName(string $sName)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($sName);
        } else {
            if (function_exists('swoole_set_process_name')) {
                swoole_set_process_name($sName);
            } else {
                throw new Exception('Require cli_set_process_title or swoole_set_process_name.');
            }
        }
    }
    
    /**
     * 是否为守候进程运行
     *
     * @return int
     */
    protected function daemonize()
    {
        return ! $this->getOption['daemonize'];
    }
    
    /**
     * 屏幕消息输出
     *
     * @param string $sMessage
     * @param boolean $booForce
     * @param string $strFormatTime
     * @return void
     */
    protected function info(string $sMessage, bool $booForce = false, string $strFormatTime = 'H:i:s')
    {
        $this->messageAll('info', $sMessage, $booForce, $strFormatTime);
    }

    /**
     * 屏幕消息输出普通消息
     *
     * @param string $sMessage
     * @param boolean $booForce
     * @param string $strFormatTime
     * @return void
     */
    protected function line(string $sMessage, bool $booForce = false, string $strFormatTime = 'H:i:s')
    {
        $this->messageAll('line', $sMessage, $booForce, $strFormatTime);
    }
    
    /**
     * 屏幕消息输出警告
     *
     * @param string $sMessage
     * @param string $strFormatTime
     * @return void
     */
    protected function warn(string $sMessage, bool $booForce = false, string $strFormatTime = '')
    {
        $this->messageAll('warn', $sMessage, $booForce, $strFormatTime);
    }

    /**
     * 屏幕消息输出错误
     *
     * @param string $sMessage
     * @param string $strFormatTime
     * @return void
     */
    protected function error(string $sMessage, bool $booForce = false, string $strFormatTime = '')
    {
        $this->messageAll('error', $sMessage, $booForce, $strFormatTime);
    }

    /**
     * 消息输入统一处理
     *
     * @param string $strType
     * @param string $sMessage
     * @param string $strFormatTime
     * @return void
     */
    protected function messageAll(string $strType, string $sMessage, bool $booForce = false, string $strFormatTime = 'H:i:s')
    {
        if (! $booForce && ! $this->daemonize()) {
            return;
        }
        
        $this->objCommand->$strType($this->messageTime($sMessage, $strFormatTime));
    }
    
    /**
     * 屏幕消息时间
     *
     * @param string $sMessage
     * @param string $strFormatTime
     * @return string
     */
    protected function messageTime(string $sMessage, string $strFormatTime = '')
    {
        return $this->objCommand->time($sMessage, $strFormatTime);
    }

    /**
     * 验证是否为正常的 JSON 字符串
     *
     * @param mixed $mixData
     * @return boolean
     */
    protected function isJson($mixData)
    {
        if (! is_scalar($mixData) && ! method_exists($mixData, '__toString')) {
            return false;
        }

        json_decode($mixData);

        return json_last_error() === JSON_ERROR_NONE;
    }

    
    /**
     * 验证 swoole 运行环境
     *
     * @return void
     */
    protected function checkEnvironment() :void
    {
        $this->checkPhpVersion();
        $this->checkSwooleInstalled();
        $this->checkSwooleInstalled();
    }
    
    /**
     * 验证 swoole 是否安装
     *
     * @return void
     */
    protected function checkSwooleInstalled() :void
    {
        if (! class_exists('Swoole\Server')) {
            throw new RuntimeException('Swoole is not installed.');
        }
    }
    
    /**
     * 验证 PHP 版本
     *
     * @return void
     */
    protected function checkPhpVersion() :void
    {
        if (version_compare(PHP_VERSION, '7.1.0', '<')) {
            throw new RuntimeException("PHP 7.1.0 OR Higher");
        }
    }
    
    /**
     * 验证 swoole 版本
     * 
     * @return void
     */
    protected function checkSwooleVersion() :void
    {
        if (version_compare(phpversion('swoole'), '2.0', '<')) {
            throw new RuntimeException("Swoole 2.0 OR Higher");
        }
    }

    /**
     * call
     *
     * @param string $method
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        return $this->objServer->$method(...$arrArgs);
    }
}
