<?php
// [$QueryPHP] A PHP Framework Since 2010.10.03. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\session\interfaces;

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

/**
 * store 接口
 *
 * @author Xiangmin Liu<635750556@qq.com>
 * @package $$
 * @since 2017.04.11
 * @version 1.0
 */
interface store {
    
    /**
     * 启动 session
     *
     * @return void
     */
    public function start();
    
    /**
     * 设置 session
     *
     * @param string $sName            
     * @param mxied $mixValue            
     * @return void
     */
    public function set($sName, $mixValue);
    
    /**
     * 批量插入
     *
     * @param string|array $mixKey            
     * @param mixed $mixValue            
     * @return void
     */
    public function put($mixKey, $mixValue = null);
    
    /**
     * 取回 session
     *
     * @param string $sName            
     * @param mixed $mixValue            
     * @return mxied
     */
    public function get($sName, $mixValue = null);
    
    /**
     * 删除 session
     *
     * @param string $sName            
     * @return bool
     */
    public function delete($sName);
    
    /**
     * 是否存在 session
     *
     * @param string $sName            
     * @return boolean
     */
    public function has($sName);
    
    /**
     * 删除 session
     *
     * @param boolean $bPrefix            
     * @return void
     */
    public function clear($bPrefix = true);
    
    /**
     * 暂停 session
     *
     * @return void
     */
    public function pause();
    
    /**
     * 终止会话
     *
     * @return bool
     */
    public function destroy();
    
    /**
     * 获取解析 session_id
     *
     * @param string $sId            
     * @return string
     */
    public function parseSessionId();
    
    /**
     * 设置 save path
     *
     * @param string $sSavePath            
     * @return string
     */
    public function savePath($sSavePath = null);
    
    /**
     * 设置 cache limiter
     *
     * @param string $strCacheLimiter            
     * @return string
     */
    public function cacheLimiter($strCacheLimiter = null);
    
    /**
     * 设置 cache expire
     *
     * @param int $nExpireSecond            
     * @return void
     */
    public function cacheExpire($nExpireSecond = null);
    
    /**
     * session_name
     *
     * @param string $sName            
     * @return string
     */
    public function sessionName($sName = null);
    
    /**
     * session id
     *
     * @param string $sId            
     * @return string
     */
    public function sessionId($sId = null);
    
    /**
     * session 的 cookie_domain 设置
     *
     * @param string $sSessionDomain            
     * @return string
     */
    public function cookieDomain($sSessionDomain = null);
    
    /**
     * session 是否使用 cookie
     *
     * @param boolean $bUseCookies            
     * @return boolean
     */
    public function useCookies($bUseCookies = null);
    
    /**
     * 客户端禁用 cookie 可以开启这个项
     *
     * @param string $nUseTransSid            
     * @return boolean
     */
    public function useTransSid($nUseTransSid = null);
    
    /**
     * 设置过期 cookie lifetime
     *
     * @param int $nCookieLifeTime            
     * @return int
     */
    public function cookieLifetime($nCookieLifeTime);
    
    /**
     * gc maxlifetime
     *
     * @param int $nGcMaxlifetime            
     * @return int
     */
    public function gcMaxlifetime($nGcMaxlifetime = null);
    
    /**
     * session 垃圾回收概率分子 (分母为 session.gc_divisor)
     *
     * @param int $nGcProbability            
     * @return int
     */
    public function gcProbability($nGcProbability = null);
}
