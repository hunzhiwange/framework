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

namespace Leevel\Protocol\Thrift\Service;

use Thrift\Exception\TApplicationException;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Type\TMessageType;

/**
 * thrift 默认服务客户端.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.01
 *
 * @version 1.0
 */
class ThriftClient implements ThriftIf
{
    protected $input_;
    protected $output_;

    protected $seqid_ = 0;

    public function __construct($input, $output = null)
    {
        $this->input_ = $input;
        $this->output_ = $output ? $output : $input;
    }

    public function call(Request $request)
    {
        $this->send_call($request);

        return $this->recv_call();
    }

    public function send_call(Request $request)
    {
        $args = new ThriftCallArgs();
        $args->request = $request;
        $bin_accel = ($this->output_ instanceof TBinaryProtocolAccelerated) && function_exists('thrift_protocol_write_binary');
        if ($bin_accel) {
            thrift_protocol_write_binary($this->output_, 'call', TMessageType::CALL, $args, $this->seqid_, $this->output_->isStrictWrite());
        } else {
            $this->output_->writeMessageBegin('call', TMessageType::CALL, $this->seqid_);
            $args->write($this->output_);
            $this->output_->writeMessageEnd();
            $this->output_->getTransport()->flush();
        }
    }

    public function recv_call()
    {
        $bin_accel = ($this->input_ instanceof TBinaryProtocolAccelerated) && function_exists('thrift_protocol_read_binary');
        if ($bin_accel) {
            $result = thrift_protocol_read_binary($this->input_, '\Leevel\Protocol\Thrift\Service\ThriftCallResult', $this->input_->isStrictRead());
        } else {
            $rseqid = 0;
            $fname = null;
            $mtype = 0;

            $this->input_->readMessageBegin($fname, $mtype, $rseqid);
            if (TMessageType::EXCEPTION === $mtype) {
                $x = new TApplicationException();
                $x->read($this->input_);
                $this->input_->readMessageEnd();

                throw $x;
            }
            $result = new ThriftCallResult();
            $result->read($this->input_);
            $this->input_->readMessageEnd();
        }
        if (null !== $result->success) {
            return $result->success;
        }

        throw new \Exception('call failed: unknown result');
    }
}
