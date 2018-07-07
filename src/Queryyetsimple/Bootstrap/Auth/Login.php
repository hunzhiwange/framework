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
use Leevel\auth\LoginFailed;
use Leevel\Http\Request;
use Leevel\Response;

/**
 * 登录验证
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.09
 *
 * @version 1.0
 */
trait Login
{
    use field;

    /**
     * 是否已经登录.
     *
     * @return bool
     */
    public function isLogin()
    {
        return Auth::isLogin();
    }

    /**
     * 获取登录信息.
     *
     * @return mixed
     */
    public function getLogin()
    {
        return Auth::getLogin();
    }

    /**
     * 登录界面.
     *
     * @return \Leevel\Http\Response
     */
    public function login()
    {
        return $this->displayLoginForm();
    }

    /**
     * 获取登录界面.
     *
     * @return \Leevel\Http\Response
     */
    public function displayLoginForm()
    {
        return Response::view(
            $this->getLoginView()
        );
    }

    /**
     * 登录验证
     *
     * @param \Leevel\Http\Request $request
     *
     * @return array|\Leevel\Http\Response
     */
    public function checkLogin(Request $request)
    {
        //$this->validateLogin($request);

        try {
            $input = $request->alls([
                'name|trim',
                'password|trim',
                'remember_me|trim',
                'remember_time|trim',
                'remember_key|trim',
            ]);

            $this->setAuthField();

            $user = Auth::login(
                $input['name'],
                $input['password'],
                $input['remember_me'] ? $input['remember_time'] : null
            );

            if ($this->isAjaxRequest()) {
                $return = [];
                $return['api_token'] = Auth::getTokenName();
                $return['user'] = $user->toArray();
                $return['remember_key'] = Auth::implodeTokenData(
                    $input['name'],
                    $input['password']
                );

                return $return;
            }

            return $this->sendSucceededLoginResponse(
                $this->getLoginSucceededMessage(
                    $user['nikename'] ?: $user['name']
                )
            );
        } catch (LoginFailed $e) {
            return $this->sendFailedLoginResponse(
                $e->getMessage()
            );
        }
    }

    /**
     * 是否处于锁定状态
     *
     * @return bool
     */
    public function isLock()
    {
        return Auth::isLock();
    }

    /**
     * 锁定登录.
     *
     * @param mixed $loginTime
     */
    public function lock($loginTime = null)
    {
        Auth::lock($loginTime);
    }

    /**
     * 解锁
     *
     * @param \Leevel\Http\Request $request
     *
     * @return array|\Leevel\Http\Response
     */
    public function unlock(Request $request)
    {
        $validate = false;

        $result = $this->onlyValidate($request, $validate);

        if (true === $validate) {
            Auth::unlock();
        }

        return $result;
    }

    /**
     * 仅仅验证用户和密码是否正确.
     *
     * @param \Leevel\Http\Request $request
     * @param bool                 $validate
     *
     * @return array|\Leevel\Http\Response
     */
    public function onlyValidate(Request $request, &$validate = false)
    {
        //$this->validateLogin($request);

        try {
            $input = $request->alls([
                'name|trim',
                'password|trim',
            ]);

            $this->setAuthField();

            $user = Auth::onlyValidate(
                $input['name'],
                $input['password']
            );

            $validate = true;

            if ($this->isAjaxRequest()) {
                return [
                    'message' => $this->getLoginSucceededMessage(
                        $user['nikename'] ?: $user['name']
                    ),
                ];
            }

            return $this->sendSucceededLoginResponse(
                $this->getLoginSucceededMessage(
                    $user['nikename'] ?: $user['name']
                )
            );
        } catch (LoginFailed $e) {
            $validate = false;

            return $this->sendFailedLoginResponse(
                $e->getMessage()
            );
        }
    }

    /**
     * 发送正确登录消息.
     *
     * @param string $success
     *
     * @return bool|\Leevel\Http\Response
     */
    protected function sendSucceededLoginResponse($success)
    {
        return Response::redirect(
            $this->getLoginSucceededRedirect()
        )->

        with('login_succeeded', $success);
    }

    /**
     * 发送错误登录消息.
     *
     * @param string $error
     *
     * @return bool|\Leevel\Http\Response
     */
    protected function sendFailedLoginResponse($error)
    {
        if ($this->isAjaxRequest()) {
            return [
                'code'    => 400,
                'message' => $error,
            ];
        }

        return Response::redirect(
            $this->getLoginFailedRedirect()
        )->

        withErrors([
            'login_error' => $error,
        ]);
    }

    /**
     * 验证登录请求
     *
     * @param \Leevel\Http\Request $request
     */
    protected function validateLogin(Request $request)
    {
        $this->validate(
            $request,
            $this->getValidateLoginRule(),
            $this->getValidateLoginMessage()
        );
    }

    /**
     * 获取验证规则.
     *
     * @return array
     */
    protected function getValidateLoginRule()
    {
        return property_exists($this, 'validateLoginRule') ?
            $this->validateLoginRule :
            [
                'name'     => 'required|max_length:50',
                'password' => 'required|min_length:6',
            ];
    }

    /**
     * 获取登录验证规则消息.
     *
     * @return array
     */
    protected function getValidateLoginMessage()
    {
        return property_exists($this, 'validateLoginMessage') ?
            $this->validateLoginMessage :
            [];
    }

    /**
     * 获取登录消息.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getLoginSucceededMessage($name)
    {
        return __('%s 登录成功', $name);
    }

    /**
     * 获取登录视图.
     *
     * @return string
     */
    protected function getLoginView()
    {
        return property_exists($this, 'loginView') ?
            $this->loginView :
            '';
    }

    /**
     * 获取登录成功转向地址
     *
     * @return string
     */
    protected function getLoginSucceededRedirect()
    {
        return property_exists($this, 'loginSucceededRedirect') ?
            $this->loginSucceededRedirect :
            'auth/index';
    }

    /**
     * 获取登录失败转向地址
     *
     * @return string
     */
    protected function getLoginFailedRedirect()
    {
        return property_exists($this, 'loginFailedRedirect') ?
            $this->loginFailedRedirect :
            'auth/login';
    }
}
