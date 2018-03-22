<?php
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
namespace Queryyetsimple\Bootstrap\auth;

use Queryyetsimple\{
    auth,
    response,
    Http\Request,
    auth\LoginFailed
};

/**
 * 登录验证
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.09
 * @version 1.0
 */
trait login
{
    use field;

    /**
     * 是否已经登录
     *
     * @return boolean
     */
    public function isLogin()
    {
        return auth::isLogin();
    }

    /**
     * 获取登录信息
     *
     * @return mixed
     */
    public function getLogin()
    {
        return auth::getLogin();
    }

    /**
     * 登录界面
     *
     * @return \Queryyetsimple\Http\Response
     */
    public function login()
    {
        return $this->displayLoginForm();
    }

    /**
     * 获取登录界面
     *
     * @return \Queryyetsimple\Http\Response
     */
    public function displayLoginForm()
    {
        return response::view($this->getLoginView());
    }

    /**
     * 登录验证
     *
     * @param \Queryyetsimple\Http\Request $oRequest
     * @return \Queryyetsimple\Http\Response|array
     */
    public function checkLogin(request $oRequest)
    {
        //$this->validateLogin($oRequest);

        try {
            $aInput = $oRequest->alls([
                'name|trim',
                'password|trim',
                'remember_me|trim',
                'remember_time|trim',
                'remember_key|trim'
            ]);

            $this->setAuthField();

            $oUser = auth::login($aInput['name'], $aInput['password'], $aInput['remember_me'] ? $aInput['remember_time'] : null);

            if ($this->isAjaxRequest()) {
                $aReturn = [];
                $aReturn['api_token'] = auth::getTokenName();
                $aReturn['user'] = $oUser->toArray();
                $aReturn['remember_key'] = auth::implodeTokenData($aInput['name'], $aInput['password']);
                return $aReturn;
            } else {
                return $this->sendSucceededLoginResponse($this->getLoginSucceededMessage($oUser['nikename'] ?  : $oUser['name']));
            }
        } catch (LoginFailed $oE) {
            return $this->sendFailedLoginResponse($oE->getMessage());
        }
    }

    /**
     * 是否处于锁定状态
     *
     * @return boolean
     */
    public function isLock()
    {
        return auth::isLock();
    }

    /**
     * 锁定登录
     *
     * @param mixed $mixLoginTime
     * @return void
     */
    public function lock($mixLoginTime = null)
    {
        auth::lock($mixLoginTime);
    }

    /**
     * 解锁
     *
     * @param \Queryyetsimple\Http\Request $oRequest
     * @return \Queryyetsimple\Http\Response|array
     * @return void
     */
    public function unlock(request $oRequest)
    {
        $booValidate = false;
        $arrResult = $this->onlyValidate($oRequest, $booValidate);
        if($booValidate === true) {
            auth::unlock();
        }
        return $arrResult;
    }

    /**
     * 仅仅验证用户和密码是否正确
     *
     * @param \Queryyetsimple\Http\Request $oRequest
     * @param boolean $booValidate
     * @return \Queryyetsimple\Http\Response|array
     */
    public function onlyValidate(request $oRequest, &$booValidate = false)
    {
        //$this->validateLogin($oRequest);

        try {
            $aInput = $oRequest->alls([
                'name|trim',
                'password|trim'
            ]);

            $this->setAuthField();

            $oUser = auth::onlyValidate($aInput['name'], $aInput['password']);

            $booValidate = true;

            if ($this->isAjaxRequest()) {
                return ['message' => $this->getLoginSucceededMessage($oUser['nikename'] ?  : $oUser['name'])];
            } else {
                return $this->sendSucceededLoginResponse($this->getLoginSucceededMessage($oUser['nikename'] ?  : $oUser['name']));
            }
        } catch (LoginFailed $oE) {
            $booValidate = false;
            return $this->sendFailedLoginResponse($oE->getMessage());
        }
    }

    /**
     * 发送正确登录消息
     *
     * @param string $strSuccess
     * @return \Queryyetsimple\Http\Response|boolean
     */
    protected function sendSucceededLoginResponse($strSuccess)
    {
        return response::redirect($this->getLoginSucceededRedirect())->with('login_succeeded', $strSuccess);
    }

    /**
     * 发送错误登录消息
     *
     * @param string $strError
     * @return \Queryyetsimple\Http\Response|boolean
     */
    protected function sendFailedLoginResponse($strError)
    {
        if ($this->isAjaxRequest()) {
            return [
                'code' => 400,
                'message' => $strError
            ];
        }

        return response::redirect($this->getLoginFailedRedirect())->withErrors([
            'login_error' => $strError
        ]);
    }

    /**
     * 验证登录请求
     *
     * @param \Queryyetsimple\Http\Request $oRequest
     * @return void
     */
    protected function validateLogin(request $oRequest)
    {
        $this->validate($oRequest, $this->getValidateLoginRule(), $this->getValidateLoginMessage());
    }

    /**
     * 获取验证规则
     *
     * @return array
     */
    protected function getValidateLoginRule()
    {
        return property_exists($this, 'strValidateLoginRule') ? $this->strValidateLoginRule : [
            'name' => 'required|max_length:50',
            'password' => 'required|min_length:6'
        ];
    }

    /**
     * 获取登录验证规则消息
     *
     * @return array
     */
    protected function getValidateLoginMessage()
    {
        return property_exists($this, 'strValidateLoginMessage') ? $this->strValidateLoginMessage : [];
    }

    /**
     * 获取登录消息
     *
     * @param string $strName
     * @return string
     */
    protected function getLoginSucceededMessage($strName)
    {
        return __('%s 登录成功', $strName);
    }

    /**
     * 获取登录视图
     *
     * @return string
     */
    protected function getLoginView()
    {
        return property_exists($this, 'strLoginView') ? $this->strLoginView : '';
    }

    /**
     * 获取登录成功转向地址
     *
     * @return string
     */
    protected function getLoginSucceededRedirect()
    {
        return property_exists($this, 'strLoginSucceededRedirect') ? $this->strLoginSucceededRedirect : 'auth/index';
    }

    /**
     * 获取登录失败转向地址
     *
     * @return string
     */
    protected function getLoginFailedRedirect()
    {
        return property_exists($this, 'strLoginFailedRedirect') ? $this->strLoginFailedRedirect : 'auth/login';
    }
}
