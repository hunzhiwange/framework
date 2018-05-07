<?php declare(strict_types=1);
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\Mvc\Console;

use Leevel\Console\{
    Make,
    Option,
    Argument
};
use Leevel\Router;

/**
 * 生成方法器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.03
 * @version 1.0
 */
class Action extends Make
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'make:action';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $strDescription = 'Create a new action';

    /**
     * 命令帮助
     *
     * @var string
     */
    protected $strHelp = <<<EOF
The <info>%command.name%</info> command to make action with project namespace:

  <info>php %command.full_name% name action</info>

You can also by using the <comment>--namespace</comment> option:

  <info>php %command.full_name% name action --namespace=common</info>

You can also by using the <comment>--extend</comment> option:

  <info>php %command.full_name% name action --extend=0</info>
EOF;

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        // 处理命名空间路径
        $this->parseNamespace();

        // 设置模板路径
        $this->setTemplatePath(__DIR__ . '/template/' . ($this->option('extend') ? 'action' : 'action_without_extend'));

        $controllerNamespace = Router::getControllerDir();

        $action = ucfirst($this->normalizeAction($this->argument('name')));

        $this->setCustomReplaceKeyValue('controller_dir', $controllerNamespace);

        $this->setCustomReplaceKeyValue('controller', ucfirst($this->argument('controller')));

        $this->setCustomReplaceKeyValue('action', $action);

        // 保存路径
        $this->setSaveFilePath($this->getNamespacePath() . str_replace('\\', '/', $controllerNamespace) . '/' . $this->argument('controller') . '/' . $action . '.php');

        // 设置类型
        $this->setMakeType('action');

        // 执行
        parent::handle();
    }

    /**
     * 格式化方法名
     *
     * @param string $action
     * @return string
     */
    protected function normalizeAction(string $action)
    {
        if (strpos($action, '-') !== false) {
            $action = str_replace('-', '_', $action);
        } 

        if (strpos($action, '_') !== false) {
            $action = '_' . str_replace('_', ' ', $action);
            $action = ltrim(str_replace(' ', '', ucwords($action)), '_');
        }

        return $action;
    }

    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'controller',
                Argument::OPTIONAL,
                'This is the parent controller name.'
            ],
            [
                'name',
                Argument::OPTIONAL,
                'This is the action name.'
            ]
        ];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'namespace',
                null,
                option::VALUE_OPTIONAL,
                'Apps namespace registered to system,default namespace is these (Common,App,Admin)',
                'app'
            ],
            [
                'extend',
                null,
                option::VALUE_OPTIONAL,
                'Action with the code that make it extends Leevel\Mvc\Controller',
                1
            ]
        ];
    }
}
