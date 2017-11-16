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
namespace queryyetsimple\validate;

use queryyetsimple\support\icontainer;

/**
 * ivalidate 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.26
 * @version 1.0
 */
interface ivalidate
{
    
    /**
     * 默认验证条件
     *
     * @var string
     */
    const DEFAULT_CONDITION = 'exists';
    
    /**
     * 存在字段即验证
     *
     * @var string
     */
    const CONDITION_EXISTS = 'exists';
    
    /**
     * 无论是否存在字段都验证
     *
     * @var string
     */
    const CONDITION_MUST = 'must';
    
    /**
     * 字段值不为空就验证
     *
     * @var string
     */
    const CONDITION_VALUE = 'value';
    
    /**
     * 失败后跳过
     *
     * @var string
     */
    const SKIP_SELF = 'self';
    
    /**
     * 跳过其它验证
     *
     * @var string
     */
    const SKIP_OTHER = 'other';
    
    /**
     * 创建一个验证器
     *
     * @param array $arrData
     * @param array $arrRule
     * @param array $arrMessage
     * @return \queryyetsimple\validate
     */
    public static function make(array $arrData = [], array $arrRule = [], array $arrMessage = []);
    
    /**
     * 初始化验证器
     *
     * @param array $arrData
     * @param array $arrRule
     * @param array $arrMessage
     * @return $this
     */
    public function init(array $arrData = [], array $arrRule = [], array $arrMessage = []);
    
    /**
     * 验证是否成功
     *
     * @return bool
     */
    public function success();
    
    /**
     * 验证是否失败
     *
     * @return bool
     */
    public function fail();
    
    /**
     * 返回所有错误消息
     *
     * @return array
     */
    public function error();
    
    /**
     * 返回验证数据
     *
     * @return array
     */
    public function getData();
    
    /**
     * 设置验证数据
     *
     * @param array $arrData
     * @return $this
     */
    public function data(array $arrData);
    
    /**
     * 添加验证数据
     *
     * @param array $arrData
     * @return $this
     */
    public function addData(array $arrData);
    
    /**
     * 设置单个字段验证数据
     *
     * @param string $strField
     * @param mixed $mixData
     * @return $this
     */
    public function fieldData($strField, $mixData);
    
    /**
     * 返回验证规则
     *
     * @return array
     */
    public function getRule();
    
    /**
     * 设置验证规则
     *
     * @param array $arrRule
     * @return $this
     */
    public function rule(array $arrRule);
    
    /**
     * 设置验证规则,带上条件
     *
     * @param array $arrRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function ruleIf(array $arrRule, $mixCallback);
    
    /**
     * 添加验证规则
     *
     * @param array $arrRule
     * @return $this
     */
    public function addRule(array $arrRule);
    
    /**
     * 添加验证规则,带上条件
     *
     * @param array $arrRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function addRuleIf(array $arrRule, $mixCallback);
    
    /**
     * 设置单个字段验证规则
     *
     * @param string $strField
     * @param mixed $mixRule
     * @return $this
     */
    public function fieldRule($strField, $mixRule);
    
    /**
     * 设置单个字段验证规则,带上条件
     *
     * @param string $strField
     * @param mixed $mixRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function fieldRuleIf($strField, $mixRule, $mixCallback);
    
    /**
     * 添加单个字段验证规则
     *
     * @param string $strField
     * @param mixed $mixRule
     * @return $this
     */
    public function addFieldRule($strField, $mixRule);
    
    /**
     * 添加单个字段验证规则,带上条件
     *
     * @param string $strField
     * @param mixed $mixRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function addFieldRuleIf($strField, $mixRule, $mixCallback);
    
    /**
     * 获取单个字段验证规则
     *
     * @param string $strField
     * @return array
     */
    public function getFieldRule($strField);
    
    /**
     * 获取单个字段验证规则，排除掉绕过的规则
     *
     * @param string $strField
     * @return array
     */
    public function getFieldRuleWithoutSkip($strField);
    
    /**
     * 返回验证消息
     *
     * @return array
     */
    public function getMessage();
    
    /**
     * 设置验证消息
     *
     * @param array $arrMessage
     * @return $this
     */
    public function message(array $arrMessage);
    
    /**
     * 添加验证消息
     *
     * @param array $arrMessage
     * @return $this
     */
    public function addMessage(array $arrMessage);
    
    /**
     * 设置单个字段验证消息
     *
     * @param string $strFieldRule
     * @param string $strMessage
     * @return $this
     */
    public function fieldRuleMessage($strFieldRule, $strMessage);
    
    /**
     * 设置别名
     *
     * @param strKey $strAlias
     * @param strKey $strFor
     * @return $this
     */
    public function alias($strAlias, $strFor);
    
    /**
     * 批量设置别名
     *
     * @param array $arrAlias
     * @return $this
     */
    public function aliasMany(array $arrAlias);
    
    /**
     * 返回别名
     *
     * @return array
     */
    public function getAlias();
    
    /**
     * 设置验证后事件
     *
     * @param callable|string $mixCallback
     * @return $this
     */
    public function after($mixCallback);
    
    /**
     * 返回所有验证后事件
     *
     * @return array
     */
    public function getAfter();
    
    /**
     * 返回所有自定义扩展
     *
     * @return array
     */
    public function getExtend();
    
    /**
     * 注册自定义扩展
     *
     * @param string $rule
     * @param callable|string $mixExtend
     * @return $this
     */
    public function extend($strRule, $mixExtend);
    
    /**
     * 批量注册自定义扩展
     *
     * @param array $arrExtend
     * @return $this
     */
    public function extendMany(array $arrExtend);
    
    /**
     * 设置 ioc 容器
     *
     * @param \queryyetsimple\support\icontainer $objContainer
     * @return $this
     */
    public function container(icontainer $objContainer);
    
    /**
     * 获取需要跳过的验证规则
     *
     * @return array
     */
    public function getSkipRule();
}
