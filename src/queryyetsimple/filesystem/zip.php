<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\filesystem;

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

use InvalidArgumentException;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * filesystem.zip
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/adapter/zip-archive/
 * @version 1.0
 */
class zip extends aconnect implements iconnect
{

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
            'path' => ''
    ];

    /**
     * 创建连接
     *
     * @return \League\Flysystem\AdapterInterface
     */
    public function makeConnect()
    {
        if (empty($this->getOption('path'))) {
            throw new InvalidArgumentException('The zip requires path option');
        }

        if (! class_exists('League\Flysystem\ZipArchive\ZipArchiveAdapter')) {
            throw new InvalidArgumentException('Please run composer require league/flysystem-ziparchive');
        }

        return new ZipArchiveAdapter($this->getOption('path'));
    }
}
