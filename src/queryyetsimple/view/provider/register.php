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

use queryyetsimple\view\theme;
use queryyetsimple\view\parser;
use queryyetsimple\view\compiler;
use queryyetsimple\view\phpui_theme;
use queryyetsimple\support\provider;

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
        $this->viewCompiler();
        $this->viewParser();
        $this->viewTheme();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'view.compiler' => [
                'queryyetsimple\view\compiler',
                'queryyetsimple\view\icompiler'
            ],
            'view.parser' => [
                'queryyetsimple\view\parser',
                'queryyetsimple\view\iparser'
            ],
            'view.theme' => [
                'queryyetsimple\view\theme',
                'queryyetsimple\view\itheme'
            ]
        ];
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

    /**
     * 注册 view.theme 服务
     *
     * @return void
     */
    protected function viewTheme()
    {
        $this->singleton('view.theme', function ($oProject) {
            $arrOption = [];
            foreach ([
                'cache_lifetime',
                'suffix',
                'controlleraction_depr',
                'cache_children',
                'switch',
                'default',
                'cookie_app',
                'theme_path_default'
            ] as $strOption) {
                $arrOption[$strOption] = $oProject['option']->get('view\\' . $strOption);
            }

            $arrOption['app_development'] = $oProject->development();
            $arrOption['app_name'] = $oProject['app_name'];
            $arrOption['controller_name'] = $oProject['controller_name'];
            $arrOption['action_name'] = $oProject['action_name'];
            $arrOption['theme_cache_path'] = $oProject->pathApplicationCache('theme') . '/' . $oProject['app_name'];

            if (env('app_mode', false) == 'phpui') {
                $arrOption['suffix'] = '.php';
                $oTheme = new phpui_theme($oProject['cookie'], $arrOption);
            } else {
                theme::setParseResolver(function () use ($oProject) {
                    return $oProject['view.parser'];
                });
                $oTheme = new theme($oProject['cookie'], $arrOption);
            }

            return $oTheme->parseContext($oProject->pathApplicationDir('theme'));
        });
    }
}
