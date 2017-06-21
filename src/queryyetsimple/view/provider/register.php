<?php
// [$QueryPHP] A PHP Framework For Simple As Free As Wind. <Query Yet Simple>
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
 * view.register 服务提供者
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
return [ 
        'singleton@view.compiler' => [ 
                'queryyetsimple\view\compiler',
                function ($oProject) {
                    $arrOption = [ ];
                    foreach ( [ 
                            'cache_children',
                            'var_identify',
                            'notallows_func',
                            'notallows_func_js' 
                    ] as $strOption ) {
                        $arrOption [$strOption] = $oProject ['option']->get ( 'view\\' . $strOption );
                    }
                    
                    return new queryyetsimple\view\compiler ($arrOption );
                } 
        ],
        'singleton@view.parser' => [ 
                'queryyetsimple\view\parser',
                function ($oProject) {
                    return (new queryyetsimple\view\parser ( [ 
                            'tag_note' => $oProject ['option']->get ( 'view\\tag_note' ) 
                    ] ))->registerCompilers ( $oProject ['view.compiler'] )->registerParsers ();
                } 
        ],
        'singleton@view.theme' => [ 
                'queryyetsimple\view\theme',
                function ($oProject) {
                    $arrOption = [ ];
                    foreach ( [ 
                            'cache_lifetime',
                            'suffix',
                            'controlleraction_depr',
                            'cache_children',
                            'switch',
                            'default',
                            'cookie_app' 
                    ] as $strOption ) {
                        $arrOption [$strOption] = $oProject ['option']->get ( 'view\\' . $strOption );
                    }
                    
                    $arrOption ['app_debug'] = env ( 'app_debug' );
                    $arrOption ['app_name'] = $oProject ['app_name'];
                    $arrOption ['controller_name'] = $oProject ['controller_name'];
                    $arrOption ['action_name'] = $oProject ['action_name'];
                    $arrOption ['theme_path_default'] = $oProject ['path_app_theme_extend'];
                    $arrOption ['theme_cache_path'] = $oProject ['path_cache_theme'];
                    
                    return (new queryyetsimple\view\theme ( $arrOption ))->registerParser ( $oProject ['view.parser'] )->registerCookie ( $oProject ['cookie'] )->parseContext ( $oProject ['path_app_theme'] );
                } 
        ] 
];
