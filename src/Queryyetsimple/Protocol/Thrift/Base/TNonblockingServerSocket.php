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

namespace Leevel\Protocol\Thrift\Base;

use Thrift\Server\TServerTransport;

/**
 * 非阻塞 socket 服务
 * This class borrows heavily from the swoole thrift-rpc-server and is part of the swoole package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.03
 *
 * @version 1.0
 *
 * @see swoole/thrift-rpc-server (https://github.com/swoole/thrift-rpc-server)
 */
class TNonblockingServerSocket extends TServerTransport
{
    public $callback;
    protected $port_ = 0;
    protected $host_ = '0.0.0.0';
    protected $handle_;
    protected $errno_  = 0;
    protected $errstr_ = '';
    protected $base_;
    protected $serverEvent_;

    public function __construct($host = 'localhost', $port = '9090')
    {
        $this->port_ = $port;
    }

    /**
     * Start listening.
     */
    public function listen()
    {
        $this->handle_ = stream_socket_server("tcp://{$this->host_}:{$this->port_}", $this->errno_, $this->errstr_);

        // no blocking
        stream_set_blocking($this->handle_, 0);

        $this->base_        = \event_base_new();
        $this->serverEvent_ = \event_new();

        \event_set($this->serverEvent_, $this->handle_, EV_READ | EV_PERSIST, [
            $this,
            'onConnect',
        ]);
        \event_base_set($this->serverEvent_, $this->base_);
        \event_add($this->serverEvent_);
        \event_base_loop($this->base_);
    }

    public function close()
    {
        @stream_socket_shutdown($this->handle_, STREAM_SHUT_RDWR);
        @fclose($this->handle_);
    }

    public function onConnect()
    {
        $clientSocket = stream_socket_accept($this->handle_);
        stream_set_blocking($clientSocket, 0);
        $clientEvent = event_new();

        \event_set($clientEvent, $clientSocket, EV_READ | EV_PERSIST, [
            $this,
            'onRequest',
        ], [
            $clientEvent,
        ]);
        \event_base_set($clientEvent, $this->base_);
        event_add($clientEvent);
    }

    public function onRequest($clientSocket, $events, $arg)
    {
        try {
            // $transport = new TBufferedTransport(new TNonblockingSocket($clientSocket));
            $transport = new TNonblockingSocket($clientSocket);
            call_user_func($this->callback, $transport);
        } catch (Exception $e) {
            \event_del($arg[0]);
            \event_free($arg[0]);

            // close socket
            @stream_socket_shutdown($clientSocket, STREAM_SHUT_RDWR);
            @fclose($clientSocket);

            return;
        }
    }

    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

    protected function acceptImpl()
    {
    }
}
