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

use Leevel\Encryption\IEncryption;

/**
 * connect 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.07
 *
 * @version 1.0
 */
abstract class Connect
{
    /**
     * 加密.
     *
     * @var \Leevel\Encryption\IEncryption
     */
    protected $encryption;

    /**
     * 认证名字.
     *
     * @var string
     */
    protected $tokenName;

    /**
     * 用户持久化名字.
     *
     * @return string
     */
    protected $userPersistenceName;

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'user_persistence'  => 'user_persistence',
        'token_persistence' => 'token_persistence',
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Encryption\IEncryption $encryption
     * @param array                          $option
     */
    public function __construct(IEncryption $encryption, array $option = [])
    {
        $this->encryption = $encryption;

        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 用户是否已经登录.
     *
     * @return bool
     */
    public function isLogin()
    {
        return $this->getLogin() ? true : false;
    }

    /**
     * 获取登录信息.
     *
     * @return mixed
     */
    public function getLogin()
    {
        $token = $this->tokenData();

        list($userId, $password) = $token ? $this->explodeTokenData($token) : [
            '',
            '',
        ];

        if ($userId && $password) {
            return $this->getPersistenceUser($userId, $password) ?: false;
        }

        $this->logout();

        return false;
    }

    /**
     * 登录验证
     *
     * @param mixed  $name
     * @param string $password
     * @param mixed  $loginTime
     *
     * @return \Leevel\Database\Ddd\IEntity|void
     */
    public function login($name, $password, $loginTime = null)
    {
        $user = $this->onlyValidate($name, $password);

        $this->setLoginTokenName($user);

        $this->tokenPersistence(
            $user->{$this->getField('id')},
            $user->{$this->getField('password')},
            $loginTime
        );

        return $user;
    }

    /**
     * 仅仅验证登录用户和密码是否正确.
     *
     * @param mixed  $name
     * @param string $password
     *
     * @return \Leevel\Database\Ddd\IEntity|void
     */
    public function onlyValidate($name, $password)
    {
        $name = trim($name);
        $password = trim($password);

        if (!$name || !$password) {
            throw new LoginFailed(__('帐号或者密码不能为空'));
        }

        $user = $this->user->where(
            $this->parseLoginField($name),
            $name
        )->getOne();

        if (empty($user->{$this->getField('id')}) ||
            'enable' !== $user->{$this->getField('status')}) {
            throw new LoginFailed(__('帐号不存在或者未启用'));
        }

        if (!$this->checkPassword(
            $password,
            $user->{$this->getField('password')},
            $user->{$this->getField('random')})) {
            throw new LoginFailed(__('账号或者密码错误'));
        }

        return $user;
    }

    /**
     * 登出.
     */
    public function logout()
    {
        $this->deletePersistence(
            $this->getUserPersistenceName()
        );

        $this->deletePersistence(
            $this->getTokenName()
        );
    }

    /**
     * 设置认证名字.
     *
     * @param string $tokenName
     *
     * @return string
     */
    public function setTokenName($tokenName)
    {
        return $this->tokenName = $tokenName;
    }

    /**
     * 取得认证名字.
     *
     * @return string
     */
    public function getTokenName()
    {
        if (null !== $this->tokenName) {
            return $this->tokenName;
        }

        return $this->tokenName = $this->option['token_persistence'];
    }

    /**
     * 设置用户信息持久化名字.
     *
     * @param string $userPersistenceName
     *
     * @return string
     */
    public function setUserPersistenceName($userPersistenceName)
    {
        return $this->userPersistenceName = $userPersistenceName;
    }

    /**
     * 取得用户信息持久化名字.
     *
     * @return string
     */
    public function getUserPersistenceName()
    {
        if (null !== $this->userPersistenceName) {
            return $this->userPersistenceName;
        }

        return $this->userPersistenceName = $this->getTokenName().'@'.
            $this->option['user_persistence'];
    }

    /**
     * 验证数据分离.
     *
     * @param string $auth
     *
     * @return mixed
     */
    public function explodeTokenData($auth)
    {
        if (!$auth) {
            return false;
        }

        $auth = $this->encryption->decrypt($auth);

        return $auth ? explode("\t", $auth) : false;
    }

    /**
     * 验证数据组合.
     *
     * @param mixed  $identifier
     * @param string $password
     *
     * @return string
     */
    public function implodeTokenData($identifier, $password)
    {
        return $this->encryption->encrypt(
            $identifier."\t".$password
        );
    }

    /**
     * 验证密码是否正确.
     *
     * @param string $sourcePassword
     * @param string $password
     * @param string $random
     *
     * @return bool
     */
    protected function checkPassword($sourcePassword, $password, $random)
    {
        return $this->encodePassword($sourcePassword, $random) === $password;
    }

    /**
     * 加密密码
     *
     * @param string $sourcePassword
     * @param string $random
     *
     * @return string
     */
    protected function encodePassword($sourcePassword, $random)
    {
        return md5(md5($sourcePassword).$random);
    }

    /**
     * 解析登录字段.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseLoginField($name)
    {
        return $this->validate->email($name) ?
            $this->getField('email') :
            ($this->validate->mobile($name) ?
                $this->getField('mobile') :
                $this->getField('name'));
    }

    /**
     * 从持久化系统获取用户信息.
     *
     * @return mixed
     */
    protected function getUserFromPersistence()
    {
        return $this->getPersistence(
            $this->getUserPersistenceName()
        );
    }

    /**
     * 将用户信息保存至持久化.
     *
     * @param \Leevel\Database\Ddd\IEntity $user
     *
     * @return array
     */
    protected function setUserToPersistence($user)
    {
        $user = $user->toArray();

        $this->setPersistence(
            $this->getUserPersistenceName(),
            $user
        );

        return $user;
    }

    /**
     * 从数据库获取用户信息.
     *
     * @param int    $userId
     * @param string $password
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    protected function getUserFromDatabase($userId, $password)
    {
        return $this->user->
        where($this->getField('id'), $userId)->

        where($this->getField('password'), $password)->

        where($this->getField('status'), 'enable')->

        setColumns($this->option['field'])->

        getOne();
    }

    /**
     * 获取用户数据.
     *
     * @param int    $userId
     * @param string $password
     *
     * @return mixed
     */
    protected function getPersistenceUser($userId, $password)
    {
        if ($user = $this->getUserFromPersistence()) {
            return $user;
        }

        if ($user = $this->getUserFromDatabase($userId, $password)) {
            return $this->setUserToPersistence($user);
        }

        $this->logout();

        return false;
    }

    /**
     * 认证信息持久化.
     *
     * @param int    $id
     * @param string $password
     * @param mixed  $loginTime
     */
    protected function tokenPersistence($id, $password, $loginTime = null)
    {
        $this->setPersistence(
            $this->getTokenName(),
            $this->implodeTokenData($id, $password),
            $loginTime
        );
    }

    /**
     * 认证信息获取.
     *
     * @return string
     */
    protected function tokenData()
    {
        return $this->getPersistence(
            $this->getTokenName()
        );
    }

    /**
     * 认证信息删除.
     */
    protected function tokenDelete()
    {
        $this->deletePersistence(
            $this->getTokenName()
        );
    }
}
