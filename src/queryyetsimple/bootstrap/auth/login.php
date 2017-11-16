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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\bootstrap\auth;

use queryyetsimple\auth;
use queryyetsimple\response;
use queryyetsimple\http\request;
use queryyetsimple\auth\login_failed;
use queryyetsimple\auth\change_password_failed;
use queryyetsimple\bootstrap\validate\request as validate_request;

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
    use validate_request;
    
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
     * @return \queryyetsimple\http\response|boolean
     */
    public function checkLogin(request $oRequest)
    {
        $this->validateLogin($oRequest);
        
        try {
            $aInput = $oRequest->alls([
                'name|trim', 
                'password|trim', 
                'remember_me|trim', 
                'remember_time|trim', 
                'remember_key|trim'
            ]);
            
            if ($aInput['remember_key']) {
                $aInput['remember_key'] = auth::explodeTokenData($aInput['remember_key']);
                if (! $aInput['remember_key']) {
                    return response::api(__('您尚未登录'), 400);
                } else {
                    list($aInput['name'], $aInput['password']) = $aInput['remember_key'];
                }
            }
            
            $this->setAuthField();
            
            $oUser = auth::login($aInput['name'], $aInput['password'], $aInput['remember_me'] ? $aInput['remember_time'] : null);
            
            if ($this->isAjaxRequest($oRequest)) {
                $aReturn = [];
                $aReturn['api_token'] = auth::getTokenName();
                $aReturn['user'] = $oUser->toArray();
                $aReturn['remember_key'] = auth::implodeTokenData($aInput['name'], $aInput['password']);
                return $aReturn;
            } else {
                return $this->sendSucceededLoginResponse($oRequest, $this->getLoginSucceededMessage($oUser['nikename'] ?  : $oUser['name']));
            }
        } catch (login_failed $oE) {
            return $this->sendFailedLoginResponse($oRequest, $oE->getMessage());
        }
    }
    
    /**
     * 登录退出
     *
     * @return \queryyetsimple\http\response
     */
    public function logout()
    {
        return $this->displayLoginout();
    }
    
    /**
     * 登录退出
     *
     * @return \queryyetsimple\http\response
     */
    public function displayLoginout()
    {
        auth::logout();
        
        if ($this->isAjaxRequest(project('request'))) {
            return [
                'message' => $this->getLogoutMessage()
            ];
        }
        
        return response::redirect($this->getLogoutRedirect())->with('login_out', $this->getLogoutMessage());
    }
    
    /**
     * 修改密码界面
     *
     * @return \queryyetsimple\http\response
     */
    public function changePassword()
    {
        return $this->displayChangePasswordForm();
    }
    
    /**
     * 获取修改密码界面
     *
     * @return \queryyetsimple\http\response
     */
    public function displayChangePasswordForm()
    {
        return response::view($this->getChangePasswordView());
    }
    
    /**
     * 执行修改密码
     *
     * @param \queryyetsimple\http\request $oRequest            
     * @return \queryyetsimple\http\response
     */
    public function changeUserPassword(request $oRequest)
    {
        $this->validateChangePassword($oRequest);
        
        try {
            $arrUser = $this->getLogin();
            
            $aPost = $oRequest->posts([
                'old_password|trim', 
                'password|trim', 
                'comfirm_password|trim'
            ]);
            
            $this->setAuthField();
            
            $aUser = auth::changePassword($arrUser['id'], $aPost['password'], $aPost['comfirm_password'], $aPost['old_password']);
            
            return $this->sendSucceededChangePasswordResponse($oRequest, $this->getChangePasswordSucceededMessage($aUser['nikename'] ?  : $aUser['name']));
        } catch (change_password_failed $oE) {
            return $this->sendFailedChangePasswordResponse($oE->getMessage());
        }
    }
    
    /**
     * 发送正确登录消息
     *
     * @param \queryyetsimple\http\request $oRequest            
     * @param string $strSuccess            
     * @return \queryyetsimple\http\response|boolean
     */
    protected function sendSucceededLoginResponse(request $oRequest, $strSuccess)
    {
        return response::redirect($this->getLoginSucceededRedirect())->with('login_succeeded', $strSuccess);
    }
    
    /**
     * 发送错误登录消息
     *
     * @param \queryyetsimple\http\request $oRequest            
     * @param string $strError            
     * @return \queryyetsimple\http\response|boolean
     */
    protected function sendFailedLoginResponse(request $oRequest, $strError)
    {
        if ($this->isAjaxRequest($oRequest)) {
            return response::api($strError, 400);
        }
        
        return response::redirect($this->getLoginFailedRedirect())->withErrors([
            'login_error' => $strError
        ]);
    }
    
    /**
     * 发送正确修改密码消息
     *
     * @param string $strSuccess            
     * @return \queryyetsimple\http\response
     */
    protected function sendSucceededChangePasswordResponse($strSuccess)
    {
        return response::redirect($this->getChangePasswordSucceededRedirect())->with('change_password_succeeded', $strSuccess);
    }
    
    /**
     * 发送错误修改密码消息
     *
     * @param string $strError            
     * @return \queryyetsimple\http\response
     */
    protected function sendFailedChangePasswordResponse($strError)
    {
        return response::redirect($this->getChangePasswordFailedRedirect())->withErrors([
            'change_password_error' => $strError
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
     * 验证登录修改密码请求
     *
     * @param \queryyetsimple\http\request $oRequest            
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
     * 获取退出登录消息
     *
     * @return string
     */
    protected function getLogoutMessage()
    {
        return __('退出成功');
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
     * 是否为 ajax
     *
     * @param \queryyetsimple\http\request $oRequest            
     * @return boolean
     */
    protected function isAjaxRequest(request $oRequest)
    {
        if ($oRequest->isAjax() && ! $oRequest->isPjax()) {
            return true;
        }
        return false;
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
     * 获取修改密码视图
     *
     * @return string
     */
    protected function getChangePasswordView()
    {
        return property_exists($this, 'strChangePasswordView') ? $this->strChangePasswordView : '';
    }
    
    /**
     * 获取退出转向地址
     *
     * @return string
     */
    protected function getLogoutRedirect()
    {
        return property_exists($this, 'strLogoutRedirect') ? $this->strLogoutRedirect : 'auth/index';
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
    protected function getChangePasswordFailedRedirect() {
        return property_exists ( $this, 'strChangePasswordFailedRedirect' ) ? $this->strChangePasswordFailedRedirect : 'auth/changePassword';
    }
}
