<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\support;

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

use Closure;
use ReflectionClass;
use InvalidArgumentException;

/**
 * 辅助函数
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.05
 * @version 1.0
 */
class helper {
    
    /**
     * 数组数据格式化
     *
     * @param mixed $mixInput            
     * @param string $sDelimiter            
     * @param boolean $bAllowedEmpty            
     * @return mixed
     */
    public static function arrays($mixInput, $sDelimiter = ',', $bAllowedEmpty = false) {
        if (is_array ( $mixInput ) || is_string ( $mixInput )) {
            if (! is_array ( $mixInput )) {
                $mixInput = explode ( $sDelimiter, $mixInput );
            }
            
            $mixInput = array_filter ( $mixInput ); // 过滤null
            if ($bAllowedEmpty === true) {
                return $mixInput;
            } else {
                $mixInput = array_map ( 'trim', $mixInput );
                return array_filter ( $mixInput, 'strlen' );
            }
        } else {
            return $mixInput;
        }
    }
    
    /**
     * 数组合并支持 + 算法
     *
     * @param array $arrOption            
     * @param boolean $booRecursion            
     * @return array
     */
    public static function arrayMergePlus($arrOption, $booRecursion = true) {
        $arrExtend = [ ];
        foreach ( $arrOption as $strKey => $mixTemp ) {
            if (strpos ( $strKey, '+' ) === 0) {
                $arrExtend [ltrim ( $strKey, '+' )] = $mixTemp;
                unset ( $arrOption [$strKey] );
            }
        }
        foreach ( $arrExtend as $strKey => $mixTemp ) {
            if (isset ( $arrOption [$strKey] ) && is_array ( $arrOption [$strKey] ) && is_array ( $mixTemp )) {
                $arrOption [$strKey] = array_merge ( $arrOption [$strKey], $mixTemp );
            } else {
                $arrOption [$strKey] = $mixTemp;
            }
        }
        
        if ($booRecursion === true) {
            foreach ( $arrOption as $strKey => $mixTemp ) {
                if (is_array ( $mixTemp )) {
                    $arrOption [$strKey] = static::arrayMergePlus ( $mixTemp );
                }
            }
        }
        
        return $arrOption;
    }
    
    /**
     * 判断字符串是否为数字
     *
     * @param string $strSearch            
     * @since bool
     */
    public static function stringNumber($mixValue) {
        if (is_numeric ( $mixValue ))
            return true;
        return ! preg_match ( "/[^\d-.,]/", trim ( $mixValue, '\'' ) );
    }
    
    /**
     * 判断字符串是否为整数
     *
     * @param string $strSearch            
     * @since bool
     */
    public static function isInteger($mixValue) {
        if (is_int ( $mixValue ))
            return true;
        return ctype_digit ( strval ( $mixValue ) );
    }
    
    /**
     * 验证 PHP 各种变量类型
     *
     * @param 待验证的变量 $mixVar            
     * @param string $sType
     *            变量类型
     * @return boolean
     */
    public static function varType($mixVar, $sType) {
        $sType = trim ( $sType ); // 整理参数，以支持 array:ini 格式
        $sType = explode ( ':', $sType );
        $sType [0] = strtolower ( $sType [0] );
        
        switch ($sType [0]) {
            case 'string' : // 字符串
                return is_string ( $mixVar );
            case 'integer' : // 整数
            case 'int' :
                return is_int ( $mixVar );
            case 'float' : // 浮点
                return is_float ( $mixVar );
            case 'boolean' : // 布尔
            case 'bool' :
                return is_bool ( $mixVar );
            case 'num' : // 数字
            case 'numeric' :
                return is_numeric ( $mixVar );
            case 'base' : // 标量（所有基础类型）
            case 'scalar' :
                return is_scalar ( $mixVar );
            case 'handle' : // 外部资源
            case 'resource' :
                return is_resource ( $mixVar );
            case 'closure' : // 闭包
                return $mixVar instanceof Closure;
            case 'array' :
                { // 数组
                    if (! empty ( $sType [1] )) {
                        $sType [1] = explode ( ',', $sType [1] );
                        return static::varArray ( $mixVar, $sType [1] );
                    } else {
                        return is_array ( $mixVar );
                    }
                }
            case 'object' : // 对象
                return is_object ( $mixVar );
            case 'null' : // 空
                return ($mixVar === null);
            case 'callback' : // 回调函数
                return is_callable ( $mixVar, false );
            default : // 类或者接口检验
                $sType = implode ( ':', $sType );
                return $mixVar instanceof $sType;
        }
    }
    
    /**
     * 验证参数是否为指定的类型集合
     *
     * @param mixed $mixVar            
     * @param mixed $mixTypes            
     * @return boolean
     */
    public static function varThese($mixVar, $mixTypes) {
        if (! static::varType ( $mixTypes, 'string' ) && ! static::varArray ( $mixTypes, [ 
                'string' 
        ] )) {
            throw new InvalidArgumentException ( __ ( '参数必须为 string 或 各项元素为 string 的数组' ) );
        }
        
        if (is_string ( $mixTypes )) {
            $mixTypes = ( array ) $mixTypes;
        }
        
        foreach ( $mixTypes as $sType ) { // 类型检查
            if (static::varType ( $mixVar, $sType )) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 验证数组中的每一项格式化是否正确
     *
     * @param array $arrArray            
     * @param array $arrTypes            
     * @return boolean
     */
    public static function varArray($arrArray, array $arrTypes) {
        if (! is_array ( $arrArray )) { // 不是数组直接返回
            return false;
        }
        
        // 判断数组内部每一个值是否为给定的类型
        foreach ( $arrArray as &$mixValue ) {
            $bRet = false;
            foreach ( $arrTypes as $mixType ) {
                if (static::varType ( $mixValue, $mixType )) {
                    $bRet = true;
                    break;
                }
            }
            
            if (! $bRet) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * 正则属性转义
     *
     * @param string $sTxt            
     * @param bool $bEsc            
     * @return string
     */
    public static function escapeCharacter($sTxt, $bEsc = true) {
        if ($sTxt == '""') {
            $sTxt = '';
        }
        
        if ($bEsc) { // 转义
            $sTxt = str_replace ( [ 
                    '\\\\',
                    "\\'",
                    '\\"',
                    '\\$',
                    '\\.' 
            ], [ 
                    '\\',
                    '~~{#!`!#}~~',
                    '~~{#!``!#}~~',
                    '~~{#!S!#}~~',
                    '~~{#!dot!#}~~' 
            ], $sTxt );
        } else { // 还原
            $sTxt = str_replace ( [ 
                    '.',
                    "~~{#!`!#}~~",
                    '~~{#!``!#}~~',
                    '~~{#!S!#}~~',
                    '~~{#!dot!#}~~' 
            ], [ 
                    '->',
                    "'",
                    '"',
                    '$',
                    '.' 
            ], $sTxt );
        }
        
        return $sTxt;
    }
    
    /**
     * 转移正则表达式特殊字符
     *
     * @param string $sTxt            
     * @return string
     */
    public static function escapeRegexCharacter($sTxt) {
        $sTxt = str_replace ( [ 
                '$',
                '/',
                '?',
                '*',
                '.',
                '!',
                '-',
                '+',
                '(',
                ')',
                '[',
                ']',
                ',',
                '{',
                '}',
                '|' 
        ], [ 
                '\$',
                '\/',
                '\\?',
                '\\*',
                '\\.',
                '\\!',
                '\\-',
                '\\+',
                '\\(',
                '\\)',
                '\\[',
                '\\]',
                '\\,',
                '\\{',
                '\\}',
                '\\|' 
        ], $sTxt );
        return $sTxt;
    }
    
    /**
     * 通配符正则
     *
     * @param string $strFoo            
     * @param bool $booStrict            
     * @return string
     */
    public static function prepareRegexForWildcard($strRegex, $booStrict = true) {
        return '/^' . str_replace ( '6084fef57e91a6ecb13fff498f9275a7', '(\S+)', static::escapeRegexCharacter ( str_replace ( '*', '6084fef57e91a6ecb13fff498f9275a7', $strRegex ) ) ) . ($booStrict ? '$' : '') . '/';
    }
}
