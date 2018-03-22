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
namespace Queryyetsimple\I18n\Streams;

/**
 * 数据流 File
 * This class borrows heavily from the Wordpress and is part of the Wordpress package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.18
 * @link https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 * @version 1.0
 */
class File extends Reader
{

    /**
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        parent::__construct();
        $this->_f = fopen($filename, 'rb');
    }

    /**
     *
     * @param int $bytes
     */
    public function read($bytes)
    {
        return fread($this->_f, $bytes);
    }

    /**
     *
     * @param int $pos
     * @return boolean
     */
    public function seekto($pos)
    {
        if (- 1 == fseek($this->_f, $pos, SEEK_SET)) {
            return false;
        }
        $this->_pos = $pos;
        return true;
    }

    /**
     *
     * @return bool
     */
    public function is_resource()
    {
        return is_resource($this->_f);
    }

    /**
     *
     * @return bool
     */
    public function feof()
    {
        return feof($this->_f);
    }

    /**
     *
     * @return bool
     */
    public function close()
    {
        return fclose($this->_f);
    }

    /**
     *
     * @return string
     */
    public function read_all()
    {
        $all = '';
        while (! $this->feof()) {
            $all .= $this->read(4096);
        }
        return $all;
    }
}
