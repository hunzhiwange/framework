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

use Closure;
use RuntimeException;
use queryyetsimple\view\iview as view_iview;

/**
 * 视图
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class view implements iview
{

    /**
     * 视图模板
     *
     * @var \queryyessimple\view\iview
     */
    protected $theme;

    /**
     * 备份视图模板
     *
     * @var \queryyessimple\view\iview
     */
    protected $backupTheme;

    /**
     * 是否永久切换
     *
     * @var boolean
     */
    protected $foreverSwitch = false;

    /**
     * 响应工厂
     *
     * @var \Closure
     */
    protected $responseFactory;

    /**
     * 响应
     *
     * @var \queryyetsimple\http\response
     */
    protected $response;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\view\iview $theme
     * @return void
     */
    public function __construct(view_iview $theme)
    {
        $this->theme = $theme;
    }

    /**
     * 切换视图
     *
     * @param \queryyetsimple\view\iview $theme
     * @param boolean $foreverSwitch
     * @return $this
     */
    public function switchView(view_iview $theme, bool $foreverSwitch = false)
    {
        $assign = $this->getAssign();

        if ($foreverSwitch === false) {
            $this->backupTheme = $this->theme;
        }
        
        $this->foreverSwitch = $foreverSwitch;
        $this->theme = $theme;
        $this->assign($assign);

        return $this;
    }

    /**
     * 设置响应工厂
     *
     * @param \Closure $responseFactory
     * @return $this;
     */
    public function setResponseFactory(Closure $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        return $this;
    }

    /**
     * 获取响应
     *
     * @return \queryyetsimple\http\response $response
     */
    public function getResponse()
    {
        if (! $this->response) {
            $this->response = call_user_func($this->responseFactory);
        }
        return $this->response;
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
        $this->checkTheme();
        $this->theme->setVar($name, $value);
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
        $this->checkTheme();
        return $this->theme->getVar($name);
    }

    /**
     * 删除变量值
     *
     * @param mixed $name
     * @return $this
     */
    public function deleteAssign($name)
    {
        $this->checkTheme();

        $args = func_get_args();
        $this->theme->deleteVar(...$args);

        return $this;
    }

    /**
     * 清空变量值
     *
     * @param string|null $name
     * @return $this
     */
    public function clearAssign()
    {
        $this->checkTheme();
        $this->theme->clearVar();
        return $this;
    }

    /**
     * 加载视图文件
     *
     * @param string $file
     * @param array $option
     * @sub string charset 编码
     * @sub string content_type 内容类型
     * @return string
     */
    public function display($file = null, array $option = null)
    {
        $this->checkTheme();

        $option = array_merge([
            'charset' => 'utf-8',
            'content_type' => 'text/html'
        ], $option ?: []);

        $this->responseHeader($option['content_type'], $option['charset']);

        $result = $this->theme->display($file, false);

        if ($this->foreverSwitch === false) {
            $this->theme = $this->backupTheme;
        }
        $this->foreverSwitch = false;

        return $result;
    }

    /**
     * 验证 theme
     *
     * @return void
     */
    protected function checkTheme()
    {
        if (! $this->theme) {
            throw new RuntimeException('Theme is not set in view');
        }
    }

    /**
     * 发送 header
     *
     * @param string $contentType
     * @param string $charset
     * @return void
     */
    protected function responseHeader($contentType = 'text/html', $charset = 'utf-8')
    {
        $this->getResponse();

        if (! $this->response) {
            throw new RuntimeException('Response is not set in view');
        }

        $this->response->contentType($contentType)->charset($charset);
    }
}
