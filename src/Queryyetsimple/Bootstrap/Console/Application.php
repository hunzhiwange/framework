<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Bootstrap\console;

use Queryyetsimple\{
    Option,
    Bootstrap\Project
};
use Symfony\Component\Console\Application as SymfonyApplication;

/**
 * 命令行应用程序
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.28
 * @version 1.0
 */
class Application
{

    /**
     * 项目容器
     *
     * @var \Queryyetsimple\Bootstrap\Project
     */
    protected $objProject;

    /**
     * symfony application
     *
     * @var object
     */
    protected $objSymfonyApplication;

    /**
     * 创建一个命令行应用程序
     *
     * @param \Queryyetsimple\Bootstrap\Project $objProject
     * @return $this
     */
    public function __construct(project $objProject)
    {
        $this->objProject = $objProject;

        // 创建应用
        $this->objSymfonyApplication = new SymfonyApplication($this->getLogo() . PHP_EOL, $this->objProject->version());

        // 注册默认命令行
        $this->registerLoadCommands()->

        // 注册用户自定义命令
        registerUserCommands();
    }

    /**
     * 默认方法
     *
     * @return void
     */
    public function run()
    {
        return $this->objSymfonyApplication->run();
    }

    /**
     * 注册载入命令行
     *
     * @return $this
     */
    protected function registerLoadCommands()
    {
        return $this->doRegisterCommands($this->objProject['console.load']->loadData());
    }

    /**
     * 注册默认命令行
     *
     * @return $this
     */
    protected function registerUserCommands()
    {
        return $this->doRegisterCommands(( array ) option::get('console'));
    }

    /**
     * 注册用户自定义命令
     *
     * @param array $arrCommands
     * @return $this
     */
    protected function doRegisterCommands($arrCommands)
    {
        foreach ($arrCommands as $strCommand) {
            $objCommand = $this->objProject->make($strCommand);
            
            // 基于 Phinx 数据库迁移组件无法设置 setContainer
            if (method_exists($objCommand, 'project')) {
                $objCommand->project($this->objProject);
            }

            $this->objProject->instance('command_' . $objCommand->getName(), $objCommand);
            $this->objSymfonyApplication->add($objCommand);
        }
        return $this;
    }

    /**
     * 返回 QueryPHP Logo
     *
     * @return string
     */
    protected function getLogo()
    {
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
