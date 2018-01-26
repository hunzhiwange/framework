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
namespace Queryyetsimple\View\Provider;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Queryyetsimple\{
    Di\Provider,
    View\Parser,
    View\Manager,
    View\Compiler
};

/**
 * view 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
class Register extends Provider
{

    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->viewViews();
        $this->viewView();
        $this->viewCompiler();
        $this->viewParser();
        $this->viewTwigParser();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'view.views' => [
                'Queryyetsimple\View\Manager',
                'Qys\View\Manager'
            ],
            'view.view' => [
                'Queryyetsimple\View\View',
                'Queryyetsimple\View\IView',
                'Qys\View\View',
                'Qys\View\IView'
            ],
            'view.compiler' => [
                'Queryyetsimple\View\Compiler',
                'Queryyetsimple\View\ICompiler',
                'Qys\View\Compiler',
                'Qys\View\ICompiler'
            ],
            'view.parser' => [
                'Queryyetsimple\View\Parser',
                'Queryyetsimple\View\IParser',
                'Qys\View\Parser',
                'Qys\View\IParser'
            ],
            'view.twig.parser'
        ];
    }

    /**
     * 注册 view.views 服务
     *
     * @return void
     */
    protected function viewViews()
    {
        $this->singleton('view.views', function ($project) {
            return new Manager($project);
        });
    }

    /**
     * 注册 view.view 服务
     *
     * @return void
     */
    protected function viewView()
    {
        $this->singleton('view.view', function ($project) {
            return $project['view.views']->connect();
        });
    }

    /**
     * 注册 view.compiler 服务
     *
     * @return void
     */
    protected function viewCompiler()
    {
        $this->singleton('view.compiler', function ($project) {
            return new Compiler();
        });
    }

    /**
     * 注册 view.parser 服务
     *
     * @return void
     */
    protected function viewParser()
    {
        $this->singleton('view.parser', function ($project) {
            return (new Parser($project['view.compiler']))->registerCompilers()->registerParsers();
        });
    }

    /**
     * 注册 view.twig.parser 服务
     *
     * @return void
     */
    protected function viewTwigParser()
    {
        $this->singleton('view.twig.parser', function ($project) {
            return new Twig_Environment(new Twig_Loader_Filesystem(), [
                'auto_reload' => true,
                'debug' => $project->development(),
                'cache' => $project->pathApplicationCache('theme') . '/' . $project['app_name']
            ]);
        });
    }
}
