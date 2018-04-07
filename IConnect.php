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
namespace Leevel\Auth;

/**
 * IConnect 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
interface IConnect
{

    /**
     * 用户是否已经登录
     *
     * @return boolean
     */
    public function isLogin();

    /**
     * 获取登录信息
     *
     * @return mixed
     */
    public function getLogin();

    /**
     * 登录验证
     *
     * @param mixed $mixName
     * @param string $sPassword
     * @param mixed $mixLoginTime
     * @return \Leevel\Mvc\IModel|void
     */
    public function login($mixName, $sPassword, $mixLoginTime = null);

    /**
     * 仅仅验证登录用户和密码是否正确
     *
     * @param mixed $mixName
     * @param string $sPassword
     * @return \Leevel\Mvc\IModel|void
     */
    public function onlyValidate($mixName, $sPassword);

    /**
     * 登出
     *
     * @return void
     */
    public function logout();

    /**
     * 是否处于锁定状态
     *
     * @return boolean
     */
    public function isLock();

    /**
     * 锁定登录
     *
     * @param mixed $mixLoginTime
     * @return void
     */
    public function lock($mixLoginTime = null);

    /**
     * 解锁
     *
     * @return void
     */
    public function unlock();

    /**
     * 修改密码
     *
     * @param mixed $mixName
     * @param string $sNewPassword
     * @param string $sConfirmPassword
     * @param string $sOldPassword
     * @param boolean $bIgnoreOldPassword
     * @return mixed
     */
    public function changePassword($mixName, $sNewPassword, $sConfirmPassword, $sOldPassword, $bIgnoreOldPassword = false);

    /**
     * 注册用户
     *
     * @param string $strName
     * @param string $strPassword
     * @param string $strComfirmPassword
     * @param string $strNikename
     * @param string $strIp
     * @param string $strEmail
     * @param string $strMobile
     * @return mixed
     */
    public function registerUser($strName, $strPassword, $strComfirmPassword, $strNikename = null, $strIp = null, $strEmail = null, $strMobile = null);

    /**
     * 设置认证名字
     *
     * @param string $strTokenName
     * @return string
     */
    public function setTokenName($strTokenName);

    /**
     * 取得认证名字
     *
     * @return string
     */
    public function getTokenName();

    /**
     * 设置用户信息持久化名字
     *
     * @param string $strUserPersistenceName
     * @return string
     */
    public function setUserPersistenceName($strUserPersistenceName);

    /**
     * 取得用户信息持久化名字
     *
     * @return string
     */
    public function getUserPersistenceName();

    /**
     * 设置字段
     *
     * @param array $arrField
     * @param boolean $booForce
     * @return void
     */
    public function setField(array $arrField, $booForce = false);

    /**
     * 获取字段
     *
     * @param array $strField
     * @return mixed
     */
    public function getField($strField);

    /**
     * 批量获取字段
     *
     * @param array $arrField
     * @param boolean $booFilterNull
     * @return array
     */
    public function getFields(array $arrField, $booFilterNull = true);

    /**
     * 验证数据分离
     *
     * @param string $sAuth
     * @return mixed
     */
    public function explodeTokenData($sAuth);

    /**
     * 验证数据组合
     *
     * @param mixed $maxIdentifier
     * @param string $strPassword
     * @return string
     */
    public function implodeTokenData($maxIdentifier, $strPassword);
}
