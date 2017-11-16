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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\filesystem;

use League\Flysystem\Sftp\SftpAdapter;

/**
 * filesystem.sftp
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/adapter/sftp/
 * @version 1.0
 */
class sftp extends aconnect implements iconnect
{
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        // 主机
        'host' => 'sftp.example.com', 
        
        // 端口
        'port' => 22, 
        
        // 用户名
        'username' => 'your-username', 
        
        // 密码
        'password' => 'your-password', 
        
        // 根目录
        'root' => '', 
        
        // 私钥路径
        'privateKey' => '', 
        
        // 超时设置
        'timeout' => 20
    ];
    
    /**
     * 创建连接
     *
     * @return \League\Flysystem\AdapterInterface
     */
    public function makeConnect()
    {
        if (! class_exists('League\Flysystem\Sftp\SftpAdapter')) {
            throw new InvalidArgumentException('Please run composer require league/flysystem-sftp');
        }
        
        return new SftpAdapter($this->getOptions());
    }
}
