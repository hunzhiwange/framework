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
 * 框架引导文件
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2016.11.17
 * @version 1.0
 */
if (version_compare ( PHP_VERSION, '5.5.0', '<' ))
    die ( 'PHP 5.5.0 OR Higher' );

if (defined ( 'Q_VER' ))
    return;

ini_set ( 'default_charset', 'utf8' );

/**
 * QueryPHP 路径定义
 */
define ( 'Q_PATH', dirname ( __DIR__ ) );

/**
 * QueryPHP 调试
 */
defined ( 'Q_DEBUG' ) or define ( 'Q_DEBUG', false );

/**
 * QueryPHP 开发环境
 * 开发模式 = development、测试环境 = testing、生产环境 production
 */
defined ( 'Q_DEVELOPMENT' ) or define ( 'Q_DEVELOPMENT', 'production' );

/**
 * QueryPHP 是否命令行工具模式
 */
defined ( 'Q_CONSOLE' ) or define ( 'Q_CONSOLE', false );
defined ( 'Q_PHPUNIT' ) or define ( 'Q_PHPUNIT', false );
defined ( 'Q_PHPUNIT_SYSTEM' ) or define ( 'Q_PHPUNIT_SYSTEM', false );

/**
 * QueryPHP 版本 | 2017.03.31
 */
define ( 'Q_VER', '4.0' );

/**
 * QueryPHP 自动载入
 */
require Q_PATH . '/psr4/psr4.php';
spl_autoload_register ( [ 
        'queryyetsimple\psr4\psr4',
        'autoload' 
] );

/**
 * QueryPHP 注册框架命名空间
 */
queryyetsimple\psr4\psr4::import ( 'queryyetsimple', Q_PATH, [ 
        'ignore' => [ 
                'resource' 
        ],
        'force' => Q_DEVELOPMENT === 'development' 
] );

/**
 * 导入 composer
 */
if (! is_file ( ($strComposer = Q_PATH . '/../../../../autoload.php') ))
    die ( 'We Need Composer' );
queryyetsimple\psr4\psr4::composer ( require $strComposer );

/**
 * QueryPHP 系统错误处理
 */
if (PHP_SAPI != 'cli') {
    set_error_handler ( [ 
            'queryyetsimple\exception\handle',
            'errorHandle' 
    ] );
    
    register_shutdown_function ( [ 
            'queryyetsimple\exception\handle',
            'shutdownHandle' 
    ] );
}
