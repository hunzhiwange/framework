<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
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
 * stack 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.25
 * @version 1.0
 */
return [ 
        'register@linkedlist' => [ 
                'queryyetsimple\stack\linkedlist',
                function ($oProject) {
                    $arrArgs = func_get_args ();
                    array_shift ( $arrArgs );
                    return new queryyetsimple\stack\linkedlist ( $arrArgs );
                } 
        ],
        'register@stack' => [ 
                'queryyetsimple\stack\stack',
                function ($oProject) {
                    $arrArgs = func_get_args ();
                    array_shift ( $arrArgs );
                    return new queryyetsimple\stack\stack ( $arrArgs );
                } 
        ],
        'register@queues' => [ 
                'queryyetsimple\stack\queue',
                function ($oProject) {
                    $arrArgs = func_get_args ();
                    array_shift ( $arrArgs );
                    return new queryyetsimple\stack\queue ( $arrArgs );
                } 
        ] 
];
