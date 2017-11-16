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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\cookie;

use Exception;
use queryyetsimple\support\option;

/**
 * cookie 封装
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class cookie implements icookie
{
    use option;
    
    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'prefix' => 'q_', 
        'expire' => 86400, 
        'domain' => '', 
        'path' => '/', 
        'httponly' => false
    ];
    
    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        $this->options($arrOption);
    }
    
    /**
     * 设置 COOKIE
     *
     * @param string $sName
     * @param string $mixValue
     * @param array $arrOption
     * @return void
     */
    public function set($sName, $mixValue = '', array $arrOption = [])
    {
        $arrOption = $this->getOptions($arrOption);
        
        if (is_array($mixValue)) {
            $mixValue = json_encode($mixValue);
        }
        
        if (! is_scalar($mixValue) && ! is_null($mixValue)) {
            throw new Exception('Cookie value must be scalar or null');
        }
        
        $sName = $arrOption['prefix'] . $sName;
        
        if ($mixValue === null || $arrOption['expire'] < 0) {
            if (isset($_COOKIE[$sName])) {
                unset($_COOKIE[$sName]);
            }
        } else {
            $_COOKIE[$sName] = $mixValue;
        }
        
        $arrOption['expire'] = $arrOption['expire'] > 0 ? time() + $arrOption['expire'] : ($arrOption['expire'] < 0 ? time() - 31536000 : 0);
        setcookie($sName, $mixValue, $arrOption['expire'], $arrOption['path'], $arrOption['domain'], ! empty($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) == 'ON', $arrOption['httponly']);
    }
    
    /**
     * 批量插入
     *
     * @param string|array $mixKey
     * @param mixed $mixValue
     * @param array $arrOption
     * @return void
     */
    public function put($mixKey, $mixValue = null, array $arrOption = [])
    {
        if (! is_array($mixKey)) {
            $mixKey = [
                $mixKey => $mixValue
            ];
        }
        
        foreach ($mixKey as $strKey => $mixValue) {
            $this->set($strKey, $mixValue, $arrOption);
        }
    }
    
    /**
     * 数组插入数据
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @param array $arrOption
     * @return void
     */
    public function push($strKey, $mixValue, array $arrOption = [])
    {
        $arr = $this->get($strKey, [], $arrOption);
        $arr[] = $mixValue;
        $this->set($strKey, $arr, $arrOption);
    }
    
    /**
     * 合并元素
     *
     * @param string $strKey
     * @param array $arrValue
     * @param array $arrOption
     * @return void
     */
    public function merge($strKey, array $arrValue, array $arrOption = [])
    {
        $this->set($strKey, array_unique(array_merge($this->get($strKey, [], $arrOption), $arrValue)), $arrOption);
    }
    
    /**
     * 弹出元素
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @param array $arrOption
     * @return void
     */
    public function pop($strKey, array $arrValue, array $arrOption = [])
    {
        $this->set($strKey, array_diff($this->get($strKey, [], $arrOption), $arrValue), $arrOption);
    }
    
    /**
     * 数组插入键值对数据
     *
     * @param string $strKey
     * @param mixed $mixKey
     * @param mixed $mixValue
     * @param array $arrOption
     * @return void
     */
    public function arrays($strKey, $mixKey, $mixValue = null, array $arrOption = [])
    {
        $arr = $this->get($strKey, [], $arrOption);
        if (is_string($mixKey)) {
            $arr[$mixKey] = $mixValue;
        } elseif (is_array($mixKey)) {
            $arr = array_merge($arr, $mixKey);
        }
        $this->set($strKey, $arr, $arrOption);
    }
    
    /**
     * 数组键值删除数据
     *
     * @param string $strKey
     * @param mixed $mixKey
     * @return void
     */
    public function arraysDelete($strKey, $mixKey, array $arrOption = [])
    {
        $arr = $this->get($strKey, [], $arrOption);
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
        $this->set($strKey, $arr, $arrOption);
    }
    
    /**
     * 获取 cookie
     *
     * @param string $sName
     * @param mixed $mixDefault
     * @param array $arrOption
     * @return mixed
     */
    public function get($sName, $mixDefault = null, array $arrOption = [])
    {
        $arrOption = $this->getOptions($arrOption);
        $sName = $arrOption['prefix'] . $sName;
        
        if (isset($_COOKIE[$sName])) {
            if ($this->isJson($_COOKIE[$sName])) {
                return json_decode($_COOKIE[$sName], true);
            }
            return $_COOKIE[$sName];
        } else {
            return $mixDefault;
        }
    }
    
    /**
     * 删除 cookie
     *
     * @param string $sName
     * @param array $arrOption
     * @return void
     */
    public function delete($sName, array $arrOption = [])
    {
        $this->set($sName, null, $arrOption);
    }
    
    /**
     * 清空 cookie
     *
     * @param boolean $bOnlyPrefix
     * @param array $arrOption
     * @return void
     */
    public function clear($bOnlyPrefix = true, array $arrOption = [])
    {
        $arrOption = $this->getOptions($arrOption);
        $strPrefix = $arrOption['prefix'];
        foreach ($_COOKIE as $sKey => $mixVal) {
            if ($bOnlyPrefix === true && $strPrefix) {
                if (strpos($sKey, $strPrefix) === 0) {
                    $this->delete($sKey, $arrOption);
                }
            } else {
                $arrOption['prefix'] = '';
                $this->delete($sKey, $arrOption);
            }
        }
    }
    
    /**
     * 验证是否为正常的 JSON 字符串
     *
     * @param mixed $mixData
     * @return boolean
     */
    protected function isJson($mixData)
    {
        if (! is_scalar($mixData) && ! method_exists($mixData, '__toString')) {
            return false;
        }
        
        json_decode($mixData);
        
        return json_last_error() === JSON_ERROR_NONE;
    }
}
