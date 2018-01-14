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
use queryyetsimple\router\router;
use queryyetsimple\view\iview as view_iview;

/**
 * 基类控制器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
abstract class controller implements icontroller
{

    /**
     * 视图
     *
     * @var \queryyetsimple\mvc\iview
     */
    protected $view;

    /**
     * 视图
     *
     * @var \queryyetsimple\router\router
     */
    protected $router;

    /**
     * 构造函数
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * 设置视图
     *
     * @param \queryyetsimple\mvc\iview $view
     * @return $this
     */
    public function setView(iview $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * 设置路由
     *
     * @param \queryyetsimple\router\router $router
     * @return $this
     */
    public function setRouter(router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * 执行子方法器
     *
     * @param string $action 方法名
     * @return void
     */
    public function action($action)
    {
        // 判断是否存在方法
        if (method_exists($this, $action)) {
            $args = func_get_args();
            array_shift($args);

            return call_user_func_array([
                $this,
                $action
            ], $args);
        }

        // 执行默认方法器
        if (! $this->router) {
            throw new RuntimeException('Router is not set in controller');
        }
        return $this->router->doBind(null, $action, null, true);
    }

    /**
     * 切换视图
     *
     * @param \queryyetsimple\view\iview $theme
     * @param boolean $forever
     * @return $this
     */
    public function switchView(view_iview $theme, bool $forever = false)
    {
        $this->checkView();
        $this->view->switchView($theme, $forever);
        return $this;
    }

    /**
     * 变量赋值
     *
     * @param mixed $name
     * @param mixed $value
     * @return $this
     */
    public function assign($name, $value = null)
    {
        $this->checkView();
        $this->view->assign($name, $value);
        return $this;
    }

    /**
     * 获取变量赋值
     *
     * @param string|null $name
     * @return mixed
     */
    public function getAssign($name = null)
    {
        $this->checkView();
        return $this->view->getAssign($name);
    }

    /**
     * 删除变量值
     *
     * @param mixed $name
     * @return $this
     */
    public function deleteAssign($name)
    {
        $this->checkView();

        call_user_func_array([
            $this->view,
            'deleteAssign'
        ], func_get_args());
        
        return $this;
    }

    /**
     * 清空变量值
     *
     * @return $this
     */
    public function clearAssign()
    {
        $this->checkView();
        $this->view->clearAssign();
        return $this;
    }

    /**
     * 加载视图文件
     *
     * @param string $file
     * @param array $option
     * @sub string charset 编码
     * @sub string content_type 类型
     * @return string
     */
    public function display($file = null, array $option = null)
    {
        $this->checkView();
        return $this->view->display($file, $option);
    }

    /**
     * 验证 view
     *
     * @return void
     */
    protected function checkView()
    {
        if (! $this->view) {
            throw new RuntimeException('View is not set in controller');
        }
    }

    /**
     * 赋值
     *
     * @param mixed $name
     * @param mixed $Value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->assign($name, $value);
    }

    /**
     * 获取值
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getAssign($name);
    }
}
