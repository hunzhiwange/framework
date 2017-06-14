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
 * router.register 服务提供者
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
return [ 
        'singleton@router' => [ 
                'queryyetsimple\router\router',
                function ($oProject) {
                    $arrOption = [ ];
                    foreach ( [ 
                            'router_cache',
                            'model',
                            'router_domain_on',
                            'html_suffix',
                            'router_domain_top',
                            'make_subdomain_on',
                            'pathinfo_depr',
                            'rewrite',
                            'public' 
                    ] as $strOption ) {
                        $arrOption [$strOption] = $oProject ['option']->get ( 'url\\' . $strOption );
                    }
                    
                    foreach ( [ 
                            '~apps~',
                            'default_app',
                            'default_controller',
                            'default_action' 
                    ] as $strOption ) {
                        $arrOption [$strOption] = $oProject ['option']->get ( $strOption );
                    }
                    return (new queryyetsimple\router\router ( $oProject, $arrOption ))->registerRequest ( $oProject ['request'] );
                } 
        ] 
];
