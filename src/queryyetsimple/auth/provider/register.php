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
 * auth.register 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.08
 * @version 1.0
 */
return [ 
        'singleton@auth' => [ 
                [ 
                        'queryyetsimple\auth\manager' 
                ],
                function ($oProject) {
                    return new queryyetsimple\auth\manager ( $oProject );
                } 
        ],
        'singleton@auth.connect' => [ 
                [ 
                        'queryyetsimple\auth\iconnect' 
                ],
                function ($oProject) {
                    return $oProject ['auth']->connect ();
                } 
        ] 
];
