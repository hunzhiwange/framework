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
namespace Leevel\Protocol\Thrift\Service;

use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Protocol\TProtocol;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Exception\TApplicationException;
use Thrift\Protocol\TBinaryProtocolAccelerated;

/**
 * thrift 默认服务调用参数
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.04.01
 * @version 1.0
 */
class ThriftCallArgs
{
    public static $_TSPEC;

    /**
     * @var \Leevel\Protocol\Thrift\Service\Request
     */
    public $request = null;

    public function __construct($vals=null)
    {
        if (!isset(self::$_TSPEC)) {
            self::$_TSPEC = array(
        1 => array(
          'var' => 'request',
          'type' => TType::STRUCT,
          'class' => '\Leevel\Protocol\Thrift\Service\Request',
          ),
        );
        }
        if (is_array($vals)) {
            if (isset($vals['request'])) {
                $this->request = $vals['request'];
            }
        }
    }

    public function getName()
    {
        return 'ThriftCallArgs';
    }

    public function read($input)
    {
        $xfer = 0;
        $fname = null;
        $ftype = 0;
        $fid = 0;
        $xfer += $input->readStructBegin($fname);
        while (true) {
            $xfer += $input->readFieldBegin($fname, $ftype, $fid);
            if ($ftype == TType::STOP) {
                break;
            }
            switch ($fid) {
        case 1:
          if ($ftype == TType::STRUCT) {
              $this->request = new Request();
              $xfer += $this->request->read($input);
          } else {
              $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
            $xfer += $input->readFieldEnd();
        }
        $xfer += $input->readStructEnd();
        return $xfer;
    }

    public function write($output)
    {
        $xfer = 0;
        $xfer += $output->writeStructBegin('ThriftCallArgs');
        if ($this->request !== null) {
            if (!is_object($this->request)) {
                throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
            }
            $xfer += $output->writeFieldBegin('request', TType::STRUCT, 1);
            $xfer += $this->request->write($output);
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }
}
