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
use queryyetsimple\view\itheme;

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
     * @var \queryyessimple\view\itheme
     */
    protected $objTheme;

    /**
     * 响应工厂
     *
     * @var \Closure
     */
    protected $calResponseFactory;

    /**
     * 响应
     *
     * @var \queryyetsimple\http\response
     */
    protected $objResponse;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\view\itheme $objTheme
     * @return void
     */
    public function __construct(itheme $objTheme)
    {
        $this->objTheme = $objTheme;
    }

    /**
     * 设置响应工厂
     *
     * @param \Closure $calResponseFactory
     * @return $this;
     */
    public function setResponseFactory(Closure $calResponseFactory)
    {
        $this->calResponseFactory = $calResponseFactory;
        return $this;
    }

    /**
     * 获取响应
     *
     * @return \queryyetsimple\http\response $objResponse
     */
    public function getResponse()
    {
        if (! $this->objResponse) {
            $this->objResponse = call_user_func($this->calResponseFactory);
        }
        return $this->objResponse;
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
        $this->checkTheme();
        $this->objTheme->setVar($mixName, $mixValue);
        return $this;
    }

    /**
     * 获取变量赋值
     *
     * @param string|null $sName
     * @return mixed
     */
    public function getAssign($sName = null)
    {
        $this->checkTheme();
        return $this->objTheme->getVar($sName);
    }

    /**
     * 删除变量值
     *
     * @param mixed $mixName
     * @return $this
     */
    public function deleteAssign($mixName)
    {
        $this->checkTheme();

        $arrArgs = func_get_args();
        $this->objTheme->deleteVar(...$arrArgs);

        return $this;
    }

    /**
     * 清空变量值
     *
     * @param string|null $sName
     * @return $this
     */
    public function clearAssign()
    {
        $this->checkTheme();
        $this->objTheme->clearVar();
        return $this;
    }

    /**
     * 加载视图文件
     *
     * @param string $sFile
     * @param array $arrOption
     * @sub string charset 编码
     * @sub string content_type 内容类型
     * @return string
     */
    public function display($sFile = null, array $arrOption = null)
    {
        $this->checkTheme();

        $arrOption = array_merge([
            'charset' => 'utf-8',
            'content_type' => 'text/html'
        ], $arrOption ?: []);

        $this->responseHeader($arrOption['content_type'], $arrOption['charset']);

        return $this->objTheme->display($sFile, false);
    }

    /**
     * 验证 theme
     *
     * @return void
     */
    protected function checkTheme()
    {
        if (! $this->objTheme) {
            throw new RuntimeException('Theme is not set in view');
        }
    }

    /**
     * 发送 header
     *
     * @param string $strContentType
     * @param string $strCharset
     * @return void
     */
    protected function responseHeader($strContentType = 'text/html', $strCharset = 'utf-8')
    {
        $this->getResponse();

        if (! $this->objResponse) {
            throw new RuntimeException('Response is not set in view');
        }

        $this->objResponse->contentType($strContentType)->charset($strCharset);
    }
}
