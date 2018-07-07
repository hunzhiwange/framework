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

use Leevel\Database\Ddd\IEntity;
use Leevel\Encryption\IEncryption;
use Leevel\Option\TClass;
use Leevel\Support\Str;
use Leevel\Validate\IValidate;

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
    use TClass;

    /**
     * user 对象
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $user;

    /**
     * 加密.
     *
     * @var \Leevel\Encryption\IEncryption
     */
    protected $encryption;

    /**
     * 验证
     *
     * @var \Leevel\Validate\IValidate
     */
    protected $validate;

    /**
     * 是否已经设置过登录字段.
     *
     * @var bool
     */
    protected $setField = false;

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
     * 锁定名字.
     *
     * @return string
     */
    protected $lockName;

    /**
     * 登录字段设置.
     *
     * @var array
     */
    protected $fields = [
        'id'          => 'id',
        'name'        => 'name',
        'nikename'    => 'nikename',
        'random'      => 'random',
        'email'       => 'email',
        'mobile'      => 'mobile',
        'password'    => 'password',
        'register_ip' => 'register_ip',
        'login_time'  => 'login_time',
        'login_ip'    => 'login_ip',
        'login_count' => 'login_count',
        'status'      => 'status',
    ];

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'user_persistence'  => 'user_persistence',
        'token_persistence' => 'token_persistence',
        'lock_persistence'  => 'lock_persistence',
        'field'             => 'id,name,nikename,email,mobile',
    ];

    /**
     * 构造函数.
     *
     * @param array $option
     */
    public function __construct(IEntity $user, IEncryption $encryption, IValidate $validate, array $option = [])
    {
        $this->user = $user;
        $this->encryption = $encryption;
        $this->validate = $validate;

        $this->options($option);
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

        $this->deletePersistence(
            $this->getLockName()
        );
    }

    /**
     * 是否处于锁定状态
     *
     * @return bool
     */
    public function isLock()
    {
        return 'locked' === $this->getPersistence($this->getLockName());
    }

    /**
     * 锁定登录.
     *
     * @param mixed $loginTime
     */
    public function lock($loginTime = null)
    {
        $this->setPersistence(
            $this->getLockName(),
            'locked',
            $loginTime
        );
    }

    /**
     * 解锁
     */
    public function unlock()
    {
        $this->deletePersistence($this->getLockName());
    }

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
    public function changePassword($name, $newPassword, $confirmPassword, $oldPassword, $ignoreOldPassword = false)
    {
        if (!$name) {
            throw new ChangePasswordFailed(__('账号或者 ID 不能为空'));
        }

        if (false === $ignoreOldPassword && '' === $oldPassword) {
            throw new ChangePasswordFailed(__('旧密码不能为空'));
        }

        if (!$newPassword) {
            throw new ChangePasswordFailed(__('新密码不能为空'));
        }

        if ($confirmPassword !== $newPassword) {
            throw new ChangePasswordFailed(__('两次输入的密码不一致'));
        }

        $user = $this->user->

        where($this->parseChangePasswordField($name), $name)->

        setColumns('id,status,random,password')->

        getOne();

        if (empty($user->{$this->getField('id')}) ||
            'enable' !== $user->{$this->getField('status')}) {
            throw new ChangePasswordFailed(__('帐号不存在或者未启用'));
        }

        if (!$ignoreOldPassword &&
            !$this->checkPassword(
                $oldPassword,
                $user->{$this->getField('password')},
                $user->{$this->getField('random')})) {
            throw new ChangePasswordFailed(__('用户输入的旧密码错误'));
        }

        try {
            $user->password = $this->encodePassword($newPassword, $user->random);
            $user->update();

            return $user;
        } catch (Exception $e) {
            throw new ChangePasswordFailed($e->getMessage());
        }
    }

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
    public function registerUser($name, $password, $comfirmPassword, $nikename = null, $ip = null, $email = null, $mobile = null)
    {
        $name = trim($name);
        $nikename = trim($nikename);
        $password = trim($password);
        $comfirmPassword = trim($comfirmPassword);
        $email = trim($email);
        $mobile = trim($mobile);

        if (!$name ||
            $name !== addslashes($name)) {
            throw new RegisterFailed(__('用户名不能为空或包含非法字符'));
        }

        if (!$password ||
            $password !== addslashes($password) ||
            false !== strpos($password, "\n") ||
            false !== strpos($password, "\r") ||
            false !== strpos($password, "\t")) {
            throw new RegisterFailed(__('密码不能为空或包含非法字符'));
        }

        if ($password !== $comfirmPassword) {
            throw new RegisterFailed(__('两次输入的密码不一致'));
        }

        try {
            $user = $this->user->

            forceProp('name', $name)->

            ifs(null !== $nikename)->forceProp('nikename', $nikename)->endIfs()->

            forceProp('random', $random = Str::randAlphaNum(6))->

            forceProp('password', $this->encodePassword($password, $random))->

            ifs(null !== $email)->forceProp('email', $email)->endIfs()->

            ifs(null !== $mobile)->forceProp('mobile', $mobile)->endIfs()->

            ifs(null !== $ip)->forceProp('register_ip', $ip)->endIfs()->

            create();

            if (empty($user->id)) {
                throw new RegisterFailed(__('注册失败'));
            }

            return $user;
        } catch (Exception $e) {
            throw new RegisterFailed($e->getMessage());
        }
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

        return $this->tokenName = $this->getOption('token_persistence');
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
            $this->getOption('user_persistence');
    }

    /**
     * 设置锁定名字.
     *
     * @param string $lockName
     *
     * @return string
     */
    public function setLockName($lockName)
    {
        return $this->lockName = $lockName;
    }

    /**
     * 取得锁定名字.
     *
     * @return string
     */
    public function getLockName()
    {
        if (null !== $this->lockName) {
            return $this->lockName;
        }

        return $this->lockName = $this->getTokenName().'@'.
            $this->getOption('lock_persistence');
    }

    /**
     * 设置字段.
     *
     * @param array $fields
     * @param bool  $force
     */
    public function setField(array $fields, $force = false)
    {
        if (false === $force && $this->setField = true) {
            return;
        }

        $this->setField = true;

        foreach ($fields as $key => $field) {
            if (isset($this->fields[$key])) {
                $this->fields[$key] = $field;
            }
        }
    }

    /**
     * 获取字段.
     *
     * @param array $field
     *
     * @return mixed
     */
    public function getField($field)
    {
        return $this->fields[$field] ?? null;
    }

    /**
     * 批量获取字段.
     *
     * @param array $fields
     * @param bool  $filterNull
     *
     * @return array
     */
    public function getFields(array $fields, $filterNull = true)
    {
        $data = [];

        foreach ($fields as $field) {
            if (null === ($value = $this->getField($field)) &&
                true === $filterNull) {
                continue;
            }

            $data[] = $value;
        }

        return $data;
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
     * 解析修改密码字段.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseChangePasswordField($name)
    {
        return is_numeric($name) ?
            $this->getField('id') :
            $this->getField('name');
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

        setColumns($this->getOption('field'))->

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
