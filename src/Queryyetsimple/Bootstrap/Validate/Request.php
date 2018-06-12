<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Bootstrap\validate;

use Leevel\Http\Response;
use Leevel\Session\ISession;
use Leevel\Validate\IValidate;
use Leevel\Validate\ValidateException;
use Leevel\Http\Request as HttpRequest;

/**
 * 请求验证
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.25
 *
 * @version 1.0
 */
trait Request
{
    /**
     * 验证请求
     *
     * @param \Leevel\Http\Request $oRequest
     * @param array                $arrRule
     * @param array                $arrMessage
     */
    public function validate(HttpRequest $oRequest, array $arrRule, array $arrMessage = [])
    {
        $oValidate = $this->getValidateComponent()->make($oRequest->allAll(), $arrRule, $arrMessage);

        if ($oValidate->fail()) {
            return $this->throwValidateException($oRequest, $oValidate);
        }
    }

    /**
     * 验证失败异常.
     *
     * @param \Leevel\Http\Request       $oRequest
     * @param \Leevel\Validate\IValidate $oValidate
     */
    protected function throwValidateException(HttpRequest $oRequest, $oValidate)
    {
        throw new ValidateException($oValidate, $this->validationResponse($oRequest, $this->validationErrors($oValidate)));
    }

    /**
     * 错误验证响应.
     *
     * @param \Leevel\Http\Request $oRequest
     * @param array                $arrErrors
     *
     * @return \Leevel\Http\Response
     */
    protected function validationResponse(HttpRequest $oRequest, array $arrErrors)
    {
        if ($oRequest->isAjax() && ! $oRequest->isPjax()) {
            return $this->getResponseComponent()->api($arrErrors);
        }

        return $this->

        getResponseComponent()->

        redirect($this->getRedirectUrl($oRequest), [
            'make' => false,
        ])->

        clearErrors()->

        withErrors($arrErrors)->

        withInputs($oRequest->allAll());
    }

    /**
     * 返回错误消息.
     *
     * @param \Leevel\Validate\IValidate $oValidate
     *
     * @return array
     */
    protected function validationErrors(IValidate $oValidate)
    {
        return $oValidate->error();
    }

    /**
     * 返回前一个页面.
     *
     *
     * @return string
     */
    protected function getRedirectUrl($oRequest)
    {
        return $oRequest->header('referer') ?: $this->getSessionPrevUrl();
    }

    /**
     * 返回 session 前一个页面.
     *
     * @return string
     */
    protected function getSessionPrevUrl()
    {
        return $this->getSessionComponent()->prevUrl();
    }

    /**
     * 返回 validate 组件.
     *
     * @return \Leevel\Validate\IValidate
     */
    protected function getValidateComponent()
    {
        return project(IValidate::class);
    }

    /**
     * 返回 session 组件.
     *
     * @return \Leevel\Session\ISession
     */
    protected function getSessionComponent()
    {
        return project(ISession::class);
    }

    /**
     * 返回响应组件.
     *
     * @return \Leevel\Http\Response
     */
    protected function getResponseComponent()
    {
        return project(response::class);
    }
}
