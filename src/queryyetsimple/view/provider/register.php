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
namespace queryyetsimple\view\provider;

use queryyetsimple\{
    view\parser,
    view\manager,
    view\compiler,
    support\provider
};

/**
 * view 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
class register extends provider
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
        $this->viewHtml();
        $this->viewV8();
        $this->viewPhpui();
        $this->viewCompiler();
        $this->viewParser();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'view.views' => 'queryyetsimple\view\manager',
            'view.view' => [
                'queryyetsimple\view\view',
                'queryyetsimple\view\iview'
            ],
            'view.html' => [
                'html',
                'queryyetsimple\view\html'
            ],
            'view.v8' => [
                'v8',
                'queryyetsimple\view\v8'
            ],
            'view.phpui' => [
                'phpui',
                'queryyetsimple\view\phpui'
            ],
            'view.compiler' => [
                'queryyetsimple\view\compiler',
                'queryyetsimple\view\icompiler'
            ],
            'view.parser' => [
                'queryyetsimple\view\parser',
                'queryyetsimple\view\iparser'
            ]
        ];
    }

    /**
     * 注册 view.views 服务
     *
     * @return void
     */
    protected function viewViews()
    {
        $this->singleton('view.views', function ($oProject) {
            return new manager($oProject);
        });
    }

    /**
     * 注册 view.html 服务
     *
     * @return void
     */
    protected function viewHtml()
    {
        $this->singleton('view.html', function ($oProject) {
            return $oProject['view.views']->connect('html');
        });
    }

    /**
     * 注册 view.v8 服务
     *
     * @return void
     */
    protected function viewV8()
    {
        $this->singleton('view.v8', function ($oProject) {
            return $oProject['view.views']->connect('v8');
        });
    }

    /**
     * 注册 view.phpui 服务
     *
     * @return void
     */
    protected function viewPhpui()
    {
        $this->singleton('view.phpui', function ($oProject) {
            return $oProject['view.views']->connect('phpui');
        });
    }

    /**
     * 注册 view.view 服务
     *
     * @return void
     */
    protected function viewView()
    {
        $this->singleton('view.view', function ($oProject) {
            return $oProject['view.views']->connect();
        });
    }

    /**
     * 注册 view.compiler 服务
     *
     * @return void
     */
    protected function viewCompiler()
    {
        $this->singleton('view.compiler', function ($oProject) {
            $arrOption = [];
            foreach ([
                'cache_children',
                'var_identify',
                'notallows_func',
                'notallows_func_js'
            ] as $strOption) {
                $arrOption[$strOption] = $oProject['option']->get('view\\' . $strOption);
            }

            return new compiler($arrOption);
        });
    }

    /**
     * 注册 view.parser 服务
     *
     * @return void
     */
    protected function viewParser()
    {
        $this->singleton('view.parser', function ($oProject) {
            return (new parser($oProject['view.compiler'], [
                'tag_note' => $oProject['option']->get('view\\tag_note')
            ]))->registerCompilers()->registerParsers();
        });
    }
}
