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
namespace queryyetsimple\bootstrap\runtime;

/**
 * 致命错误消息
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.04
 * @version 1.0
 */
class shutdown extends message
{
    
    /**
     * 构造函数
     *
     * @param \queryyetsimple\bootstrap\project $oProject
     * @return void
     */
    public function __construct($oProject)
    {
        $this->oProject = $oProject;
        if (($arrError = error_get_last()) && ! empty($arrError['type'])) {
            $this->strMessage = "[{$arrError['type']}]: {$arrError['message']} <br> File: {$arrError['file']} <br> Line: {$arrError['line']}";
        }
    }
}
