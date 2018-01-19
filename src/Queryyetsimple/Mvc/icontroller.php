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
namespace queryyetsimple\mvc;

use queryyetsimple\router\router;

/**
 * icontroller 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface icontroller extends iview
{

    /**
     * 返回父控制器
     *
     * @param \queryyetsimple\mvc\iview $view
     * @return $this
     */
    public function setView(iview $view);

    /**
     * 返回父控制器
     *
     * @param \queryyetsimple\router\router $router
     * @return $this
     */
    public function setRouter(router $router);

    /**
     * 执行子方法器
     *
     * @param string $action 方法名
     * @return void
     */
    public function action($action);
}
