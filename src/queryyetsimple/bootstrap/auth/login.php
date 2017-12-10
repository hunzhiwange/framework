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
namespace queryyetsimple\bootstrap\auth;

use queryyetsimple\auth;
use queryyetsimple\response;
use queryyetsimple\http\request;
use queryyetsimple\auth\login_failed;

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
     * @return \queryyetsimple\http\response
     */
    public function login()
    {
        return $this->displayLoginForm();
    }

    /**
     * 获取登录界面
     *
     * @return \queryyetsimple\http\response
     */
    public function displayLoginForm()
    {
        return response::view($this->getLoginView());
    }

    /**
     * 登录验证
     *
     * @param \queryyetsimple\http\request $oRequest
     * @return \queryyetsimple\http\response|array
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
        } catch (login_failed $oE) {
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
     * @param \queryyetsimple\http\request $oRequest
     * @return \queryyetsimple\http\response|array
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
     * @param \queryyetsimple\http\request $oRequest
     * @param boolean $booValidate
     * @return \queryyetsimple\http\response|array
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
        } catch (login_failed $oE) {
            $booValidate = false;
            return $this->sendFailedLoginResponse($oE->getMessage());
        }
    }

    /**
     * 发送正确登录消息
     *
     * @param string $strSuccess
     * @return \queryyetsimple\http\response|boolean
     */
    protected function sendSucceededLoginResponse($strSuccess)
    {
        return response::redirect($this->getLoginSucceededRedirect())->with('login_succeeded', $strSuccess);
    }

    /**
     * 发送错误登录消息
     *
     * @param string $strError
     * @return \queryyetsimple\http\response|boolean
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
     * @param \queryyetsimple\http\request $oRequest
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
