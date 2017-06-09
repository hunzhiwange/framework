<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\bootstrap\console;

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

use queryyetsimple\mvc\project;
use queryyetsimple\option\option;
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * 命令行应用程序
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.28
 * @version 1.0
 */
class application {
    
    /**
     * 项目容器
     *
     * @var \queryyetsimple\mvc\project
     */
    protected $objProject;
    
    /**
     * symfony application
     *
     * @var object
     */
    private $objSymfonyApplication = null;
    
    /**
     * 创建一个命令行应用程序
     *
     * @param \queryyetsimple\mvc\project $objProject            
     * @return $this
     */
    public function __construct(project $objProject) {
        $this->objProject = $objProject;
        
        // 创建应用
        $this->objSymfonyApplication = new SymfonyApplication ( $this->getLogo (), env ( 'queryphp_version' ) );
        
        // 注册默认命令行
        $this->registerDefaultCommands ()->
        
        // 注册用户自定义命令
        registerUserCommands ();
    }
    
    /**
     * 默认方法
     *
     * @return void
     */
    public function run() {
        return $this->objSymfonyApplication->run ();
    }
    
    /**
     * 注册默认命令行
     *
     * @return $this
     */
    private function registerDefaultCommands() {
        return $this->doRegisterCommands ( ( array ) require __DIR__ . '/default.php' );
    }
    
    /**
     * 注册默认命令行
     *
     * @return $this
     */
    private function registerUserCommands() {
        return $this->doRegisterCommands ( ( array ) option::gets ( 'console' ) );
    }
    
    /**
     * 注册用户自定义命令
     *
     * @param array $arrCommands            
     * @return $this
     */
    private function doRegisterCommands($arrCommands) {
        foreach ( $arrCommands as $strCommand ) {
            $objCommand = $this->objProject->make ( $strCommand );
            // 基于 Phinx 数据库迁移组件无法设置 setContainer
            if (method_exists ( $objCommand, 'project' )) {
                $objCommand->project ( $this->objProject );
            }
            $this->objProject->instance ( 'command_' . $objCommand->getName (), $objCommand );
            $this->objSymfonyApplication->add ( $objCommand );
        }
        return $this;
    }
    
    /**
     * 返回 QueryPHP Logo
     *
     * @return string
     */
    private function getLogo() {
        return <<<queryphp
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
    }
}
