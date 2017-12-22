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
namespace queryyetsimple\session;

use RuntimeException;
use BadMethodCallException;
use SessionHandlerInterface;
use queryyetsimple\support\{
    option,
    assert
};

/**
 * session 仓储
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.17
 * @version 1.0
 */
class session implements isession
{
    use option;

    /**
     * 配置
     *
     * @var array
     */
    protected $objConnect;

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'default' => null,
        'prefix' => 'q_',
        'id' => null,
        'name' => null,
        'cookie_domain' => null,
        'cache_limiter' => null,
        'expire' => 86400,
        'cookie_lifetime' => null,
        'gc_maxlifetime' => null,
        'save_path' => null,
        'use_trans_sid' => null,
        'gc_probability' => null
    ];

    /**
     * 构造函数
     *
     * @param \SessionHandlerInterface|null $objConnect
     * @param array $arrOption
     * @return void
     */
    public function __construct(SessionHandlerInterface $objConnect = null, array $arrOption = [])
    {
        $this->objConnect = $objConnect;
        $this->options($arrOption);
    }

    /**
     * 启动 session
     *
     * @return $this
     */
    public function start()
    {
        if ($this->isStart()) {
            return $this;
        }

        // 设置 session 不自动启动
        ini_set('session.auto_start', 0);

        // 设置 session id
        if ($this->getOption('id')) {
            session_id($this->getOption('id'));
        } else {
            if (is_null($this->parseSessionId())) {
                $this->sessionId(uniqid(dechex(mt_rand())));
            }
        }

        // cookie domain
        if ($this->getOption('cookie_domain')) {
            $this->cookieDomain($this->getOption('cookie_domain'));
        }

        // session name
        if ($this->getOption('name')) {
            $this->sessionName($this->getOption('name'));
        }

        // cache expire
        if ($this->getOption('expire')) {
            $this->cacheExpire($this->getOption('expire'));
        }

        // gc maxlifetime
        if ($this->getOption('gc_maxlifetime')) {
            $this->gcMaxlifetime($this->getOption('gc_maxlifetime'));
        }

        // cookie lifetime
        if ($this->getOption('cookie_lifetime')) {
            $this->cookieLifetime($this->getOption('cookie_lifetime'));
        }

        // cache limiter
        if ($this->getOption('cache_limiter')) {
            $this->cacheLimiter($this->getOption('cache_limiter'));
        }

        // save path
        if ($this->getOption('save_path')) {
            $this->savePath($this->getOption('save_path'));
        }

        // use_trans_sid
        if ($this->getOption('use_trans_sid')) {
            $this->useTransSid($this->getOption('use_trans_sid'));
        }

        // gc_probability
        if ($this->getOption('gc_probability')) {
            $this->gcProbability($this->getOption('gc_probability'));
        }

        // 驱动
        if ($this->objConnect && ! session_set_save_handler($this->objConnect)) {
            throw new RuntimeException(sprintf('Session drive %s settings failed.', get_class($this->objConnect)));
        }

        // 启动 session
        if (! session_start()) {
            throw new RuntimeException('Session start failed');
        }

        return $this;
    }

    /**
     * 设置 session
     *
     * @param string $sName
     * @param mxied $mixValue
     * @return void
     */
    public function set($sName, $mixValue)
    {
        $this->checkStart();

        assert::string($sName);
        $sName = $this->getName($sName);
        $_SESSION[$sName] = $mixValue;
    }

    /**
     * 批量插入
     *
     * @param string|array $mixKey
     * @param mixed $mixValue
     * @return void
     */
    public function put($mixKey, $mixValue = null)
    {
        $this->checkStart();

        if (! is_array($mixKey)) {
            $mixKey = [
                $mixKey => $mixValue
            ];
        }

        foreach ($mixKey as $strKey => $mixValue) {
            $this->set($strKey, $mixValue);
        }
    }

    /**
     * 数组插入数据
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @return void
     */
    public function push($strKey, $mixValue)
    {
        $arr = $this->get($strKey, []);
        $arr[] = $mixValue;
        $this->set($strKey, $arr);
    }

    /**
     * 合并元素
     *
     * @param string $strKey
     * @param array $arrValue
     * @return void
     */
    public function merge($strKey, array $arrValue)
    {
        $this->set($strKey, array_unique(array_merge($this->get($strKey, []), $arrValue)));
    }

    /**
     * 弹出元素
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @return void
     */
    public function pop($strKey, array $arrValue)
    {
        $this->set($strKey, array_diff($this->get($strKey, []), $arrValue));
    }

    /**
     * 数组插入键值对数据
     *
     * @param string $strKey
     * @param mixed $mixKey
     * @param mixed $mixValue
     * @return void
     */
    public function arrays($strKey, $mixKey, $mixValue = null)
    {
        $arr = $this->get($strKey, []);
        if (is_string($mixKey)) {
            $arr[$mixKey] = $mixValue;
        } elseif (is_array($mixKey)) {
            $arr = array_merge($arr, $mixKey);
        }
        $this->set($strKey, $arr);
    }

    /**
     * 数组键值删除数据
     *
     * @param string $strKey
     * @param mixed $mixKey
     * @return void
     */
    public function arraysDelete($strKey, $mixKey)
    {
        $arr = $this->get($strKey, []);
        if (! is_array($mixKey)) {
            $mixKey = [
                $mixKey
            ];
        }
        foreach ($mixKey as $strFoo) {
            if (isset($arr[$strFoo])) {
                unset($arr[$strFoo]);
            }
        }
        $this->set($strKey, $arr);
    }

    /**
     * 取回 session
     *
     * @param string $sName
     * @param mixed $mixValue
     * @return mxied
     */
    public function get($sName, $mixValue = null)
    {
        $this->checkStart();

        assert::string($sName);
        $sName = $this->getName($sName);
        return $_SESSION[$sName] ?? $mixValue;
    }

    /**
     * 删除 session
     *
     * @param string $sName
     * @param boolean $bPrefix
     * @return bool
     */
    public function delete($sName, $bPrefix = true)
    {
        $this->checkStart();

        assert::string($sName);
        if ($bPrefix) {
            $sName = $this->getName($sName);
        }

        if (isset($_SESSION[$sName])) {
            unset($_SESSION[$sName]);
        }

        return true;
    }

    /**
     * 是否存在 session
     *
     * @param string $sName
     * @return boolean
     */
    public function has($sName)
    {
        $this->checkStart();

        assert::string($sName);
        $sName = $this->getName($sName);
        return isset($_SESSION[$sName]);
    }

    /**
     * 删除 session
     *
     * @param boolean $bPrefix
     * @return void
     */
    public function clear($bPrefix = true)
    {
        $this->checkStart();

        $strPrefix = $this->getOption('prefix');
        foreach ($_SESSION as $sKey => $Val) {
            if ($bPrefix === true && $strPrefix && strpos($sKey, $strPrefix) === 0) {
                $this->delete($sKey, false);
            } else {
                $this->delete($sKey, false);
            }
        }
    }

    /**
     * 闪存一个数据，当前请求和下一个请求可用
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @return void
     */
    public function flash($strKey, $mixValue = null)
    {
        if (is_null($mixValue)) {
            return $this->getFlash($strKey);
        } else {
            $this->set($this->flashDataKey($strKey), $mixValue);
            $this->mergeNewFlash([
                $strKey
            ]);
            $this->popOldFlash([
                $strKey
            ]);
        }
    }

    /**
     * 批量闪存数据，当前请求和下一个请求可用
     *
     * @param array $arrFlash
     * @return void
     */
    public function flashs(array $arrFlash)
    {
        foreach ($arrFlash as $strKey => $mixValue) {
            $this->flash($strKey, $mixValue);
        }
    }

    /**
     * 闪存一个 flash 用于当前请求使用，下一个请求将无法获取
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @return void
     */
    public function nowFlash($strKey, $mixValue)
    {
        $this->set($this->flashDataKey($strKey), $mixValue);
        $this->mergeOldFlash([
            $strKey
        ]);
    }

    /**
     * 保持所有闪存数据
     *
     * @return void
     */
    public function rebuildFlash()
    {
        $this->mergeNewFlash($this->get($this->flashOldKey(), []));
        $this->set($this->flashOldKey(), []);
    }

    /**
     * 保持闪存数据
     *
     * @param mixed $mixKey
     * @return void
     */
    public function keepFlash($mixKey)
    {
        $mixKey = is_array($mixKey) ? $mixKey : func_get_args();
        $this->mergeNewFlash($mixKey);
        $this->popOldFlash($mixKey);
    }

    /**
     * 返回闪存数据
     *
     * @param string $strKey
     * @param mixed $mixDefault
     * @return mixed
     */
    public function getFlash($strKey, $mixDefault = null)
    {
        if (strpos($strKey, '\\') !== false) {
            return $this->getPartData($strKey, $mixDefault);
        } else {
            return $this->get($this->flashDataKey($strKey), $mixDefault);
        }
    }

    /**
     * 删除闪存数据
     *
     * @param mixed $mixKey
     * @return void
     */
    public function deleteFlash($mixKey)
    {
        $mixKey = is_array($mixKey) ? $mixKey : func_get_args();

        foreach ($mixKey as $strKey) {
            $this->delete($this->flashDataKey($strKey));
        }

        $this->mergeOldFlash($mixKey);
        $this->popNewFlash($mixKey);
    }

    /**
     * 清理所有闪存数据
     *
     * @return void
     */
    public function clearFlash()
    {
        foreach ($this->get($this->flashNewKey(), []) as $strNew) {
            $this->deleteFlash($strNew);
        }
    }

    /**
     * 程序执行结束清理 flash
     *
     * @return void
     */
    public function unregisterFlash()
    {
        if ($this->isStart()) {
            $arr = $this->get($this->flashNewKey(), []);
            $arrOld = $this->get($this->flashOldKey(), []);

            foreach ($arrOld as $strOld) {
                $this->delete($this->flashDataKey($strOld));
            }

            $this->delete($this->flashNewKey());
            $this->set($this->flashOldKey(), $arr);

            unset($arr, $arrOld);
        }
    }

    /**
     * 获取前一个请求地址
     *
     * @return string|null
     */
    public function prevUrl()
    {
        return $this->get($this->prevUrlKey());
    }

    /**
     * 设置前一个请求地址
     *
     * @param string $strUrl
     * @return void
     */
    public function setPrevUrl($strUrl)
    {
        return $this->set($this->prevUrlKey(), $strUrl);
    }

    /**
     * 暂停 session
     *
     * @return void
     */
    public function pause()
    {
        $this->checkStart();
        session_write_close();
    }

    /**
     * 终止会话
     *
     * @return bool
     */
    public function destroy()
    {
        $this->checkStart();

        $this->clear(false);

        if (isset($_COOKIE[$this->sessionName()])) {
            setcookie($this->sessionName(), '', time() - 42000, '/');
        }

        session_destroy();
    }

    /**
     * 获取解析 session_id
     *
     * @param string $sId
     * @return string
     */
    public function parseSessionId()
    {
        if (($sId = $this->sessionId())) {
            return $sId;
        }
        if ($this->useCookies()) {
            if (isset($_COOKIE[$this->sessionName()])) {
                return $_COOKIE[$this->sessionName()];
            }
        } else {
            if (isset($_GET[$this->sessionName()])) {
                return $_GET[$this->sessionName()];
            }
            if (isset($_POST[$this->sessionName()])) {
                return $_POST[$this->sessionName()];
            }
        }
        return null;
    }

    /**
     * 设置 save path
     *
     * @param string $sSavePath
     * @return string
     */
    public function savePath($sSavePath = null)
    {
        return ! empty($sSavePath) ? session_save_path($sSavePath) : session_save_path();
    }

    /**
     * 设置 cache limiter
     *
     * @param string $strCacheLimiter
     * @return string
     */
    public function cacheLimiter($strCacheLimiter = null)
    {
        return isset($strCacheLimiter) ? session_cache_limiter($strCacheLimiter) : session_cache_limiter();
    }

    /**
     * 设置 cache expire
     *
     * @param int $nExpireSecond
     * @return void
     */
    public function cacheExpire($nExpireSecond = null)
    {
        return isset($nExpireSecond) ? session_cache_expire(intval($nExpireSecond)) : session_cache_expire();
    }

    /**
     * session_name
     *
     * @param string $sName
     * @return string
     */
    public function sessionName($sName = null)
    {
        return isset($sName) ? session_name($sName) : session_name();
    }

    /**
     * session id
     *
     * @param string $sId
     * @return string
     */
    public function sessionId($sId = null)
    {
        return isset($sId) ? session_id($sId) : session_id();
    }

    /**
     * session 的 cookie_domain 设置
     *
     * @param string $sSessionDomain
     * @return string
     */
    public function cookieDomain($sSessionDomain = null)
    {
        $sReturn = ini_get('session.cookie_domain');
        if (! empty($sSessionDomain)) {
            ini_set('session.cookie_domain', $sSessionDomain); // 跨域访问 session
        }
        return $sReturn;
    }

    /**
     * session 是否使用 cookie
     *
     * @param boolean $bUseCookies
     * @return boolean
     */
    public function useCookies($bUseCookies = null)
    {
        $booReturn = ini_get('session.use_cookies') ? true : false;
        if (isset($bUseCookies)) {
            ini_set('session.use_cookies', $bUseCookies ? 1 : 0);
        }
        return $booReturn;
    }

    /**
     * 客户端禁用 cookie 可以开启这个项
     *
     * @param string $nUseTransSid
     * @return boolean
     */
    public function useTransSid($nUseTransSid = null)
    {
        $booReturn = ini_get('session.use_trans_sid') ? true : false;
        if (isset($nUseTransSid)) {
            ini_set('session.use_trans_sid', $nUseTransSid ? 1 : 0);
        }
        return $booReturn;
    }

    /**
     * 设置过期 cookie lifetime
     *
     * @param int $nCookieLifeTime
     * @return int
     */
    public function cookieLifetime($nCookieLifeTime)
    {
        $nReturn = ini_get('session.cookie_lifetime');
        if (isset($nCookieLifeTime) && intval($nCookieLifeTime) >= 1) {
            ini_set('session.cookie_lifetime', intval($nCookieLifeTime));
        }
        return $nReturn;
    }

    /**
     * gc maxlifetime
     *
     * @param int $nGcMaxlifetime
     * @return int
     */
    public function gcMaxlifetime($nGcMaxlifetime = null)
    {
        $nReturn = ini_get('session.gc_maxlifetime');
        if (isset($nGcMaxlifetime) && intval($nGcMaxlifetime) >= 1) {
            ini_set('session.gc_maxlifetime', intval($nGcMaxlifetime));
        }
        return $nReturn;
    }

    /**
     * session 垃圾回收概率分子 (分母为 session.gc_divisor)
     *
     * @param int $nGcProbability
     * @return int
     */
    public function gcProbability($nGcProbability = null)
    {
        $nReturn = ini_get('session.gc_probability');
        if (isset($nGcProbability) && intval($nGcProbability) >= 1 && intval($nGcProbability) <= 100) {
            ini_set('session.gc_probability', intval($nGcProbability));
        }
        return $nReturn;
    }

    /**
     * 返回 session 名字
     *
     * @param string $sName
     * @return string
     */
    protected function getName($sName)
    {
        return $this->getOption('prefix') . $sName;
    }

    /**
     * session 是否已经启动
     *
     * @return boolean
     */
    protected function isStart()
    {
        return isset($_SESSION);
    }

    /**
     * 验证 session 是否开启
     *
     * @return void
     */
    protected function checkStart()
    {
        if (! $this->isStart()) {
            throw new RuntimeException('Session is not start yet');
        }
    }

    /**
     * 弹出旧闪存 KEY
     *
     * @param array $arrKey
     * @return void
     */
    protected function popOldFlash(array $arrKey)
    {
        $this->pop($this->flashOldKey(), $arrKey);
    }

    /**
     * 合并旧闪存 KEY
     *
     * @param array $arrKey
     * @return void
     */
    protected function mergeOldFlash(array $arrKey)
    {
        $this->merge($this->flashOldKey(), $arrKey);
    }

    /**
     * 弹出新闪存 KEY
     *
     * @param array $arrKey
     * @return void
     */
    protected function popNewFlash(array $arrKey)
    {
        $this->pop($this->flashNewKey(), $arrKey);
    }

    /**
     * 合并新闪存 KEY
     *
     * @param array $arrKey
     * @return void
     */
    protected function mergeNewFlash(array $arrKey)
    {
        $this->merge($this->flashNewKey(), $arrKey);
    }

    /**
     * 返回部分闪存数据
     *
     * @param string $strKey
     * @param mixed $mixDefault
     * @return mixed
     */
    protected function getPartData($strKey, $mixDefault = null)
    {
        list($strKey, $strName) = explode('\\', $strKey);
        $mixValue = $this->get($this->flashDataKey($strKey));

        if (is_array($mixValue)) {
            $arrParts = explode('.', $strName);
            foreach ($arrParts as $sPart) {
                if (! isset($mixValue[$sPart])) {
                    return $mixDefault;
                }
                $mixValue = &$mixValue[$sPart];
            }
            return $mixValue;
        } else {
            return $mixDefault;
        }
    }

    /**
     * 闪存值 KEY
     *
     * @param string $strKey
     * @return string
     */
    protected function flashDataKey($strKey)
    {
        return 'flash.data.' . $strKey;
    }

    /**
     * 新值闪存 KEY
     *
     * @return string
     */
    protected function flashNewKey()
    {
        return 'flash.new.key';
    }

    /**
     * 旧值闪存 KEY
     *
     * @return string
     */
    protected function flashOldKey()
    {
        return 'flash.old.key';
    }

    /**
     * 前一个页面 KEY
     *
     * @return string
     */
    protected function prevUrlKey()
    {
        return 'prev.url.key';
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        if (is_null($this->objConnect)) {
            throw new BadMethodCallException(sprintf('Method %s is not exits.', $sMethod));
        }

        return $this->objConnect->$sMethod(...$arrArgs);
    }
}
