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
use Leevel\Http\Request;
use Leevel\Response;

/**
 * 修改密码
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.09
 *
 * @version 1.0
 */
trait ChangePassword
{
    use field;

    /**
     * 修改密码界面.
     *
     * @return \Leevel\Http\Response
     */
    public function changePassword()
    {
        return $this->displayChangePasswordForm();
    }

    /**
     * 获取修改密码界面.
     *
     * @return \Leevel\Http\Response
     */
    public function displayChangePasswordForm()
    {
        return Response::view(
            $this->getChangePasswordView()
        );
    }

    /**
     * 执行修改密码
     *
     * @param \Leevel\Http\Request $request
     *
     * @return array|\Leevel\Http\Response
     */
    public function changeUserPassword(Request $request)
    {
        //$this->validateChangePassword($request);

        try {
            $input = $request->alls([
                'id|trim',
                'old_password|trim',
                'password|trim',
                'comfirm_password|trim',
            ]);

            $this->setAuthField();

            $user = Auth::changePassword(
                $input['id'],
                $input['password'],
                $input['comfirm_password'],
                $input['old_password']
            );

            if ($this->isAjaxRequest()) {
                return [
                    'message' => $this->getChangePasswordSucceededMessage(
                        $user['nikename'] ?: $user['name']
                    ),
                ];
            }

            return $this->sendSucceededChangePasswordResponse(
                $this->getChangePasswordSucceededMessage(
                    $user['nikename'] ?: $user['name']
                )
            );
        } catch (change_password_failed $e) {
            return $this->sendFailedChangePasswordResponse(
                $e->getMessage()
            );
        }
    }

    /**
     * 发送正确修改密码消息.
     *
     * @param string $success
     *
     * @return \Leevel\Http\Response
     */
    protected function sendSucceededChangePasswordResponse($success)
    {
        return Response::redirect(
            $this->getChangePasswordSucceededRedirect()
        )->

        with('change_password_succeeded', $success);
    }

    /**
     * 发送错误修改密码消息.
     *
     * @param string $error
     *
     * @return \Leevel\Http\Response
     */
    protected function sendFailedChangePasswordResponse($error)
    {
        if ($this->isAjaxRequest()) {
            return [
                'code'    => 400,
                'message' => $error,
            ];
        }

        return Response::redirect(
            $this->getChangePasswordFailedRedirect()
        )->

        withErrors([
            'change_password_error' => $error,
        ]);
    }

    /**
     * 验证登录修改密码请求
     *
     * @param \Leevel\Http\Request $request
     */
    protected function validateChangePassword(Request $request)
    {
        $this->validate(
            $request,
            $this->getValidateChangePasswordRule(),
            $this->getValidateChangePasswordMessage()
        );
    }

    /**
     * 获取修改密码验证规则.
     *
     * @return array
     */
    protected function getValidateChangePasswordRule()
    {
        return property_exists($this, 'validateChangePasswordRule') ?
            $this->validateChangePasswordRule :
            [
                'old_password'     => 'required|min_length:6',
                'password'         => 'required|min_length:6',
                'comfirm_password' => 'required|min_length:6|equal_to:password',
            ];
    }

    /**
     * 获取修改密码验证规则消息.
     *
     * @return array
     */
    protected function getValidateChangePasswordMessage()
    {
        return property_exists($this, 'validateChangePasswordMessage') ?
            $this->validateChangePasswordMessage :
            [];
    }

    /**
     * 获取修改密码消息.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getChangePasswordSucceededMessage($name)
    {
        return __('%s 修改密码成功', $name);
    }

    /**
     * 获取修改密码视图.
     *
     * @return string
     */
    protected function getChangePasswordView()
    {
        return property_exists($this, 'changePasswordView') ?
            $this->changePasswordView :
            '';
    }

    /**
     * 获取修改密码成功转向地址
     *
     * @return string
     */
    protected function getChangePasswordSucceededRedirect()
    {
        return property_exists($this, 'changePasswordSucceededRedirect') ?
            $this->changePasswordSucceededRedirect :
            'auth/login';
    }

    /**
     * 获取修改密码失败转向地址
     *
     * @return string
     */
    protected function getChangePasswordFailedRedirect()
    {
        return property_exists($this, 'changePasswordFailedRedirect') ?
            $this->changePasswordFailedRedirect :
            'auth/changePassword';
    }
}
