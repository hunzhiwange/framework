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
namespace queryyetsimple\auth;

use queryyetsimple\{
    mvc\imodel,
    support\option,
    validate\ivalidate,
    encryption\iencryption
};

/**
 * connect 驱动抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
abstract class aconnect
{
    use option;

    /**
     * user 对象
     *
     * @var \queryyetsimple\mvc\imodel
     */
    protected $oUser;

    /**
     * 加密
     *
     * @var \queryyetsimple\encryption\iencryption
     */
    protected $oEncryption;

    /**
     * 验证
     *
     * @var \queryyetsimple\validate\ivalidate
     */
    protected $oValidate;

    /**
     * 是否已经设置过登录字段
     *
     * @var boolean
     */
    protected $booSetField = false;

    /**
     * 认证名字
     *
     * @var string
     */
    protected $strTokenName;

    /**
     * 用户持久化名字
     *
     * @return string
     */
    protected $strUserPersistenceName;

    /**
     * 锁定名字
     *
     * @return string
     */
    protected $strLockName;

    /**
     * 登录字段设置
     *
     * @var array
     */
    protected $arrField = [
        'id' => 'id',
        'name' => 'name',
        'nikename' => 'nikename',
        'random' => 'random',
        'email' => 'email',
        'mobile' => 'mobile',
        'password' => 'password',
        'register_ip' => 'register_ip',
        'login_time' => 'login_time',
        'login_ip' => 'login_ip',
        'login_count' => 'login_count',
        'status' => 'status'
    ];

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'user_persistence' => 'user_persistence',
        'token_persistence' => 'token_persistence',
        'lock_persistence' => 'lock_persistence',
        'field' => 'id,name,nikename,email,mobile'
    ];

    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(imodel $oUser, iencryption $oEncryption, ivalidate $oValidate, array $arrOption = [])
    {
        $this->oUser = $oUser;
        $this->oEncryption = $oEncryption;
        $this->oValidate = $oValidate;

        $this->options($arrOption);
    }

    /**
     * 用户是否已经登录
     *
     * @return boolean
     */
    public function isLogin()
    {
        return $this->getLogin() ? true : false;
    }

    /**
     * 获取登录信息
     *
     * @return mixed
     */
    public function getLogin()
    {
        $sToken = $this->tokenData();
        list($nUserId, $sPassword) = $sToken ? $this->explodeTokenData($sToken) : [
            '',
            ''
        ];

        if ($nUserId && $sPassword) {
            return $this->getPersistenceUser($nUserId, $sPassword) ?  : false;
        } else {
            $this->logout();
            return false;
        }
    }

    /**
     * 登录验证
     *
     * @param mixed $mixName
     * @param string $sPassword
     * @param mixed $mixLoginTime
     * @return \queryyetsimple\mvc\imodel|void
     */
    public function login($mixName, $sPassword, $mixLoginTime = null)
    {
        $oUser = $this->onlyValidate($mixName, $sPassword);

        $this->setLoginTokenName($oUser);

        $this->tokenPersistence($oUser->{$this->getField('id')}, $oUser->{$this->getField('password')}, $mixLoginTime);

        return $oUser;
    }

    /**
     * 仅仅验证登录用户和密码是否正确
     *
     * @param mixed $mixName
     * @param string $sPassword
     * @return \queryyetsimple\mvc\imodel|void
     */
    public function onlyValidate($mixName, $sPassword)
    {
        $mixName = trim($mixName);
        $sPassword = trim($sPassword);

        if (! $mixName || ! $sPassword) {
            throw new login_failed(__('帐号或者密码不能为空'));
        }

        $oUser = $this->oUser->where($this->parseLoginField($mixName), $mixName)->getOne();

        if (empty($oUser->{$this->getField('id')}) || $oUser->{$this->getField('status')} != 'enable') {
            throw new login_failed(__('帐号不存在或者未启用'));
        }

        if (! $this->checkPassword($sPassword, $oUser->{$this->getField('password')}, $oUser->{$this->getField('random')})) {
            throw new login_failed(__('账号或者密码错误'));
        }

        return $oUser;
    }

    /**
     * 登出
     *
     * @return void
     */
    public function logout()
    {
        $this->deletePersistence($this->getUserPersistenceName());
        $this->deletePersistence($this->getTokenName());
        $this->deletePersistence($this->getLockName());
    }

    /**
     * 是否处于锁定状态
     *
     * @return boolean
     */
    public function isLock()
    {
        return $this->getPersistence($this->getLockName()) == 'locked';
    }

    /**
     * 锁定登录
     *
     * @param mixed $mixLoginTime
     * @return void
     */
    public function lock($mixLoginTime = null)
    {
        $this->setPersistence($this->getLockName(), 'locked', $mixLoginTime);
    }

    /**
     * 解锁
     *
     * @return void
     */
    public function unlock()
    {
        $this->deletePersistence($this->getLockName());
    }

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
    public function changePassword($mixName, $sNewPassword, $sConfirmPassword, $sOldPassword, $bIgnoreOldPassword = false)
    {
        if (! $mixName) {
            throw new change_password_failed(__('账号或者 ID 不能为空'));
        }

        if ($bIgnoreOldPassword === false && $sOldPassword == '') {
            throw new change_password_failed(__('旧密码不能为空'));
        }

        if (! $sNewPassword) {
            throw new change_password_failed(__('新密码不能为空'));
        }

        if ($sConfirmPassword != $sNewPassword) {
            throw new change_password_failed(__('两次输入的密码不一致'));
        }

        $oUser = $this->oUser->where($this->parseChangePasswordField($mixName), $mixName)->setColumns('id,status,random,password')->getOne();

        if (empty($oUser->{$this->getField('id')}) || $oUser->{$this->getField('status')} != 'enable') {
            throw new change_password_failed(__('帐号不存在或者未启用'));
        }

        if (! $bIgnoreOldPassword && ! $this->checkPassword($sOldPassword, $oUser->{$this->getField('password')}, $oUser->{$this->getField('random')})) {
            throw new change_password_failed(__('用户输入的旧密码错误'));
        }

        try {
            $oUser->password = $this->encodePassword($sNewPassword, $oUser->random);
            $oUser->update();

            return $oUser;
        } catch (Exception $oE) {
            throw new change_password_failed($oE->getMessage());
        }
    }

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
    public function registerUser($strName, $strPassword, $strComfirmPassword, $strNikename = null, $strIp = null, $strEmail = null, $strMobile = null)
    {
        $strName = trim($strName);
        $strNikename = trim($strNikename);
        $strPassword = trim($strPassword);
        $strComfirmPassword = trim($strComfirmPassword);
        $strEmail = trim($strEmail);
        $strMobile = trim($strMobile);

        if (! $strName || $strName != addslashes($strName)) {
            throw new register_failed(__('用户名不能为空或包含非法字符'));
        }

        if (! $strPassword || $strPassword != addslashes($strPassword) || strpos($strPassword, "\n") !== false || strpos($strPassword, "\r") !== false || strpos($strPassword, "\t") !== false) {
            throw new register_failed(__('密码不能为空或包含非法字符'));
        }

        if ($strPassword != $strComfirmPassword) {
            throw new register_failed(__('两次输入的密码不一致'));
        }

        try {
            $oUser = $this->oUser->

            forceProp('name', $strName)->

            ifs(! is_null($strNikename))->forceProp('nikename', $strNikename)->endIfs()->

            forceProp('random', $strRandom = str::randAlphaNum(6))->

            forceProp('password', $this->encodePassword($strPassword, $strRandom))->

            ifs(! is_null($strEmail))->forceProp('email', $strEmail)->endIfs()->

            ifs(! is_null($strMobile))->forceProp('mobile', $strMobile)->endIfs()->

            ifs(! is_null($strIp))->forceProp('register_ip', $strIp)->endIfs()->

            create();

            if (empty($oUser->id)) {
                throw new register_failed(__('注册失败'));
            }

            return $oUser;
        } catch (Exception $oE) {
            throw new register_failed($oE->getMessage());
        }
    }

    /**
     * 设置认证名字
     *
     * @param string $strTokenName
     * @return string
     */
    public function setTokenName($strTokenName)
    {
        return $this->strTokenName = $strTokenName;
    }

    /**
     * 取得认证名字
     *
     * @return string
     */
    public function getTokenName()
    {
        if (! is_null($this->strTokenName)) {
            return $this->strTokenName;
        }
        return $this->strTokenName = $this->getOption('token_persistence');
    }

    /**
     * 设置用户信息持久化名字
     *
     * @param string $strUserPersistenceName
     * @return string
     */
    public function setUserPersistenceName($strUserPersistenceName)
    {
        return $this->strUserPersistenceName = $strUserPersistenceName;
    }

    /**
     * 取得用户信息持久化名字
     *
     * @return string
     */
    public function getUserPersistenceName()
    {
        if (! is_null($this->strUserPersistenceName)) {
            return $this->strUserPersistenceName;
        }
        return $this->strUserPersistenceName = $this->getTokenName() . '@' . $this->getOption('user_persistence');
    }

    /**
     * 设置锁定名字
     *
     * @param string $strLockName
     * @return string
     */
    public function setLockName($strLockName)
    {
        return $this->strLockName = $strLockName;
    }

    /**
     * 取得锁定名字
     *
     * @return string
     */
    public function getLockName()
    {
        if (! is_null($this->strLockName)) {
            return $this->strLockName;
        }
        return $this->strLockName = $this->getTokenName() . '@' . $this->getOption('lock_persistence');
    }

    /**
     * 设置字段
     *
     * @param array $arrField
     * @param boolean $booForce
     * @return void
     */
    public function setField(array $arrField, $booForce = false)
    {
        if ($booForce === false && $this->booSetField = true) {
            return;
        }

        $this->booSetField = true;
        foreach ($arrField as $strKey => $strField) {
            if (isset($this->arrField[$strKey])) {
                $this->arrField[$strKey] = $strField;
            }
        }
    }

    /**
     * 获取字段
     *
     * @param array $strField
     * @return mixed
     */
    public function getField($strField)
    {
        return $this->arrField[$strField] ?? null;
    }

    /**
     * 批量获取字段
     *
     * @param array $arrField
     * @param boolean $booFilterNull
     * @return array
     */
    public function getFields(array $arrField, $booFilterNull = true)
    {
        $arrData = [];
        foreach ($arrField as $strField) {
            if (is_null($mixValue = $this->getField($strField)) && $booFilterNull === true) {
                continue;
            }
            $arrData[] = $mixValue;
        }
        return $arrData;
    }

    /**
     * 验证数据分离
     *
     * @param string $sAuth
     * @return mixed
     */
    public function explodeTokenData($sAuth)
    {
        if (! $sAuth) {
            return false;
        }
        $sAuth = $this->oEncryption->decrypt($sAuth);
        return $sAuth ? explode("\t", $sAuth) : false;
    }

    /**
     * 验证数据组合
     *
     * @param mixed $maxIdentifier
     * @param string $strPassword
     * @return string
     */
    public function implodeTokenData($maxIdentifier, $strPassword)
    {
        return $this->oEncryption->encrypt($maxIdentifier . "\t" . $strPassword);
    }

    /**
     * 验证密码是否正确
     *
     * @param string $sSourcePassword
     * @param string $sPassword
     * @param string $sRandom
     * @return boolean
     */
    protected function checkPassword($sSourcePassword, $sPassword, $sRandom)
    {
        return $this->encodePassword($sSourcePassword, $sRandom) == $sPassword;
    }

    /**
     * 加密密码
     *
     * @param string $sSourcePassword
     * @param string $sRandom
     * @return string
     */
    protected function encodePassword($sSourcePassword, $sRandom)
    {
        return md5(md5($sSourcePassword) . $sRandom);
    }

    /**
     * 解析登录字段
     *
     * @param string $sName
     * @return string
     */
    protected function parseLoginField($sName)
    {
        return $this->oValidate->email($sName) ? $this->getField('email') : ($this->oValidate->mobile($sName) ? $this->getField('mobile') : $this->getField('name'));
    }

    /**
     * 解析修改密码字段
     *
     * @param string $sName
     * @return string
     */
    protected function parseChangePasswordField($sName)
    {
        return is_numeric($sName) ? $this->getField('id') : $this->getField('name');
    }

    /**
     * 从持久化系统获取用户信息
     *
     * @return mixed
     */
    protected function getUserFromPersistence()
    {
        return $this->getPersistence($this->getUserPersistenceName());
    }

    /**
     * 将用户信息保存至持久化
     *
     * @param \queryyetsimple\mvc\imodel $oUser
     * @return array
     */
    protected function setUserToPersistence($oUser)
    {
        $oUser = $oUser->toArray();
        $this->setPersistence($this->getUserPersistenceName(), $oUser);
        return $oUser;
    }

    /**
     * 从数据库获取用户信息
     *
     * @param int $nUserId
     * @param string $sPassword
     * @return \queryyetsimple\mvc\imodel
     */
    protected function getUserFromDatabase($nUserId, $sPassword)
    {
        return $this->oUser->

        where($this->getField('id'), $nUserId)->

        where($this->getField('password'), $sPassword)->

        where($this->getField('status'), 'enable')->

        setColumns($this->getOption('field'))->

        getOne();
    }

    /**
     * 获取用户数据
     *
     * @param int $nUserId
     * @param string $sPassword
     * @return mixed
     */
    protected function getPersistenceUser($nUserId, $sPassword)
    {
        if ($arrUser = $this->getUserFromPersistence()) {
            return $arrUser;
        }

        if ($oUser = $this->getUserFromDatabase($nUserId, $sPassword)) {
            return $this->setUserToPersistence($oUser);
        } else {
            $this->logout();
            return false;
        }
    }

    /**
     * 认证信息持久化
     *
     * @param int $intId
     * @param string $strPassword
     * @param mixed $mixLoginTime
     * @return void
     */
    protected function tokenPersistence($intId, $strPassword, $mixLoginTime = null)
    {
        $this->setPersistence($this->getTokenName(), $this->implodeTokenData($intId, $strPassword), $mixLoginTime);
    }

    /**
     * 认证信息获取
     *
     * @return string
     */
    protected function tokenData()
    {
        return $this->getPersistence($this->getTokenName());
    }

    /**
     * 认证信息删除
     *
     * @return void
     */
    protected function tokenDelete()
    {
        $this->deletePersistence($this->getTokenName());
    }
}
