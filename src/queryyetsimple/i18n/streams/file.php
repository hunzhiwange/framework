<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\i18n\streams;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

/**
 * 数据流 file
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.18
 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/pomo/
 * @version 1.0
 */
class file extends reader
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
