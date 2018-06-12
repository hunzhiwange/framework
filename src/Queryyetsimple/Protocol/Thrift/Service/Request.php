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

use Thrift\Exception\TProtocolException;
use Thrift\Type\TType;

/**
 * 定义一个请求包结构
 * 约定请求数据包，方便只定义一个解构全自动调用 MVC 服务
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.02
 *
 * @version 1.0
 */
class Request
{
    public static $_TSPEC;

    /**
     * @var string
     */
    public $call;

    /**
     * @var string[]
     */
    public $params;

    /**
     * @var array
     */
    public $metas;

    public function __construct($vals = null)
    {
        if (!isset(self::$_TSPEC)) {
            self::$_TSPEC = [
                1 => [
                    'var'  => 'call',
                    'type' => TType::STRING,
                ],
                2 => [
                    'var'   => 'params',
                    'type'  => TType::LST,
                    'etype' => TType::STRING,
                    'elem'  => [
                        'type' => TType::STRING,
                    ],
                ],
                3 => [
                    'var'   => 'metas',
                    'type'  => TType::MAP,
                    'ktype' => TType::STRING,
                    'vtype' => TType::STRING,
                    'key'   => [
                        'type' => TType::STRING,
                    ],
                    'val' => [
                        'type' => TType::STRING,
                    ],
                ],
            ];
        }
        if (is_array($vals)) {
            if (isset($vals['call'])) {
                $this->call = $vals['call'];
            }
            if (isset($vals['params'])) {
                $this->params = $vals['params'];
            }
            if (isset($vals['metas'])) {
                $this->metas = $vals['metas'];
            }
        }
    }

    public function getName()
    {
        return 'Request';
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
            if (TType::STOP === $ftype) {
                break;
            }
            switch ($fid) {
        case 1:
          if (TType::STRING === $ftype) {
              $xfer += $input->readString($this->call);
          } else {
              $xfer += $input->skip($ftype);
          }

          break;
        case 2:
          if (TType::LST === $ftype) {
              $this->params = [];
              $_size0 = 0;
              $_etype3 = 0;
              $xfer += $input->readListBegin($_etype3, $_size0);
              for ($_i4 = 0; $_i4 < $_size0; $_i4++) {
                  $elem5 = null;
                  $xfer += $input->readString($elem5);
                  $this->params[] = $elem5;
              }
              $xfer += $input->readListEnd();
          } else {
              $xfer += $input->skip($ftype);
          }

          break;
        case 3:
          if (TType::MAP === $ftype) {
              $this->metas = [];
              $_size6 = 0;
              $_ktype7 = 0;
              $_vtype8 = 0;
              $xfer += $input->readMapBegin($_ktype7, $_vtype8, $_size6);
              for ($_i10 = 0; $_i10 < $_size6; $_i10++) {
                  $key11 = '';
                  $val12 = '';
                  $xfer += $input->readString($key11);
                  $xfer += $input->readString($val12);
                  $this->metas[$key11] = $val12;
              }
              $xfer += $input->readMapEnd();
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
        $xfer += $output->writeStructBegin('Request');
        if (null !== $this->call) {
            $xfer += $output->writeFieldBegin('call', TType::STRING, 1);
            $xfer += $output->writeString($this->call);
            $xfer += $output->writeFieldEnd();
        }
        if (null !== $this->params) {
            if (!is_array($this->params)) {
                throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
            }
            $xfer += $output->writeFieldBegin('params', TType::LST, 2);

            $output->writeListBegin(TType::STRING, count($this->params));

            foreach ($this->params as $iter13) {
                $xfer += $output->writeString($iter13);
            }

            $output->writeListEnd();

            $xfer += $output->writeFieldEnd();
        }
        if (null !== $this->metas) {
            if (!is_array($this->metas)) {
                throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
            }
            $xfer += $output->writeFieldBegin('metas', TType::MAP, 3);

            $output->writeMapBegin(TType::STRING, TType::STRING, count($this->metas));

            foreach ($this->metas as $kiter14 => $viter15) {
                $xfer += $output->writeString($kiter14);
                $xfer += $output->writeString($viter15);
            }

            $output->writeMapEnd();

            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();

        return $xfer;
    }
}
