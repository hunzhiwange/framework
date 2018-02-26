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
namespace Queryyetsimple\Bootstrap\auth;

use Queryyetsimple\{
    auth,
    response,
    Http\Request,
    auth\RegisterFailed
};

/**
 * 用户注册
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.09
 * @version 1.0
 */
trait register
{

    /**
     * 注册界面
     *
     * @return \Queryyetsimple\Http\Response
     */
    public function register()
    {
        return $this->displayRegisterForm();
    }

    /**
     * 注册用户
     *
     * @param \Queryyetsimple\Http\Request $oRequest
     * @return \Queryyetsimple\Http\Response
     */
    public function registerUser(request $oRequest)
    {
        $this->validateRegister($oRequest);

        try {
            $aPost = $oRequest->posts([
                'name|trim',
                'nikename|trim',
                'password|trim',
                'comfirm_password|trim',
                'email|trim',
                'mobile|trim'
            ]);

            $this->setAuthField();

            auth::registerUser($aPost['name'], $aPost['password'], $aPost['comfirm_password'], $aPost['nikename'], $oRequest->getClientIp(), $aPost['email'], $aPost['mobile']);

            return $this->sendSucceededRegisterResponse($this->getRegisterSucceededMessage($aPost['nikename'] ?  : $aPost['name']));
        } catch (RegisterFailed $oE) {
            return $this->sendFailedRegisterResponse($oE->getMessage());
        }
    }

    /**
     * 获取注册界面
     *
     * @return \Queryyetsimple\Http\Response
     */
    public function displayRegisterForm()
    {
        return response::view($this->getRegisterView());
    }

    /**
     * 发送正确注册消息
     *
     * @param string $strSuccess
     * @return \Queryyetsimple\Http\Response
     */
    protected function sendSucceededRegisterResponse($strSuccess)
    {
        return response::redirect($this->getRegisterSucceededRedirect())->with('register_succeeded', $strSuccess);
    }

    /**
     * 发送错误注册消息
     *
     * @param string $strError
     * @return \Queryyetsimple\Http\Response
     */
    protected function sendFailedRegisterResponse($strError)
    {
        return response::redirect($this->getRegisterFailedRedirect())->withErrors([
            'register_error' => $strError
        ]);
    }

    /**
     * 验证注册请求
     *
     * @param \Queryyetsimple\Http\Request $oRequest
     * @return void
     */
    protected function validateRegister(request $oRequest)
    {
        $this->validate($oRequest, $this->getValidateRegisterRule(), $this->getValidateRegisterMessage());
    }

    /**
     * 获取注册验证规则
     *
     * @return array
     */
    protected function getValidateRegisterRule()
    {
        return property_exists($this, 'strValidateRegisterRule') ? $this->strValidateRegisterRule : [
            'name' => 'required|max_length:50',
            'nikename' => 'required|max_length:50',
            'password' => 'required|min_length:6',
            'comfirm_password' => 'required|min_length:6|equal_to:password',
            'email' => 'required|email',
            'mobile' => 'value|mobile'
        ];
    }

    /**
     * 获取注册验证规则消息
     *
     * @return array
     */
    protected function getValidateRegisterMessage()
    {
        return property_exists($this, 'strValidateRegisterMessage') ? $this->strValidateRegisterMessage : [];
    }

    /**
     * 获取注册消息
     *
     * @param string $strName
     * @return string
     */
    protected function getRegisterSucceededMessage($strName)
    {
        return __('%s 注册成功', $strName);
    }

    /**
     * 获取注册视图
     *
     * @return string
     */
    protected function getRegisterView()
    {
        return property_exists($this, 'strRegisterView') ? $this->strRegisterView : '';
    }

    /**
     * 获取注册成功转向地址
     *
     * @return string
     */
    protected function getRegisterSucceededRedirect()
    {
        return property_exists($this, 'strRegisterSucceededRedirect') ? $this->strRegisterSucceededRedirect : 'auth/login';
    }

    /**
     * 获取注册失败转向地址
     *
     * @return string
     */
    protected function getRegisterFailedRedirect()
    {
        return property_exists($this, 'strRegisterFailedRedirect') ? $this->strRegisterFailedRedirect : 'auth/register';
    }
}
