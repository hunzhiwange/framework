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

namespace Leevel\Bootstrap\Validate;

use Leevel\Http\Request as HttpRequest;
use Leevel\Http\Response;
use Leevel\Session\ISession;
use Leevel\Validate\IValidate;
use Leevel\Validate\ValidateException;

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
     * @param \Leevel\Http\Request $request
     * @param array                $rules
     * @param array                $messages
     */
    public function validate(HttpRequest $request, array $rules, array $messages = [])
    {
        $validate = $this->getValidateComponent()->

        make($request->allAll(), $rules, $messages);

        if ($validate->fail()) {
            return $this->throwValidateException($request, $validate);
        }
    }

    /**
     * 验证失败异常.
     *
     * @param \Leevel\Http\Request       $request
     * @param \Leevel\Validate\IValidate $validate
     */
    protected function throwValidateException(HttpRequest $request, $validate)
    {
        throw new ValidateException(
            $validate,
            $this->validationResponse(
                $request, $this->validationErrors($validate)
            )
        );
    }

    /**
     * 错误验证响应.
     *
     * @param \Leevel\Http\Request $request
     * @param array                $errors
     *
     * @return \Leevel\Http\Response
     */
    protected function validationResponse(HttpRequest $request, array $errors)
    {
        if ($request->isAjax() && !$request->isPjax()) {
            return $this->getResponseComponent()->api($errors);
        }

        return $this->
        getResponseComponent()->

        redirect($this->getRedirectUrl($request), [
            'make' => false,
        ])->

        clearErrors()->

        withErrors($errors)->

        withInputs($request->allAll());
    }

    /**
     * 返回错误消息.
     *
     * @param \Leevel\Validate\IValidate $validate
     *
     * @return array
     */
    protected function validationErrors(IValidate $validate)
    {
        return $validate->error();
    }

    /**
     * 返回前一个页面.
     *
     *
     * @param mixed $request
     *
     * @return string
     */
    protected function getRedirectUrl($request)
    {
        return $request->header('referer') ?: $this->getSessionPrevUrl();
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
        return project(Response::class);
    }
}
