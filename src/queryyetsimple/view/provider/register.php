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
 * view.register 服务提供者
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
return [ 
        'singleton@view.compilers' => [ 
                'queryyetsimple\view\compilers',
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
                    
                    return new queryyetsimple\view\compilers ( $oProject, $arrOption );
                } 
        ],
        'singleton@view.parsers' => [ 
                'queryyetsimple\view\parsers',
                function ($oProject) {
                    return new queryyetsimple\view\parsers ( $oProject, $oProject ['option']->get ( 'view\\tag_note' ) );
                } 
        ],
        'singleton@view.theme' => [ 
                'queryyetsimple\view\theme',
                function ($oProject) {
                    return new queryyetsimple\view\theme ( $oProject, $oProject ['option']->get ( 'view\\cache_lifetime' ) );
                } 
        ] 
];
