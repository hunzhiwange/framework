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

use Thrift\Type\TType;

/**
 * 定义一个响应包结构
 * 通用响应接口，数据以 JSON 进行交互.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.04.02
 *
 * @version 1.0
 */
class Response
{
    public static $_TSPEC;

    /**
     * @var int
     */
    public $status;

    /**
     * @var string
     */
    public $data;

    public function __construct($vals = null)
    {
        if (!isset(self::$_TSPEC)) {
            self::$_TSPEC = [
                1 => [
                    'var' => 'status',
                    'type' => TType::I16,
                ],
                2 => [
                    'var' => 'data',
                    'type' => TType::STRING,
                ],
            ];
        }
        if (is_array($vals)) {
            if (isset($vals['status'])) {
                $this->status = $vals['status'];
            }
            if (isset($vals['data'])) {
                $this->data = $vals['data'];
            }
        }
    }

    public function getName()
    {
        return 'Response';
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
          if (TType::I16 === $ftype) {
              $xfer += $input->readI16($this->status);
          } else {
              $xfer += $input->skip($ftype);
          }

          break;
        case 2:
          if (TType::STRING === $ftype) {
              $xfer += $input->readString($this->data);
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
        $xfer += $output->writeStructBegin('Response');
        if (null !== $this->status) {
            $xfer += $output->writeFieldBegin('status', TType::I16, 1);
            $xfer += $output->writeI16($this->status);
            $xfer += $output->writeFieldEnd();
        }
        if (null !== $this->data) {
            $xfer += $output->writeFieldBegin('data', TType::STRING, 2);
            $xfer += $output->writeString($this->data);
            $xfer += $output->writeFieldEnd();
        }
        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();

        return $xfer;
    }
}
