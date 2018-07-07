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

namespace Leevel\Bootstrap\Auth;

use Leevel\Auth;
use Leevel\Auth\RegisterFailed;
use Leevel\Http\Request;
use Leevel\Response;

/**
 * 用户注册.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.09
 *
 * @version 1.0
 */
trait Register
{
    /**
     * 注册界面.
     *
     * @return \Leevel\Http\Response
     */
    public function register()
    {
        return $this->displayRegisterForm();
    }

    /**
     * 注册用户.
     *
     * @param \Leevel\Http\Request $request
     *
     * @return \Leevel\Http\Response
     */
    public function registerUser(Request $request)
    {
        $this->validateRegister($request);

        try {
            $post = $request->posts([
                'name|trim',
                'nikename|trim',
                'password|trim',
                'comfirm_password|trim',
                'email|trim',
                'mobile|trim',
            ]);

            $this->setAuthField();

            Auth::registerUser(
                $post['name'],
                $post['password'],
                $post['comfirm_password'],
                $post['nikename'],
                $request->getClientIp(),
                $post['email'],
                $post['mobile']
            );

            return $this->sendSucceededRegisterResponse(
                $this->getRegisterSucceededMessage(
                    $post['nikename'] ?: $post['name']
                )
            );
        } catch (RegisterFailed $e) {
            return $this->sendFailedRegisterResponse(
                $e->getMessage()
            );
        }
    }

    /**
     * 获取注册界面.
     *
     * @return \Leevel\Http\Response
     */
    public function displayRegisterForm()
    {
        return Response::view(
            $this->getRegisterView()
        );
    }

    /**
     * 发送正确注册消息.
     *
     * @param string $success
     *
     * @return \Leevel\Http\Response
     */
    protected function sendSucceededRegisterResponse($success)
    {
        return Response::redirect(
            $this->getRegisterSucceededRedirect()
        )->

        with('register_succeeded', $success);
    }

    /**
     * 发送错误注册消息.
     *
     * @param string $error
     *
     * @return \Leevel\Http\Response
     */
    protected function sendFailedRegisterResponse($error)
    {
        return Response::redirect(
            $this->getRegisterFailedRedirect()
        )->

        withErrors([
            'register_error' => $error,
        ]);
    }

    /**
     * 验证注册请求
     *
     * @param \Leevel\Http\Request $request
     */
    protected function validateRegister(Request $request)
    {
        $this->validate(
            $request,
            $this->getValidateRegisterRule(),
            $this->getValidateRegisterMessage()
        );
    }

    /**
     * 获取注册验证规则.
     *
     * @return array
     */
    protected function getValidateRegisterRule()
    {
        return property_exists($this, 'validateRegisterRule') ?
            $this->validateRegisterRule :
            [
                'name'             => 'required|max_length:50',
                'nikename'         => 'required|max_length:50',
                'password'         => 'required|min_length:6',
                'comfirm_password' => 'required|min_length:6|equal_to:password',
                'email'            => 'required|email',
                'mobile'           => 'value|mobile',
            ];
    }

    /**
     * 获取注册验证规则消息.
     *
     * @return array
     */
    protected function getValidateRegisterMessage()
    {
        return property_exists($this, 'validateRegisterMessage') ?
            $this->validateRegisterMessage :
            [];
    }

    /**
     * 获取注册消息.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getRegisterSucceededMessage($name)
    {
        return __('%s 注册成功', $name);
    }

    /**
     * 获取注册视图.
     *
     * @return string
     */
    protected function getRegisterView()
    {
        return property_exists($this, 'registerView') ?
            $this->registerView :
            '';
    }

    /**
     * 获取注册成功转向地址
     *
     * @return string
     */
    protected function getRegisterSucceededRedirect()
    {
        return property_exists($this, 'registerSucceededRedirect') ?
            $this->registerSucceededRedirect :
            'auth/login';
    }

    /**
     * 获取注册失败转向地址
     *
     * @return string
     */
    protected function getRegisterFailedRedirect()
    {
        return property_exists($this, 'registerFailedRedirect') ?
            $this->registerFailedRedirect :
            'auth/register';
    }
}
