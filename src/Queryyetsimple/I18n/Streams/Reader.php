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

namespace Leevel\I18n\Streams;

/**
 * 数据流
 * This class borrows heavily from the Wordpress and is part of the Wordpress package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.18
 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 *
 * @version 1.0
 */
class Reader
{
    /**
     * prop.
     *
     * @var string
     */
    public $endian = 'little';
    public $_post  = '';

    /**
     * PHP5 constructor.
     */
    public function __construct()
    {
        $this->is_overloaded = (0 !== (ini_get('mbstring.func_overload') & 2)) && function_exists('mb_substr');
        $this->_pos          = 0;
    }

    /**
     * Sets the endianness of the file.
     *
     * @param $endian string 'big' or 'little'
     */
    public function setEndian($endian)
    {
        $this->endian = $endian;
    }

    /**
     * Reads a 32bit Integer from the Stream.
     *
     * @return mixed The integer, corresponding to the next 32 bits from
     *               the stream of false if there are not enough bytes or on error
     */
    public function readint32()
    {
        $bytes = $this->read(4);
        if (4 !== $this->strlen($bytes)) {
            return false;
        }
        $endian_letter = ('big' === $this->endian) ? 'N' : 'V';
        $int           = unpack($endian_letter, $bytes);

        return reset($int);
    }

    /**
     * Reads an array of 32-bit Integers from the Stream.
     *
     * @param int count How many elements should be read
     * @param mixed $count
     *
     * @return mixed Array of integers or false if there isn't enough data or on error
     */
    public function readint32array($count)
    {
        $bytes = $this->read(4 * $count);
        if (4 * $count !== $this->strlen($bytes)) {
            return false;
        }
        $endian_letter = ('big' === $this->endian) ? 'N' : 'V';

        return unpack($endian_letter.$count, $bytes);
    }

    /**
     * @param string $string
     * @param int    $start
     * @param int    $length
     *
     * @return string
     */
    public function substr($string, $start, $length)
    {
        if ($this->is_overloaded) {
            return mb_substr($string, $start, $length, 'ascii');
        }

        return substr($string, $start, $length);
    }

    /**
     * @param string $string
     *
     * @return int
     */
    public function strlen($string)
    {
        if ($this->is_overloaded) {
            return mb_strlen($string, 'ascii');
        }

        return strlen($string);
    }

    /**
     * @param string $string
     * @param int    $chunk_size
     *
     * @return array
     */
    public function str_split($string, $chunk_size)
    {
        if (!function_exists('str_split')) {
            $length = $this->strlen($string);
            $out    = [];
            for ($i = 0; $i < $length; $i += $chunk_size) {
                $out[] = $this->substr($string, $i, $chunk_size);
            }

            return $out;
        }

        return str_split($string, $chunk_size);
    }

    /**
     * @return int
     */
    public function pos()
    {
        return $this->_pos;
    }

    /**
     * @return true
     */
    public function is_resource()
    {
        return true;
    }

    /**
     * @return true
     */
    public function close()
    {
        return true;
    }
}
