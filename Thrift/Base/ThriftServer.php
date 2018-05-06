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
namespace Leevel\Protocol\Thrift\Base;

use Exception;
use Thrift\Protocol\TBinaryProtocol;
use Leevel\Protocol\Thrift\Base\Socket;

/**
 * Thrift 服务
 * This class borrows heavily from the swoole thrift-rpc-server and is part of the swoole package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.03
 * @version 1.0
 * @see swoole/thrift-rpc-server (https://github.com/swoole/thrift-rpc-server)
 */
class ThriftServer extends TNonblockingServer
{
    protected $processor;
    protected $serviceName = 'Thrift';

    public function receive($serv, $fd, $fromId, $data)
    {
        $processorClass = '\Leevel\Protocol\Thrift\Service\\' . $this->serviceName . 'Processor';
        $handlerClass = '\Leevel\Protocol\Thrift\service\\' . $this->serviceName . 'Handler';

        $handler = new $handlerClass();
        $this->processor = new $processorClass($handler);

        $socket = new Socket();
        $socket->setHandle($fd);
        $socket->buffer = $data;
        $socket->server = $serv;
        $protocol = new TBinaryProtocol($socket, false, false);

        try {
            $protocol->fname = $this->serviceName;
            $this->processor->process($protocol, $protocol);
        } catch (Exception $e) {
            $this->log('CODE:' . $e->getCode() . ' MESSAGE:' . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    public function serve()
    {
    }

    protected function log($log) 
    {
    }
}
