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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Protocol;

use InvalidArgumentException;
use Leevel\Di\IContainer;
use Leevel\Protocol\Process as ProtocolProcess;
use Swoole\Process;
use Swoole\Runtime;
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
abstract class Server
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * swoole 服务实例.
     *
     * @var \Swoole\Server
     */
    protected $server;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [];

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
     * @param \Leevel\Di\IContainer $container
     * @param array                 $option
     */
    public function __construct(IContainer $container, array $option = [])
    {
        $this->validSwoole();

        $this->container = $container;

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
     *
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 获取配置.
     *
     * @return array
     */
    public function getOption(): array
    {
        return $this->option;
    }

    /**
     * 添加自定义进程.
     *
     * @param string $process
     */
    public function process(string $process): void
    {
        $newProgress = new Process(
            function (Process $worker) use ($process) {
                $newProgress = $this->container->make($process);

                if (!is_object($newProgress) || ($newProgress instanceof ProtocolProcess)) {
                    throw new InvalidArgumentException(
                        sprintf('Process `%s` was invalid.', $process)
                    );
                }

                if (!is_callable([$newProgress, 'handle'])) {
                    throw new InvalidArgumentException(
                        sprintf('The `handle` of process `%s` was not found.', $process)
                    );
                }

                $worker->name($this->option['process_name'].'.'.$newProgress->getName());

                $newProgress->handle($this, $worker);
            }
        );

        $this->server->addProcess($newProgress);
    }

    /**
     * 运行服务
     */
    public function startServer()
    {
        $this->checkPidPath();
        $this->createServer();
        $this->eventServer();
        $this->startSwooleServer();
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
        $this->log(
            sprintf(
                'Server is started at %s:%d',
                $this->option['host'], $this->option['port']
            ),
            true, ''
        );

        $this->log('Server master worker start', true);

        $this->setProcessName($this->option['process_name'].'.master');

        $pid = $server->master_pid."\n".$server->manager_pid;

        $dirname = dirname($this->option['pid_path']);

        if (!is_writable($dirname) ||
            !file_put_contents($this->option['pid_path'], $pid)) {
            throw new InvalidArgumentException(
                sprintf('Dir %s is not writeable.', $dirname)
            );
        }

        chmod($this->option['pid_path'], 0666 & ~umask());

        // 开启验证模式
        //Runtime::enableStrictMode();
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
        $this->log(
            sprintf(
                'Server connect, fd %d, reactorId %d.',
                $fd, $reactorId
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
            $this->setProcessName(
                $this->option['process_name'].'.task'
            );
        } else {
            $this->setProcessName(
                $this->option['process_name'].'.worker'
            );
        }

        // 开启 opcache 重连后需要刷新
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
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
        $this->log('Server manager worker start', true);

        $this->setProcessName(
            $this->option['process_name'].'.manager'
        );
    }

    /**
     * worker进程终止时发生
     *
     * @param \Swoole\Server $server
     * @param int            $workerId
     */
    public function onWorkerStop(SwooleServer $server, int $workerId)
    {
        $this->log(
            sprintf(
                'Server %s worker %d shutdown',
                $server->setting['process_name'], $workerId
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
        $this->log(
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
        $this->log(
            sprintf(
                'Task %d form workder %d, the result is %s',
                $taskId, $fromId, $data
            )
        );

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

        $this->log('Server shutdown');
    }

    /**
     * 清理协程上下文数据.
     */
    protected function removeCoroutine(): void
    {
        $this->container->removeCoroutine();
    }

    /**
     * 验证 pid_path 是否可用.
     */
    protected function checkPidPath()
    {
        if (!$this->option['pid_path']) {
            throw new InvalidArgumentException('Pid path is not set');
        }

        $dirname = dirname($this->option['pid_path']);

        if (!is_dir($dirname)) {
            if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
                throw new InvalidArgumentException(
                    sprintf('Unable to create the %s directory.', $dirname)
                );
            }

            mkdir($dirname, 0777, true);
        }

        if (!is_writable($dirname)) {
            throw new InvalidArgumentException(
                sprintf('Pid dir is not writable'.$dirname)
            );
        }
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

        foreach ($this->option['processes'] as $process) {
            $this->process($process);
        }
    }

    /**
     * http server 绑定事件.
     */
    protected function eventServer()
    {
        $type = get_class($this);
        $type = substr($type, strrpos($type, '\\') + 1);
        $type = str_replace('Server', '', $type);

        foreach ($this->serverEvent as $event) {
            if (!method_exists($this, $onEvent = 'on'.$type.ucfirst($event))) {
                $onEvent = 'on'.ucfirst($event);
            }

            $this->server->on($event, [$this, $onEvent]);
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
     * 设置 swoole 进程名称.
     *
     * @param string $name
     *
     * @see http://php.net/manual/zh/function.cli-set-process-title.php
     * @see https://wiki.swoole.com/wiki/page/125.html
     */
    protected function setProcessName(string $name)
    {
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($name);
        } else {
            if (function_exists('swoole_set_process_name')) {
                swoole_set_process_name($name);
            } else {
                throw new InvalidArgumentException(
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
     * 记录日志.
     *
     * @param string $message
     * @param bool   $force
     * @param string $formatTime
     */
    protected function log(string $message, bool $force = false, string $formatTime = 'H:i:s')
    {
        if (!$force && !$this->daemonize()) {
            return;
        }

        fwrite(STDOUT, $this->messageTime($message, $formatTime).PHP_EOL);
    }

    /**
     * 消息时间.
     *
     * @param string $message
     * @param string $formatTime
     *
     * @return string
     */
    protected function messageTime(string $message, string $formatTime = ''): string
    {
        return ($formatTime ? sprintf('[%s]', date($formatTime)) : '').$message;
    }

    /**
     * 验证 swoole 版本.
     */
    protected function validSwoole(): void
    {
        if (!extension_loaded('swoole')) {
            throw new InvalidArgumentException('Swoole was not installed.');
        }

        if (version_compare(phpversion('swoole'), '4.2.9', '<')) {
            throw new InvalidArgumentException('Swoole 4.2.9 OR Higher');
        }
    }
}
