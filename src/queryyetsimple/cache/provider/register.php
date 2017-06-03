<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
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
 * cache 服务提供者
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.03
 * @version 1.0
 */
return [ 
        'singleton@cache' => [ 
                'queryyetsimple\cache\cache',
                function ($objProject) {
                    return \queryyetsimple\cache\cache::singleton ( $objProject );
                } 
        ],
        'singleton@filecache' => [ 
                'queryyetsimple\cache\filecache',
                function ($objProject, $arrOption = []) {
                    $objCache = new \queryyetsimple\cache\filecache ( $arrOption );
                    $objCache->projectContainer ( $objProject );
                    return $objCache;
                } 
        ],
        'singleton@memcache' => [ 
                'queryyetsimple\cache\memcache',
                function ($objProject, $arrOption = []) {
                    $objCache = new \queryyetsimple\cache\memcache ( $arrOption );
                    $objCache->projectContainer ( $objProject );
                    return $objCache;
                } 
        ] 
];
