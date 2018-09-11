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

namespace Leevel\Auth;

/**
 * IConnect 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
 */
interface IConnect
{
    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value);

    /**
     * 用户是否已经登录.
     *
     * @return bool
     */
    public function isLogin();

    /**
     * 获取登录信息.
     *
     * @return mixed
     */
    public function getLogin();

    /**
     * 登录验证
     *
     * @param mixed  $name
     * @param string $password
     * @param mixed  $loginTime
     *
     * @return \Leevel\Database\Ddd\IEntity|void
     */
    public function login($name, $password, $loginTime = null);

    /**
     * 仅仅验证登录用户和密码是否正确.
     *
     * @param mixed  $name
     * @param string $password
     *
     * @return \Leevel\Database\Ddd\IEntity|void
     */
    public function onlyValidate($name, $password);

    /**
     * 登出.
     */
    public function logout();

    /**
     * 是否处于锁定状态
     *
     * @return bool
     */
    public function isLock();

    /**
     * 锁定登录.
     *
     * @param mixed $loginTime
     */
    public function lock($loginTime = null);

    /**
     * 解锁
     */
    public function unlock();

    /**
     * 修改密码
     *
     * @param mixed  $name
     * @param string $newPassword
     * @param string $confirmPassword
     * @param string $oldPassword
     * @param bool   $ignoreOldPassword
     *
     * @return mixed
     */
    public function changePassword($name, $newPassword, $confirmPassword, $oldPassword, $ignoreOldPassword = false);

    /**
     * 注册用户.
     *
     * @param string $name
     * @param string $password
     * @param string $comfirmPassword
     * @param string $nikename
     * @param string $ip
     * @param string $email
     * @param string $mobile
     *
     * @return mixed
     */
    public function registerUser($name, $password, $comfirmPassword, $nikename = null, $ip = null, $email = null, $mobile = null);

    /**
     * 设置认证名字.
     *
     * @param string $tokenName
     *
     * @return string
     */
    public function setTokenName($tokenName);

    /**
     * 取得认证名字.
     *
     * @return string
     */
    public function getTokenName();

    /**
     * 设置用户信息持久化名字.
     *
     * @param string $userPersistenceName
     *
     * @return string
     */
    public function setUserPersistenceName($userPersistenceName);

    /**
     * 取得用户信息持久化名字.
     *
     * @return string
     */
    public function getUserPersistenceName();

    /**
     * 设置字段.
     *
     * @param array $fields
     * @param bool  $force
     */
    public function setField(array $fields, $force = false);

    /**
     * 获取字段.
     *
     * @param array $field
     *
     * @return mixed
     */
    public function getField($field);

    /**
     * 批量获取字段.
     *
     * @param array $fields
     * @param bool  $filterNull
     *
     * @return array
     */
    public function getFields(array $fields, $filterNull = true);

    /**
     * 验证数据分离.
     *
     * @param string $auth
     *
     * @return mixed
     */
    public function explodeTokenData($auth);

    /**
     * 验证数据组合.
     *
     * @param mixed  $identifier
     * @param string $password
     *
     * @return string
     */
    public function implodeTokenData($identifier, $password);
}
