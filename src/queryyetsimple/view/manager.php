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
     * @param object $connect
     * @return object
     */
    protected function createConnect($connect)
    {
        return new view($connect);
    }

    /**
     * 创建 html 模板驱动
     *
     * @param array $options
     * @return \queryyetsimple\view\html
     */
    protected function makeConnectHtml($options = [])
    {
        $options = $this->getOption('html', $options);
        $options = array_merge($options, $this->viewOptionCommon());

        $container = $this->container;
        
        html::setParseResolver(function () use ($container) {
            return $container['view.parser'];
        });

        return new html($options);
    }

    /**
     * 创建 phpui 模板驱动
     *
     * @param array $options
     * @return \queryyetsimple\view\phpui
     */
    protected function makeConnectPhpui($options = [])
    {
        $options = $this->getOption('phpui', $options);
        $options = array_merge($options, $this->viewOptionCommon());
        return new phpui($options);
    }

    /**
     * 创建 v8 模板驱动
     *
     * @param array $options
     * @return \queryyetsimple\view\vue
     */
    protected function makeConnectV8($options = [])
    {
        $options = $this->getOption('v8', $options);
        $options = array_merge($options, $this->viewOptionCommon());
        return new v8($options);
    }

    /**
     * 视图公共配置
     *
     * @return array
     */
    protected function viewOptionCommon()
    {
        $options = [
            'development' => $this->container->development(),
            'controller_name' => $this->container['controller_name'],
            'action_name' => $this->container['action_name'],
            'theme_path' => $this->container->pathApplicationDir('theme') . '/' . $this->container['option']['view\theme_name'],

            // 仅 html 模板需要缓存路径
            'theme_cache_path' => $this->container->pathApplicationCache('theme') . '/' . $this->container['app_name']
        ];

        return $options;
    }
}
