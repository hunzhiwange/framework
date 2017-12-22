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
namespace queryyetsimple\bootstrap\validate;

use queryyetsimple\{
    http\response,
    session\isession,
    validate\ivalidate,
    validate\validate_failed,
    http\request as http_request
};

/**
 * 请求验证
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.25
 * @version 1.0
 */
trait request
{

    /**
     * 验证请求
     *
     * @param \queryyetsimple\http\request $oRequest
     * @param array $arrRule
     * @param array $arrMessage
     * @return void
     */
    public function validate(http_request $oRequest, array $arrRule, array $arrMessage = [])
    {
        $oValidate = $this->getValidateComponent()->make($oRequest->allAll(), $arrRule, $arrMessage);

        if ($oValidate->fail()) {
            return $this->throwValidateException($oRequest, $oValidate);
        }
    }

    /**
     * 验证失败异常
     *
     * @param \queryyetsimple\http\request $oRequest
     * @param \queryyetsimple\validate\ivalidate $oValidate
     * @return void
     */
    protected function throwValidateException(http_request $oRequest, $oValidate)
    {
        throw new validate_failed($oValidate, $this->validationResponse($oRequest, $this->validationErrors($oValidate)));
    }

    /**
     * 错误验证响应
     *
     * @param \queryyetsimple\http\request $oRequest
     * @param array $arrErrors
     * @return \queryyetsimple\http\response
     */
    protected function validationResponse(http_request $oRequest, array $arrErrors)
    {
        if ($oRequest->isAjax() && ! $oRequest->isPjax()) {
            return $this->getResponseComponent()/*->code ( 422 )*/->api($arrErrors);
        }

        return $this->getResponseComponent()->redirect($this->getRedirectUrl($oRequest), [
            'make' => false
        ])->clearErrors()->withErrors($arrErrors)->withInputs($oRequest->allAll());
    }

    /**
     * 返回错误消息
     *
     * @param \queryyetsimple\validate\ivalidate $oValidate
     * @return array
     */
    protected function validationErrors(ivalidate $oValidate)
    {
        return $oValidate->error();
    }

    /**
     * 返回前一个页面
     *
     *
     * @return string
     */
    protected function getRedirectUrl($oRequest)
    {
        return $oRequest->header('referer') ?  : $this->getSessionPrevUrl();
    }

    /**
     * 返回 session 前一个页面
     *
     * @return string
     */
    protected function getSessionPrevUrl()
    {
        return $this->getSessionComponent()->prevUrl();
    }

    /**
     * 返回 validate 组件
     *
     * @return \queryyetsimple\validate\ivalidate
     */
    protected function getValidateComponent()
    {
        return project(ivalidate::class);
    }

    /**
     * 返回 session 组件
     *
     * @return \queryyetsimple\session\isession
     */
    protected function getSessionComponent()
    {
        return project(isession::class);
    }

    /**
     * 返回响应组件
     *
     * @return \queryyetsimple\http\response
     */
    protected function getResponseComponent()
    {
        return project(response::class);
    }
}
