<?php
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
namespace Queryyetsimple\Protocol\Thrift\Base;

use Thrift\Transport\TFramedTransport;
use Thrift\Factory\TStringFuncFactory;
use Thrift\Exception\TTransportException;

/**
 * Socket Transport
 * This class borrows heavily from the swoole thrift-rpc-server and is part of the swoole package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.03
 * @version 1.0
 * @see swoole/thrift-rpc-server (https://github.com/swoole/thrift-rpc-server)
 */
class Socket extends TFramedTransport
{
    public $buffer = '';
    public $offset = 0;
    public $server;
    protected $fd;
    protected $read_ = true;
    protected $rBuf_ = '';
    protected $wBuf_ = '';

    public function setHandle($fd)
    {
        $this->fd = $fd;
    }

    public function readFrame()
    {
        $buf = $this->_read(4);
        $val = unpack('N', $buf);
        $sz = $val[1];

        $this->rBuf_ = $this->_read($sz);
    }

    public function _read($len)
    {
        if (strlen($this->buffer) - $this->offset < $len) {
            throw new TTransportException('TSocket['.strlen($this->buffer).'] read '.$len.' bytes failed.');
        }
        $data = substr($this->buffer, $this->offset, $len);
        $this->offset += $len;
        
        return $data;
    }

    public function read($len)
    {
        if (!$this->read_) {
            return $this->_read($len);
        }

        if (TStringFuncFactory::create()->strlen($this->rBuf_) === 0) {
            $this->readFrame();
        }

        // Just return full buff
        if ($len >= TStringFuncFactory::create()->strlen($this->rBuf_)) {
            $out = $this->rBuf_;
            $this->rBuf_ = null;
            return $out;
        }

        // Return TStringFuncFactory::create()->substr
        $out = TStringFuncFactory::create()->substr($this->rBuf_, 0, $len);
        $this->rBuf_ = TStringFuncFactory::create()->substr($this->rBuf_, $len);
        return $out;
    }

    public function write($buf)
    {
        $this->wBuf_ .= $buf;
    }

    public function flush()
    {
        $out = pack('N', strlen($this->wBuf_));
        $out .= $this->wBuf_;
        $this->server->send($this->fd, $out);
        $this->wBuf_ = '';
    }
}
