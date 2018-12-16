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

use Thrift\Server\TServer;
use Thrift\Transport\TTransport;

/**
 * 非阻塞服务
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
class TNonblockingServer extends TServer
{
    public function serve()
    {
        $this->transport_->setCallback([$this, 'handleRequest']);
        $this->transport_->listen();
    }

    public function stop()
    {
        $this->transport_->close();
    }

    public function handleRequest(TTransport $transport)
    {
        $inputTransport = $this->inputTransportFactory_->getTransport($transport);
        $outputTransport = $this->outputTransportFactory_->getTransport($transport);
        $inputProtocol = $this->inputProtocolFactory_->getProtocol($inputTransport);
        $outputProtocol = $this->outputProtocolFactory_->getProtocol($outputTransport);

        $this->processor_->process($inputProtocol, $outputProtocol);

        // $protocol = new TBinaryProtocol($transport, true, true);
        // $this->processor_->process($protocol, $protocol);
    }
}
