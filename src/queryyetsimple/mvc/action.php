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

use RuntimeException;
use BadFunctionCallException;
use queryyetsimple\router\router;
use queryyetsimple\view\iview as view_iview;

/**
 * 基类方法器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
abstract class action implements iaction
{

    /**
     * 父控制器
     *
     * @var \queryyetsimple\mvc\icontroller
     */
    protected $objController;

    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * 设置父控制器
     *
     * @param \queryyetsimple\mvc\icontroller $objController
     * @return $this
     */
    public function setController(icontroller $objController)
    {
        $this->objController = $objController;
        return $this;
    }

    /**
     * 设置视图
     *
     * @param \queryyetsimple\mvc\iview $objView
     * @return $this
     */
    public function setView(iview $objView)
    {
        $this->checkController();
        return $this->objController->setView($objView);
    }

    /**
     * 设置路由
     *
     * @param \queryyetsimple\router\router $objRouter
     * @return $this
     */
    public function setRouter(router $objRouter)
    {
        $this->checkController();
        return $this->objController->setRouter($objRouter);
    }

    /**
     * 执行子方法器
     *
     * @param string $sActionName 方法名
     * @return void
     */
    public function action($sActionName)
    {
        $this->checkController();
        return $this->objController->action($sActionName);
    }

    /**
     * 切换视图
     *
     * @param \queryyetsimple\view\iview $objTheme
     * @param boolean $booForever
     * @return $this
     */
    public function switchView(view_iview $objTheme, bool $booForever = false)
    {
        $this->checkController();
        $this->objController->switchView($objTheme, $booForever);
        return $this;
    }

    /**
     * 变量赋值
     *
     * @param mixed $mixName
     * @param mixed $mixValue
     * @return $this
     */
    public function assign($mixName, $mixValue = null)
    {
        $this->checkController();
        return $this->objController->assign($mixName, $mixValue);
    }

    /**
     * 获取变量赋值
     *
     * @param string|null $sName
     * @return mixed
     */
    public function getAssign($sName = null)
    {
        $this->checkController();
        return $this->objController->getAssign($sName);
    }

    /**
     * 删除变量值
     *
     * @param mixed $mixName
     * @return $this
     */
    public function deleteAssign($mixName)
    {
        $this->checkController();

        $arrArgs = func_get_args();
        $this->objController->{'deleteAssign'}(...$arrArgs);

        return $this;
    }

    /**
     * 清空变量值
     *
     * @return $this
     */
    public function clearAssign()
    {
        $this->checkController();
        $this->objController->clearAssign();
        return $this;
    }

    /**
     * 加载视图文件
     *
     * @param string $sFile
     * @param array $arrOption
     * @sub string charset 编码
     * @sub string content_type 类型
     * @return string
     */
    public function display($sFile = null, array $arrOption = null)
    {
        $this->checkController();
        return $this->objController->display($sFile, $arrOption);
    }

    /**
     * 验证 controller
     *
     * @return void
     */
    protected function checkController()
    {
        if (! $this->objController) {
            throw new RuntimeException('Controller is not set in action');
        }
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        if ($sMethod == 'run') {
            throw new BadFunctionCallException(sprintf('Run method is not allowed.'));
        }
        throw new BadFunctionCallException(sprintf('Method %s is not defined.', $sMethod));
    }
}
