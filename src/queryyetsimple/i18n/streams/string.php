<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\i18n\streams;

/**
 * 数据流 string
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.18
 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 * @version 1.0
 */
class string extends reader
{

    /**
     * prop
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
     *
     * @param string $bytes
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
     *
     * @param int $pos
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
     *
     * @return int
     */
    public function length()
    {
        return $this->strlen($this->_str);
    }

    /**
     *
     * @return string
     */
    public function read_all()
    {
        return $this->substr($this->_str, $this->_pos, $this->strlen($this->_str));
    }
}
