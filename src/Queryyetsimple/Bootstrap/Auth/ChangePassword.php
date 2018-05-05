<?php declare(strict_types=1);
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
namespace Leevel\Bootstrap\auth;

use Leevel\{
    auth,
    response,
    Http\Request,
    auth\change_password_failed
};

/**
 * 修改密码
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.09
 * @version 1.0
 */
trait change_password
{
    use field;

    /**
     * 修改密码界面
     *
     * @return \Leevel\Http\Response
     */
    public function changePassword()
    {
        return $this->displayChangePasswordForm();
    }

    /**
     * 获取修改密码界面
     *
     * @return \Leevel\Http\Response
     */
    public function displayChangePasswordForm()
    {
        return response::view($this->getChangePasswordView());
    }

    /**
     * 执行修改密码
     *
     * @param \Leevel\Http\Request $oRequest
     * @return \Leevel\Http\Response|array
     */
    public function changeUserPassword(request $oRequest)
    {
        //$this->validateChangePassword($oRequest);

        try {
            $aInput = $oRequest->alls([
                'id|trim',
                'old_password|trim',
                'password|trim',
                'comfirm_password|trim'
            ]);

            $this->setAuthField();

            $aUser = auth::changePassword($aInput['id'], $aInput['password'], $aInput['comfirm_password'], $aInput['old_password']);

            if ($this->isAjaxRequest()) {
                return [
                    'message' => $this->getChangePasswordSucceededMessage($aUser['nikename'] ?  : $aUser['name'])
                ];
            } else {
                return $this->sendSucceededChangePasswordResponse($this->getChangePasswordSucceededMessage($aUser['nikename'] ?  : $aUser['name']));
            }
        } catch (change_password_failed $oE) {
            return $this->sendFailedChangePasswordResponse($oE->getMessage());
        }
    }

    /**
     * 发送正确修改密码消息
     *
     * @param string $strSuccess
     * @return \Leevel\Http\Response
     */
    protected function sendSucceededChangePasswordResponse($strSuccess)
    {
        return response::redirect($this->getChangePasswordSucceededRedirect())->with('change_password_succeeded', $strSuccess);
    }

    /**
     * 发送错误修改密码消息
     *
     * @param \Leevel\Http\Request $oRequest
     * @param string $strError
     * @return \Leevel\Http\Response
     */
    protected function sendFailedChangePasswordResponse($strError)
    {
        if ($this->isAjaxRequest()) {
            return [
                'code' => 400,
                'message' => $strError
            ];
        }

        return response::redirect($this->getChangePasswordFailedRedirect())->withErrors([
            'change_password_error' => $strError
        ]);
    }

    /**
     * 验证登录修改密码请求
     *
     * @param \Leevel\Http\Request $oRequest
     * @return void
     */
    protected function validateChangePassword(request $oRequest)
    {
        $this->validate($oRequest, $this->getValidateChangePasswordRule(), $this->getValidateChangePasswordMessage());
    }

    /**
     * 获取修改密码验证规则
     *
     * @return array
     */
    protected function getValidateChangePasswordRule()
    {
        return property_exists($this, 'strValidateChangePasswordRule') ? $this->strValidateChangePasswordRule : [
            'old_password' => 'required|min_length:6',
            'password' => 'required|min_length:6',
            'comfirm_password' => 'required|min_length:6|equal_to:password'
        ];
    }

    /**
     * 获取修改密码验证规则消息
     *
     * @return array
     */
    protected function getValidateChangePasswordMessage()
    {
        return property_exists($this, 'strValidateChangePasswordMessage') ? $this->strValidateChangePasswordMessage : [];
    }

    /**
     * 获取修改密码消息
     *
     * @param string $strName
     * @return string
     */
    protected function getChangePasswordSucceededMessage($strName)
    {
        return __('%s 修改密码成功', $strName);
    }

    /**
     * 获取修改密码视图
     *
     * @return string
     */
    protected function getChangePasswordView()
    {
        return property_exists($this, 'strChangePasswordView') ? $this->strChangePasswordView : '';
    }

    /**
     * 获取修改密码成功转向地址
     *
     * @return string
     */
    protected function getChangePasswordSucceededRedirect()
    {
        return property_exists($this, 'strChangePasswordSucceededRedirect') ? $this->strChangePasswordSucceededRedirect : 'auth/login';
    }

    /**
     * 获取修改密码失败转向地址
     *
     * @return string
     */
    protected function getChangePasswordFailedRedirect()
    {
        return property_exists($this, 'strChangePasswordFailedRedirect') ? $this->strChangePasswordFailedRedirect : 'auth/changePassword';
    }
}
