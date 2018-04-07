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
namespace Leevel\Bootstrap\auth;

use Leevel\{
    auth,
    Http\Request
};

/**
 * 登录字段设置
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.09
 * @version 1.0
 */
trait field
{

    /**
     * 设置字段
     *
     * @return void
     */
    public function setAuthField()
    {
        auth::setField($this->authField());
    }

    /**
     * 返回所有字段
     *
     * @return array
     */
    public function authField()
    {
        return [
            'id' => $this->authFieldId(),
            'name' => $this->authFieldName(),
            'nikename' => $this->authFieldNikename(),
            'random' => $this->authFieldRandom(),
            'email' => $this->authFieldEmail(),
            'mobile' => $this->authFieldMobile(),
            'password' => $this->authFieldPassword(),
            'register_ip' => $this->authFieldRegisterIp(),
            'login_time' => $this->authFieldLoginTime(),
            'login_ip' => $this->authFieldLoginIp(),
            'login_count' => $this->authFieldLoginCount(),
            'status' => $this->authFieldStatus()
        ];
    }

    /**
     * 登录 ID 字段
     *
     * @return string
     */
    public function authFieldId()
    {
        return property_exists($this, 'strFieldId') ? $this->strFieldId : 'id';
    }

    /**
     * 登录帐号字段
     *
     * @return string
     */
    public function authFieldName()
    {
        return property_exists($this, 'strFieldName') ? $this->strFieldName : 'name';
    }

    /**
     * 登录昵称字段
     *
     * @return string
     */
    public function authFieldNikename()
    {
        return property_exists($this, 'strFieldNikename') ? $this->strFieldNikename : 'nikename';
    }

    /**
     * 登录随机码字段
     *
     * @return string
     */
    public function authFieldRandom()
    {
        return property_exists($this, 'strFieldRandom') ? $this->strFieldRandom : 'random';
    }

    /**
     * 登录邮件字段
     *
     * @return string
     */
    public function authFieldEmail()
    {
        return property_exists($this, 'strFieldEmail') ? $this->strFieldEmail : 'email';
    }

    /**
     * 登录手机字段
     *
     * @return string
     */
    public function authFieldMobile()
    {
        return property_exists($this, 'strFieldMobile') ? $this->strFieldMobile : 'mobile';
    }

    /**
     * 登录密码字段
     *
     * @return string
     */
    public function authFieldPassword()
    {
        return property_exists($this, 'strFieldPassword') ? $this->strFieldPassword : 'password';
    }

    /**
     * 登录注册 IP 字段
     *
     * @return string
     */
    public function authFieldRegisterIp()
    {
        return property_exists($this, 'strFieldRegisterIp') ? $this->strFieldRegisterIp : 'register_ip';
    }

    /**
     * 登录密码字段
     *
     * @return string
     */
    public function authFieldLoginTime()
    {
        return property_exists($this, 'strFieldLoginTime') ? $this->strFieldLoginTime : 'login_time';
    }

    /**
     * 登录 IP 字段
     *
     * @return string
     */
    public function authFieldLoginIp()
    {
        return property_exists($this, 'strFieldLoginIp') ? $this->strFieldLoginIp : 'login_ip';
    }

    /**
     * 登录次数字段
     *
     * @return string
     */
    public function authFieldLoginCount()
    {
        return property_exists($this, 'strFieldLoginCount') ? $this->strFieldLoginCount : 'login_count';
    }

    /**
     * 登录状态字段
     *
     * @return string
     */
    public function authFieldStatus()
    {
        return property_exists($this, 'strFieldStatus') ? $this->strFieldStatus : 'status';
    }

    /**
     * 是否为 ajax
     *
     * @return boolean
     */
    protected function isAjaxRequest()
    {
        return is_ajax_request();
    }
}
