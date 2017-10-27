<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\auth;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use Exception;
use queryyetsimple\mvc\imodel;
use queryyetsimple\http\request;
use queryyetsimple\cookie\icookie;
use queryyetsimple\support\string;
use queryyetsimple\session\isession;
use queryyetsimple\validate\ivalidate;
use queryyetsimple\encryption\iencryption;
use queryyetsimple\auth\abstracts\connect;
use queryyetsimple\auth\exception\login_failed;
use queryyetsimple\auth\exception\register_failed;
use queryyetsimple\auth\exception\change_password_failed;
use queryyetsimple\auth\interfaces\connect as interfaces_connect;

/**
 * auth.session
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
class session extends connect implements interfaces_connect {
    
    /**
     * user 对象
     *
     * @var \queryyetsimple\mvc\imodel
     */
    protected $oUser;
    
    /**
     * session
     *
     * @var \queryyetsimple\session\isession
     */
    protected $oSession;
    
    /**
     * cookie
     *
     * @var \queryyetsimple\cookie\icookie
     */
    protected $oCookie;
    
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
     * 配置
     *
     * @var array
     */
    protected $arrOption = [ 
            'prefix' => 'q_',
            'cookie' => 'auth',
            'session' => 'auth',
            'field' => 'id,name,nikename,email,mobile' 
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption            
     * @param \queryyetsimple\mvc\imodel $oUser            
     * @param \queryyetsimple\session\isession $oSession            
     * @param \queryyetsimple\cookie\icookie $oCookie            
     * @param \queryyetsimple\encryption\iencryption $oEncryption            
     * @param \queryyetsimple\validate\ivalidate $oValidate            
     */
    public function __construct(array $arrOption, imodel $oUser, isession $oSession, icookie $oCookie, iencryption $oEncryption, ivalidate $oValidate) {
        $this->oUser = $oUser;
        $this->oSession = $oSession;
        $this->oCookie = $oCookie;
        $this->oEncryption = $oEncryption;
        $this->oValidate = $oValidate;
        
        parent::__construct ( $arrOption );
    }
    
    /**
     * 用户是否已经登录
     *
     * @return boolean
     */
    public function isLogin() {
        return $this->getLogin () ? true : false;
    }
    
    /**
     * 获取登录信息
     *
     * @return mixed
     */
    public function getLogin() {
        $sAuth = $this->oCookie->get ( $this->getSessionName () );
        list ( $nUserId, $sPassword ) = $sAuth ? $this->explodeAuthData ( $sAuth ) : [ 
                '',
                '' 
        ];
        
        if ($nUserId && $sPassword) {
            return $this->getSesionUser ( $nUserId, $sPassword ) ?  : false;
        } else {
            $this->logout ();
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
    public function login($mixName, $sPassword, $mixLoginTime = null) {
        $mixName = trim ( $mixName );
        $sPassword = trim ( $sPassword );
        
        if (! $mixName || ! $sPassword)
            throw new login_failed ( __ ( '帐号或者密码不能为空' ) );
        
        $oUser = $this->oUser->where ( $this->parseLoginField ( $mixName ), $mixName )->getOne ();
        
        if (empty ( $oUser->{$this->getField ( 'id' )} ) || $oUser->{$this->getField ( 'status' )} != 'enable')
            throw new login_failed ( __ ( '帐号不存在或者未启用' ) );
        
        if (! $this->checkPassword ( $sPassword, $oUser->{$this->getField ( 'password' )}, $oUser->{$this->getField ( 'random' )} ))
            throw new login_failed ( __ ( '账号或者密码错误' ) );
        
        $this->sendCookie ( $oUser->{$this->getField ( 'id' )}, $oUser->{$this->getField ( 'password' )}, $mixLoginTime );
        
        return $oUser;
    }
    
    /**
     * 登出
     *
     * @return void
     */
    public function logout() {
        $this->oCookie->delete ( $this->getCookieName () );
        $this->oSession->delete ( $this->getSessionName () );
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
    public function changePassword($mixName, $sNewPassword, $sConfirmPassword, $sOldPassword, $bIgnoreOldPassword = false) {
        if (! $mixName) {
            throw new change_password_failed ( __ ( '账号或者 ID 不能为空' ) );
        }
        
        if ($bIgnoreOldPassword === false && $sOldPassword == '') {
            throw new change_password_failed ( __ ( '旧密码不能为空' ) );
        }
        
        if (! $sNewPassword) {
            throw new change_password_failed ( __ ( '新密码不能为空' ) );
        }
        
        if ($sConfirmPassword != $sNewPassword) {
            throw new change_password_failed ( __ ( '两次输入的密码不一致' ) );
        }
        
        $oUser = $this->oUser->where ( $this->parseChangePasswordField ( $mixName ), $mixName )->setColumns ( 'id,status,random,password' )->getOne ();
        
        if (empty ( $oUser->{$this->getField ( 'id' )} ) || $oUser->{$this->getField ( 'status' )} != 'enable')
            throw new change_password_failed ( __ ( '帐号不存在或者未启用' ) );
        
        if (! $bIgnoreOldPassword && ! $this->checkPassword ( $sOldPassword, $oUser->{$this->getField ( 'password' )}, $oUser->{$this->getField ( 'random' )} ))
            throw new change_password_failed ( __ ( '用户输入的旧密码错误' ) );
        
        try {
            $oUser->password = $this->encodePassword ( $sNewPassword, $oUser->random );
            $oUser->update ();
            
            return $oUser;
        } catch ( Exception $oE ) {
            throw new change_password_failed ( $oE->getMessage () );
        }
    }
    
    /**
     * 注册用户
     *
     * @param string $strName            
     * @param string $strPassword            
     * @param string $strComfirmPassword            
     * @param string $strNikename            
     * @param stringstring $strIp            
     * @param string $strEmail            
     * @param string $strMobile            
     * @return mixed
     */
    public function registerUser($strName, $strPassword, $strComfirmPassword, $strNikename = null, $strIp = null, $strEmail = null, $strMobile = null) {
        $strName = trim ( $strName );
        $strNikename = trim ( $strNikename );
        $strPassword = trim ( $strPassword );
        $strComfirmPassword = trim ( $strComfirmPassword );
        $strEmail = trim ( $strEmail );
        $strMobile = trim ( $strMobile );
        
        if (! $strName || $strName != addslashes ( $strName )) {
            throw new register_failed ( __ ( '用户名不能为空或包含非法字符' ) );
        }
        
        if (! $strPassword || $strPassword != addslashes ( $strPassword ) || strpos ( $strPassword, "\n" ) !== false || strpos ( $strPassword, "\r" ) !== false || strpos ( $strPassword, "\t" ) !== false) {
            throw new register_failed ( __ ( '密码不能为空或包含非法字符' ) );
        }
        
        if ($strPassword != $strComfirmPassword) {
            throw new register_failed ( __ ( '两次输入的密码不一致' ) );
        }
        
        try {
            $oUser = $this->oUser->forceProp ( 'name', $strName )->ifs ( $strNikename )->forceProp ( 'nikename', $strNikename )->endIfs ()->forceProp ( 'random', $strRandom = string::randAlphaNum ( 6 ) )->forceProp ( 'password', $this->encodePassword ( $strPassword, $strRandom ) )->ifs ( $strEmail )->forceProp ( 'email', $strEmail )->endIfs ()->ifs ( $strMobile )->forceProp ( 'mobile', $strMobile )->endIfs ()->ifs ( $strIp )->forceProp ( 'register_ip', $strIp )->endIfs ()->create ();
            
            if (empty ( $oUser->id )) {
                throw new register_failed ( __ ( '注册失败' ) );
            }
            
            return $oUser;
        } catch ( Exception $oE ) {
            throw new register_failed ( $oE->getMessage () );
        }
    }
    
    /**
     * 获取 session 数据
     *
     * @param int $nUserId            
     * @param string $sPassword            
     * @return mixed
     */
    protected function getSesionUser($nUserId, $sPassword) {
        if ($arrUser = $this->getUserFromSession ()) {
            return $arrUser;
        }
        
        if ($oUser = $this->getUserFromDatabase ( $nUserId, $sPassword )) {
            return $this->setUserToSession ( $oUser );
        } else {
            $this->logout ();
            return false;
        }
    }
    
    /**
     * 从 session 获取用户信息
     *
     * @return mixed
     */
    protected function getUserFromSession() {
        return $this->oSession->get ( $this->getSessionName () );
    }
    
    /**
     * 将用户信息保存至 session
     *
     * @param \queryyetsimple\mvc\imodel $oUser            
     * @return array
     */
    protected function setUserToSession($oUser) {
        $oUser = $oUser->toArray ();
        $this->oSession->set ( $this->getSessionName (), $oUser );
        return $oUser;
    }
    
    /**
     * 从数据库获取用户信息
     *
     * @param int $nUserId            
     * @param string $sPassword            
     * @return void
     */
    protected function getUserFromDatabase($nUserId, $sPassword) {
        return $this->oUser->where ( $this->getField ( 'id' ), $nUserId )->where ( $this->getField ( 'password' ), $sPassword )->where ( $this->getField ( 'status' ), 'enable' )->setColumns ( $this->getOption ( 'field' ) )->getOne ();
    }
    
    /**
     * COOKIE 记住登录状态
     *
     * @param int $intId            
     * @param string $strPassword            
     * @param mixed $mixLoginTime            
     * @return void
     */
    protected function sendCookie($intId, $strPassword, $mixLoginTime = null) {
        $this->oCookie->set ( $this->getCookieName (), $this->implodeAuthData ( $intId, $strPassword ), [ 
                'expire' => $mixLoginTime 
        ] );
    }
    
    /**
     * 验证数据分离
     *
     * @param string $sAuth            
     * @return string
     */
    protected function explodeAuthData($sAuth) {
        return explode ( "\t", $this->oEncryption->decrypt ( $sAuth ) );
    }
    
    /**
     * 验证数据组合
     *
     * @param int $intId            
     * @param string $strPassword            
     * @return string
     */
    protected function implodeAuthData($intId, $strPassword) {
        return $this->oEncryption->encrypt ( $intId . "\t" . $strPassword );
    }
    
    /**
     * 获取 session 名字
     *
     * @return string
     */
    protected function getSessionName() {
        return $this->getOption ( 'prefix' ) . $this->getOption ( 'session' );
    }
    
    /**
     * 获取 cookie 名字
     *
     * @return string
     */
    protected function getCookieName() {
        return $this->getOption ( 'prefix' ) . $this->getOption ( 'cookie' );
    }
    
    /**
     * 解析登录字段
     *
     * @param string $sName            
     * @return string
     */
    protected function parseLoginField($sName) {
        return $this->oValidate->email ( $sName ) ? $this->getField ( 'email' ) : ($this->oValidate->mobile ( $sName ) ? $this->getField ( 'mobile' ) : $this->getField ( 'name' ));
    }
    
    /**
     * 解析修改密码字段
     *
     * @param string $sName            
     * @return string
     */
    protected function parseChangePasswordField($sName) {
        return is_numeric ( $sName ) ? $this->getField ( 'id' ) : $this->getField ( 'name' );
    }
    
    /**
     * 验证密码是否正确
     *
     * @param string $sSourcePassword            
     * @param string $sPassword            
     * @param string $sRandom            
     * @return boolean
     */
    protected function checkPassword($sSourcePassword, $sPassword, $sRandom) {
        return $this->encodePassword ( $sSourcePassword, $sRandom ) == $sPassword;
    }
    
    /**
     * 加密密码
     *
     * @param string $sSourcePassword            
     * @param string $sRandom            
     * @return string
     */
    protected function encodePassword($sSourcePassword, $sRandom) {
        return md5 ( md5 ( $sSourcePassword ) . $sRandom );
    }
}
