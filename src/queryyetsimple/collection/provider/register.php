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
 * collection 服务提供者
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.06.03
 * @version 1.0
 */
return [ 
        'register@collection' => [ 
                'queryyetsimple\collection\collection',
                function ($oProject /* args */ ) {
                    $arrArgs = func_get_args ();
                    array_shift ( $arrArgs );
                    return (new ReflectionClass ( 'queryyetsimple\collection\collection' ))->newInstanceArgs ( $arrArgs );
                } 
        ] 
];
