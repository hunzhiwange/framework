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
 * 数据流 Str
 * This class borrows heavily from the Wordpress and is part of the Wordpress package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.18
 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 *
 * @version 1.0
 */
class Str extends Reader
{
    /**
     * prop.
     *
     * @var string
     */
    public $_str = '';

    /**
     * PHP5 constructor.
     */
    public function __construct($str = '')
    {
        parent::__construct();
        $this->_str = $str;
        $this->_pos = 0;
    }

    /**
     * @param string $bytes
     *
     * @return string
     */
    public function read($bytes)
    {
        $data = $this->substr($this->_str, $this->_pos, $bytes);
        $this->_pos += $bytes;
        if ($this->strlen($this->_str) < $this->_pos) {
            $this->_pos = $this->strlen($this->_str);
        }

        return $data;
    }

    /**
     * @param int $pos
     *
     * @return int
     */
    public function seekto($pos)
    {
        $this->_pos = $pos;
        if ($this->strlen($this->_str) < $this->_pos) {
            $this->_pos = $this->strlen($this->_str);
        }

        return $this->_pos;
    }

    /**
     * @return int
     */
    public function length()
    {
        return $this->strlen($this->_str);
    }

    /**
     * @return string
     */
    public function read_all()
    {
        return $this->substr($this->_str, $this->_pos, $this->strlen($this->_str));
    }
}
