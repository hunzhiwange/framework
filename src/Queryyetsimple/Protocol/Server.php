<?php

declare(strict_types=1);

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

namespace Leevel\Protocol;

use Exception;
use Leevel\Console\Command;
use RuntimeException;
use Swoole\Client as SwooleClient;
use Swoole\Server as SwooleServer;

/**
 * swoole 服务基类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.12.25
 * @see https://www.swoole.com/
 * @see https://www.cnblogs.com/luojianqun/p/5355439.html
 *
 * @version 1.0
 */
class Server implements IServer
{
    /**
     * swoole 服务实例.
     *
     * @var \Swoole\Server
     */
    protected $server;

    /**
     * 命令行工具.
     *
     * @var \leevel\Console\Command
     */
    protected $command;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        // 监听 IP 地址
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'host' => '0.0.0.0',

        // 监听端口
        // see https://wiki.swoole.com/wiki/page/p-server.html
        // see https://wiki.swoole.com/wiki/page/327.html
        'port' => '9501',

        // swoole 进程名称
        'process_name' => 'queryphp.swoole.default',

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
        'daemonize' => 0,
    ];

    /**
     * 服务回调事件.
     *
     * @var array
     */
    protected $serverEvent = [
        'start',
        'connect',
        'workerStart',
        'managerStart',
        'workerStop',
        'receive',
        'task',
        'finish',
        'shutdown',
        'close',
    ];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->server->{$method}(...$args);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setOption(string $name, $value): void
    {
        $this->option[$name] = $value;
    }

    /**
     * 设置命名行
     * 实现友好的屏幕信息输出.
     *
     * @param leevel\Console\Command $command
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    /**
     * 运行服务
     */
    public function startServer()
    {
        $this->checkBefore();
        $this->createServer();
        $this->eventServer();
        $this->startSwooleServer();
    }

    /**
     * 列出当前服务进程.
     *
     * @see https://www.cnblogs.com/emanlee/p/3983678.html
     */
    public function listsServer()
    {
        $this->info('List swoole service process', true, '');

        $cmd = 'ps aux|grep '.
            $this->option['process_name'].
            "|grep -v grep|awk '{print $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11}'";

        exec($cmd, $out);

        if (empty($out)) {
            $this->warn('No swoole service process was found', true);
        }

        foreach ($out as &$v) {
            $v = explode(' ', $v);
        }

        $this->info($cmd, true);

        $this->command->table([
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
            'COMMAND',
        ], $out);
    }

    /**
     * 结束当前服务进程
     * 每个 Worker 进程退出或重启时执行一次
     */
    public function stopServer()
    {
        $this->info('Stop swoole service process...', true, '');

        $pidFile = $this->option['pid_path'];

        if (!is_file($pidFile)) {
            $this->error(
                sprintf('Swoole pid file %s not exists.', $pidFile),
                true
            );

            return;
        }

        $pid = explode("\n", file_get_contents($pidFile));

        $bind = $this->portBind((int) ($this->option['port']));

        if (empty($bind) || !isset($bind[$pid[0]])) {
            $this->error(
                sprintf(
                    'Specified port occupancy process does not exist,port:%d, pid:%d.',
                    $this->option['port'],
                    $pid[0]),
                true
            );

            return;
        }

        $cmds = [];
        $cmd = "kill {$pid[0]}";
        $cmds[] = $cmd;
        exec($cmd);

        do {
            $out = [];
            $cmd = "ps ax | awk '{ print $1 }' | grep -e \"^{$pid[0]}$\"";
            exec($cmd, $out);
            $cmds[] = $cmd;

            if (empty($out)) {
                break;
            }
        } while (true);

        if (is_file($pidFile)) {
            unlink($pidFile);
        }

        $this->info(
            sprintf(
                'Execution command stop succeeds,port %s:%d process ends',
                $this->option['host'],
                $this->option['port']
            ),
            true
        );

        foreach ($cmds as $cmd) {
            $this->info($cmd, true);
        }
    }

    /**
     * 重启当前服务进程.
     */
    public function restartServer()
    {
        $this->info('Restart swoole service...', true, '');

        try {
            $cient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);

            $result = $cient->connect(
                (string) $this->option['host'],
                (int) $this->option['port']
            );

            if (empty($result)) {
                $this->error(
                    sprintf(
                        '%s:%d swoole service does not exist or has been closed.',
                        $this->option['host'],
                        $this->option['port']
                    ),
                    true
                );

                return;
            }

            $cient->send(json_encode(['action' => 'reload']));

            $this->info(
                sprintf(
                    'Execution command reload success, port %s:%d process has restarted.',
                    $this->option['host'],
                    $this->option['port']
                ),
                true
            );
        } catch (Exception $e) {
            $this->error($e->getMessage(), true);
            $this->error($e->getTraceAsString(), true);
        }
    }

    /**
     * 关闭当前服务进程.
     */
    public function closeServer()
    {
        $this->info('Close swoole service...', true, '');

        try {
            $cient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);

            $result = $cient->connect(
                $this->option['host'],
                $this->option['port']
            );

            if (empty($result)) {
                $this->error(
                    sprintf(
                        '%s:%d swoole service does not exist or has been closed.',
                        $this->option['host'],
                        $this->option['port']
                    ),
                    true
                );

                return;
            }

            $cient->send(json_encode(['action' => 'close']));

            if (is_file($this->option['pid_path'])) {
                unlink($this->option['pid_path']);
            }

            $this->info(
                sprintf(
                    'Execution command close success, port %s:%d process has closed.',
                    $this->option['host'],
                    $this->option['port']
                ),
                true
            );
        } catch (Exception $e) {
            $this->error($e->getMessage(), true);
            $this->error($e->getTraceAsString(), true);
        }
    }

    /**
     * 查看当前服务进程.
     */
    public function statusServer()
    {
        $this->info('Status of swoole service...', true, '');

        $pidFile = $this->option['pid_path'];

        if (!is_file($pidFile)) {
            $this->error(
                sprintf('Swoole pid file %s not exists.', $pidFile),
                true
            );

            return;
        }

        $pid = explode("\n", file_get_contents($pidFile));

        $bind = $this->portBind((int) ($this->option['port']));

        if (empty($bind) || !isset($bind[$pid[0]])) {
            $this->error(
                sprintf(
                    'Specified port occupancy process does not exist,port:%d, pid:%d.',
                    $this->option['port'],
                    $pid[0]
                ),
                true
            );

            return;
        }

        $cient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);

        $result = $cient->connect(
            $this->option['host'],
            $this->option['port']
        );

        if (empty($result)) {
            $this->error(
                sprintf(
                    '%s:%d swoole service does not exist or has been closed.',
                    $this->option['host'],
                    $this->option['port']
                ),
                true
            );

            return;
        }

        $cient->send(json_encode(['action' => 'status']));

        $out = $cient->recv();
        $result = json_decode($out);

        // see https://wiki.swoole.com/wiki/page/288.html
        $detail = [];

        foreach ($result as $key => $val) {
            if ('start_time' === $key) {
                $val = date('Y-m-d H:i:s', $val);
            }

            $detail[] = [
                $key,
                $val,
            ];
        }

        $this->command->table([
            'Item',
            'Value',
        ], $detail);
    }

    /**
     * 在当前服务进程开启任务
     */
    public function taskServer()
    {
        $this->info('Stask swoole service...', true, '');

        try {
            $cient = new SwooleClient(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);

            $result = $cient->connect(
                $this->option['host'],
                $this->option['port']
            );

            if (empty($result)) {
                $this->error(
                    sprintf(
                        '%s:%d swoole service does not exist or has been closed.',
                        $this->option['host'],
                        $this->option['port']
                    ),
                    true
                );

                return;
            }

            // 发送数据
            // $cient->send('test');
        } catch (Exception $e) {
            $this->error($e->getMessage(), true);
            $this->error($e->getTraceAsString(), true);
        }
    }

    /**
     * 返回服务
     *
     * @return \Swoole\Server
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * 主进程的主线程
     * 记录进程 id,脚本实现自动重启.
     *
     * @param \Swoole\Server $server
     *
     * @see https://wiki.swoole.com/wiki/page/p-event/onStart.html
     */
    public function onStart(SwooleServer $server)
    {
        $this->info(
            sprintf(
                'Swoole server is started at %s:%d',
                $this->option['host'],
                $this->option['port']
            ),
            true,
            ''
        );

        $this->info('Swoole server master worker start', true);

        $this->setProcesname($this->option['process_name'].'-master');

        $pid = $server->master_pid."\n".$server->manager_pid;

        $dirname = dirname($this->option['pid_path']);

        if (!is_writable($dirname) ||
            !file_put_contents($this->option['pid_path'], $pid)) {
            throw new InvalidArgumentException(
                sprintf('Dir %s is not writeable', $dirname)
            );
        }

        chmod($this->option['pid_path'], 0777);
    }

    /**
     * 新的连接进入时
     * 每次连接时(相当于每个浏览器第一次打开页面时)执行一次, reload 时连接不会断开, 也就不会再次触发该事件.
     *
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $reactorId
     *
     * @see https://wiki.swoole.com/wiki/page/49.html
     */
    public function onConnect(SwooleServer $server, int $fd, int $reactorId)
    {
        $this->line(
            sprintf(
                'Swoole server connect, fd %d, reactorId %d.',
                $fd,
                $reactorId
            )
        );
    }

    /**
     * worker start 加载业务脚本常驻内存
     * 由于服务端命令行也采用 QueryPHP,无需再次引入 QueryPHP
     * 每个 Worker 进程启动或重启时都会执行.
     *
     * @param \Swoole\Server $server
     * @param int            $workeId
     *
     * @see https://wiki.swoole.com/wiki/page/p-event/onWorkerStart.html
     */
    public function onWorkerStart(SwooleServer $server, int $workeId)
    {
        if ($workeId >= $this->option['worker_num']) {
            $this->setProcesname(
                $this->option['process_name'].'-task'
            );
        } else {
            $this->setProcesname(
                $this->option['process_name'].'-event'
            );
        }
    }

    /**
     * 当管理进程启动时调用
     * 服务器启动时执行一次
     *
     * @param \Swoole\Server $server
     *
     * @see https://wiki.swoole.com/wiki/page/190.html
     */
    public function onManagerStart(SwooleServer $server)
    {
        $this->info('Swoole server manager worker start', true);

        $this->setProcesname(
            $this->option['process_name'].'-manager'
        );

        $this->showStartOption($server);
    }

    /**
     * worker进程终止时发生
     *
     * @param \Swoole\Server $server
     * @param int            $workerId
     */
    public function onWorkerStop(SwooleServer $server, int $workerId)
    {
        $this->error(
            sprintf(
                'Swoole server %s worker %d shutdown',
                $server->setting['process_name'],
                $workerId
            )
        );
    }

    /**
     * 监听数据发送事件.
     *
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $reactorId
     * @param string         $data
     *
     * @see https://wiki.swoole.com/wiki/page/50.html
     */
    public function onReceive(SwooleServer $server, int $fd, int $reactorId, string $data)
    {
        $this->line(
            sprintf('Get message from client %d:%s.', $fd, $data)
        );

        if ($this->isJson($data)) {
            $result = json_decode($data, true);

            switch ($result['action']) {
                case 'reload': // 重启
                    $server->reload();

                    break;
                case 'close': // 关闭
                    $server->shutdown();

                    break;
                case 'status': // 状态
                    // see https://wiki.swoole.com/wiki/page/288.html
                    $server->send($fd, json_encode($server->stats()));

                    break;
                default:
                    // 耗时任务放入 task
                    $result['querytask_meta'] = [
                        'fd'         => $reactorId,
                        'reactor_id' => $reactorId,
                    ];
                    $server->task(json_encode($result));

                    break;
            }
        } else {
            // 耗时任务放入 task
            $result = [];
            $result['data'] = $data;
            $result['querytask_meta'] = [
                'fd'         => $reactorId,
                'reactor_id' => $reactorId,
            ];

            $server->task(json_encode($result));
        }
    }

    /**
     * 监听连接 Finish 事件.
     *
     * @param \Swoole\Server $server
     * @param int            $taskId
     * @param string         $data
     *
     * @see https://wiki.swoole.com/wiki/page/136.html
     */
    public function onFinish(SwooleServer $server, int $taskId, string $data)
    {
        $this->info(
            sprintf('Task %d finish, the result is %s', $taskId, $data)
        );
    }

    /**
     * 监听连接 task 事件.
     *
     * @param \Swoole\Server $server
     * @param int            $taskId
     * @param int            $fromId
     * @param string         $data
     *
     * @see https://wiki.swoole.com/wiki/page/134.html
     */
    public function onTask(SwooleServer $server, int $taskId, int $fromId, string $data)
    {
        $this->info(
            sprintf(
                'Task %d form workder %d, the result is %s',
                $taskId,
                $fromId,
                $data
            )
        );

        $result = json_decode($data, true);

        // task 实际执行功能

        $server->finish($data);
    }

    /**
     * Server 正常结束时发生
     * 服务器关闭时执行一次
     *
     * @param \Swoole\Server $server
     *
     * @see https://wiki.swoole.com/wiki/page/p-event/onShutdown.html
     */
    public function onShutdown(SwooleServer $server)
    {
        if (is_file($this->option['pid_path'])) {
            unlink($this->option['pid_path']);
        }

        $this->error('Swoole server shutdown');
    }

    /**
     * 监听连接关闭事件
     * 每个浏览器连接关闭时执行一次, reload 时连接不会断开, 也就不会触发该事件.
     *
     * @param \Swoole\Server $server
     * @param int            $fd
     * @param int            $reactorId
     *
     * @see https://wiki.swoole.com/wiki/page/p-event/onClose.html
     */
    public function onClose(SwooleServer $server, int $fd, int $reactorId)
    {
        $this->line(
            sprintf(
                'Swoole server close, fd %d, reactorId %d.',
                $fd,
                $reactorId
            )
        );
    }

    /**
     * 创建服务前环境验证
     */
    protected function checkBefore()
    {
        $this->checkEnvironment();
        $this->checkPort();
        $this->checkService();
        $this->checkPidPath();
    }

    /**
     * 验证 pid_path 是否可用.
     */
    protected function checkPidPath()
    {
        if (!$this->option['pid_path']) {
            throw new Exception('Pid path is not set');
        }

        $dir = dirname($this->option['pid_path']);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (!is_writable($dir)) {
            throw new Exception(
                sprintf('swoole pid dir is not writable'.$dir)
            );
        }
    }

    /**
     * 验证服务是否被占用.
     */
    protected function checkService()
    {
        $file = $this->option['pid_path'];

        if (is_file($file)) {
            $pid = explode("\n", file_get_contents($file));

            $cmd = "ps ax | awk '{ print $1 }' | grep -e \"^{$pid[0]}$\"";
            exec($cmd, $out);

            if (!empty($out)) {
                throw new Exception(
                    sprintf(
                        'Swoole pid file %s is already exists,pid is %d',
                        $file,
                        $pid[0]
                    )
                );
            }

            $this->warn(
                sprintf(
                    'Warning:swoole pid file is already exists.',
                    $file
                ).
                PHP_EOL.
                'It is possible that the swoole service was last unusual exited.'.
                PHP_EOL.
                'The non daemon mode ctrl+c termination is the most possible.'.
                PHP_EOL
            );

            unlink($file);
        }
    }

    /**
     * 验证端口是否被占用.
     */
    protected function checkPort()
    {
        $bind = $this->portBind(
            (int) ($this->option['port'])
        );

        if ($bind) {
            foreach ($bind as $k => $val) {
                if ('*' === $val['ip'] ||
                    $val['ip'] === $this->option['host']) {
                    throw new Exception(
                        sprintf(
                            'The port has been used %s:%s,the port process ID is %s',
                            $val['ip'],
                            $val['port'],
                            $k
                        )
                    );
                }
            }
        }
    }

    /**
     * 获取端口占用情况.
     *
     * @param int $port
     *
     * @return array
     */
    protected function portBind(int $port)
    {
        $result = [];

        $cmd = "lsof -i :{$port}|awk '$1 != \"COMMAND\"  {print $1, $2, $9}'";
        exec($cmd, $out);

        if (!empty($out)) {
            foreach ($out as $val) {
                $tmp = explode(' ', $val);
                list($ip, $p) = explode(':', $tmp[2]);

                $result[$tmp[1]] = [
                    'cmd'  => $tmp[0],
                    'ip'   => $ip,
                    'port' => $p,
                ];
            }
        }

        return $result;
    }

    /**
     * 创建 server.
     */
    protected function createServer()
    {
        $this->server = new SwooleServer(
            $this->option['host'],
            (int) ($this->option['port'])
        );

        $this->initServer();
    }

    /**
     * 初始化 http server.
     */
    protected function initServer()
    {
        $this->server->set($this->option);
    }

    /**
     * http server 绑定事件.
     */
    protected function eventServer()
    {
        foreach ($this->serverEvent as $event) {
            $this->server->on($event, [
                $this,
                'on'.ucfirst($event),
            ]);
        }
    }

    /**
     * http server 启动.
     */
    protected function startSwooleServer()
    {
        $this->server->start();
    }

    /**
     * 显示服务启动配置
     * 服务器启动时执行一次
     *
     * @param \Swoole\Server $server
     */
    protected function showStartOption(SwooleServer $server)
    {
        $option = [];

        foreach ($server->setting as $key => $val) {
            if ('pid_path' === $key) {
                $val = str_replace(path_swoole_cache(), 'runtime/swoole', $val);
            }

            $option[] = [
                $key,
                $val,
            ];
        }

        $this->command->table([
            'Item',
            'Value',
        ], $option);
    }

    /**
     * 设置 swoole 进程名称.
     *
     * @param string $name
     *
     * @see http://php.net/manual/zh/function.cli-set-process-title.php
     * @see https://wiki.swoole.com/wiki/page/125.html
     */
    protected function setProcesname(string $name)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else {
            if (function_exists('swoole_set_process_name')) {
                swoole_set_process_name($name);
            } else {
                throw new Exception(
                    'Require cli_set_process_title or swoole_set_process_name.'
                );
            }
        }
    }

    /**
     * 是否为守候进程运行.
     *
     * @return int
     */
    protected function daemonize()
    {
        return !$this->option['daemonize'];
    }

    /**
     * 屏幕消息输出.
     *
     * @param string $message
     * @param bool   $force
     * @param string $formatTime
     */
    protected function info(string $message, bool $force = false, string $formatTime = 'H:i:s')
    {
        $this->messageAll('info', $message, $force, $formatTime);
    }

    /**
     * 屏幕消息输出普通消息.
     *
     * @param string $message
     * @param bool   $force
     * @param string $formatTime
     */
    protected function line(string $message, bool $force = false, string $formatTime = 'H:i:s')
    {
        $this->messageAll('line', $message, $force, $formatTime);
    }

    /**
     * 屏幕消息输出警告.
     *
     * @param string $message
     * @param string $formatTime
     */
    protected function warn(string $message, bool $force = false, string $formatTime = '')
    {
        $this->messageAll('warn', $message, $force, $formatTime);
    }

    /**
     * 屏幕消息输出错误.
     *
     * @param string $message
     * @param string $formatTime
     */
    protected function error(string $message, bool $force = false, string $formatTime = '')
    {
        $this->messageAll('error', $message, $force, $formatTime);
    }

    /**
     * 消息输入统一处理.
     *
     * @param string $strType
     * @param string $message
     * @param string $formatTime
     */
    protected function messageAll(string $strType, string $message, bool $force = false, string $formatTime = 'H:i:s')
    {
        if (!$force && !$this->daemonize()) {
            return;
        }

        $this->command->{$strType}(
            $this->messageTime($message, $formatTime)
        );
    }

    /**
     * 屏幕消息时间.
     *
     * @param string $message
     * @param string $formatTime
     *
     * @return string
     */
    protected function messageTime(string $message, string $formatTime = '')
    {
        return $this->command->time($message, $formatTime);
    }

    /**
     * 验证是否为正常的 JSON 字符串.
     *
     * @param mixed $data
     *
     * @return bool
     */
    protected function isJson($data): bool
    {
        if (!is_scalar($data) &&
            !method_exists($data, '__toString')) {
            return false;
        }

        json_decode($data);

        return JSON_ERROR_NONE === json_last_error();
    }

    /**
     * 验证 swoole 运行环境.
     */
    protected function checkEnvironment(): void
    {
        $this->checkPhpVersion();
        $this->checkSwooleInstalled();
        $this->checkSwooleInstalled();
    }

    /**
     * 验证 swoole 是否安装.
     */
    protected function checkSwooleInstalled(): void
    {
        if (!extension_loaded('swoole')) {
            throw new RuntimeException('Swoole is not installed.');
        }
    }

    /**
     * 验证 PHP 版本.
     */
    protected function checkPhpVersion(): void
    {
        if (version_compare(PHP_VERSION, '7.1.3', '<')) {
            throw new RuntimeException('PHP 7.1.3 OR Higher');
        }
    }

    /**
     * 验证 swoole 版本.
     */
    protected function checkSwooleVersion(): void
    {
        if (version_compare(phpversion('swoole'), '2.1.1', '<')) {
            throw new RuntimeException('Swoole 2.1.1 OR Higher');
        }
    }
}
