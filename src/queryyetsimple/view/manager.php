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
namespace queryyetsimple\view;

use queryyetsimple\support\manager as support_manager;

/**
 * view 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.01.10
 * @version 1.0
 */
class manager extends support_manager
{

    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace()
    {
        return 'view';
    }

    /**
     * 创建连接对象
     *
     * @param object $objConnect
     * @return object
     */
    protected function createConnect($objConnect)
    {
        return new view($objConnect);
    }

    /**
     * 创建 html 模板驱动
     *
     * @param array $arrOption
     * @return \queryyetsimple\view\html
     */
    protected function makeConnectHtml($arrOption = [])
    {
        $arrOption = $this->getOption('html', $arrOption);
        $arrOption = array_merge($arrOption, $this->viewOptionCommon());

        $oProject = $this->objContainer;
        
        html::setParseResolver(function () use ($oProject) {
            return $oProject['view.parser'];
        });

        return new html($arrOption);
    }

    /**
     * 创建 phpui 模板驱动
     *
     * @param array $arrOption
     * @return \queryyetsimple\view\phpui
     */
    protected function makeConnectPhpui($arrOption = [])
    {
        $arrOption = $this->getOption('phpui', $arrOption);
        $arrOption = array_merge($arrOption, $this->viewOptionCommon());
        return new phpui($arrOption);
    }

    /**
     * 创建 v8 模板驱动
     *
     * @param array $arrOption
     * @return \queryyetsimple\view\vue
     */
    protected function makeConnectV8($arrOption = [])
    {
        $arrOption = $this->getOption('v8', $arrOption);
        $arrOption = array_merge($arrOption, $this->viewOptionCommon());
        return new v8($arrOption);
    }

    /**
     * 视图公共配置
     *
     * @return array
     */
    protected function viewOptionCommon()
    {
        $arrOption = [
            'development' => $this->objContainer->development(),
            'controller_name' => $this->objContainer['controller_name'],
            'action_name' => $this->objContainer['action_name'],
            'theme_path' => $this->objContainer->pathApplicationDir('theme') . '/' . $this->objContainer['option']['view\theme_name'],

            // 仅 html 模板需要缓存路径
            'theme_cache_path' => $this->objContainer->pathApplicationCache('theme') . '/' . $this->objContainer['app_name']
        ];

        return $arrOption;
    }
}
