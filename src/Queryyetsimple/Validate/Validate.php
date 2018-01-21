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
namespace Queryyetsimple\Validate;

use DateTime;
use Exception;
use DateTimeZone;
use BadMethodCallException;
use InvalidArgumentException;
use Queryyetsimple\Support\{
    Str,
    Helper,
    IContainer,
    FlowControl
};

/**
 * validate 数据验证器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.02
 * @version 1.0
 */
class Validate implements IValidate
{
    use FlowControl;

    /**
     * IOC 容器
     *
     * @var \Queryyetsimple\Support\IContainer
     */
    protected $objContainer;

    /**
     * 待验证数据
     *
     * @var array
     */
    protected $arrData = [];

    /**
     * 验证规则
     *
     * @var array
     */
    protected $arrRule = [];

    /**
     * 默认验证提示信息
     *
     * @var array
     */
    protected static $arrDefaultMessage = [];

    /**
     * 是否初始化默认验证提示信息
     *
     * @var boolean
     */
    protected static $booDefaultMessage = false;

    /**
     * 验证提示信息
     *
     * @var array
     */
    protected $arrMessage = [];

    /**
     * 字段名字
     *
     * @var array
     */
    protected $arrFieldName = [];

    /**
     * 错误规则
     *
     * @var array
     */
    protected $arrFailedRules;

    /**
     * 错误消息
     *
     * @var array
     */
    protected $arrErrorMessages = [];

    /**
     * 需要跳过的验证规则
     * 用于扩展属性支持
     *
     * @var array
     */
    protected $arrSkipRule = [];

    /**
     * 分析数据键
     * like this hello.world.foobar
     *
     * @var array
     */
    protected $arrParsedDataKey;

    /**
     * 扩展验证器
     *
     * @var array
     */
    protected $arrExtend = [];

    /**
     * 验证后续事件
     *
     * @var array
     */
    protected $arrAfter = [];

    /**
     * 验证别名
     *
     * @var array
     */
    protected $arrAlias = [
        'confirm' => 'equal_to',
        'gt' => 'greater_than',
        '>' => 'greater_than',
        'egt' => 'equal_greater_than',
        '>=' => 'equal_greater_than',
        'lt' => 'less_than',
        '<' => 'less_than',
        'elt' => 'equal_less_than',
        '<=' => 'equal_less_than',
        'eq' => 'equal',
        '=' => 'equal',
        'neq' => 'not_equal',
        '!=' => 'not_equal'
    ];

    /**
     * 构造函数
     *
     * @param array $arrData
     * @param array $arrRule
     * @param array $arrFieldName
     * @param array $arrMessage
     */
    public function __construct(array $arrData = [], array $arrRule = [], array $arrFieldName = [], array $arrMessage = [])
    {
        $this->data($arrData);
        $this->rule($arrRule);
        $this->fieldName($arrFieldName);
        $this->message($arrMessage);
        static::defaultMessage();
    }

    /**
     * 初始化验证器
     *
     * @param array $arrData
     * @param array $arrRule
     * @param array $arrFieldName
     * @param array $arrMessage
     * @return \Queryyetsimple\Validate
     */
    public static function make(array $arrData = [], array $arrRule = [], array $arrFieldName = [], array $arrMessage = [])
    {
        return new static($arrData, $arrRule, $arrFieldName, $arrMessage);
    }

    /**
     * 验证是否成功
     *
     * @return bool
     */
    public function success()
    {
        $arrSkipRule = $this->getSkipRule();

        foreach ($this->arrRule as $strField => $arrRule) {
            foreach ($arrRule as $strRule) {
                if (in_array($strRule, $arrSkipRule)) {
                    continue;
                }

                if ($this->doValidateItem($strField, $strRule) === false) {
                    // 验证失败跳过剩余验证规则
                    if ($this->shouldSkipOther($strField)) {
                        break 2;
                    }

                    // 验证失败跳过自身剩余验证规则
                    elseif ($this->shouldSkipSelf($strField)) {
                        break;
                    }
                }
            }
        }
        unset($arrSkipRule);

        foreach ($this->arrAfter as $calAfter) {
            call_user_func($calAfter);
        }

        return count($this->arrErrorMessages) === 0;
    }

    /**
     * 验证是否失败
     *
     * @return bool
     */
    public function fail()
    {
        return ! $this->success();
    }

    /**
     * 返回所有错误消息
     *
     * @return array
     */
    public function error()
    {
        return $this->arrErrorMessages;
    }

    /**
     * 返回验证数据
     *
     * @return array
     */
    public function getData()
    {
        return $this->arrData;
    }

    /**
     * 设置验证数据
     *
     * @param array $arrData
     * @return $this
     */
    public function data(array $arrData)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrData = $arrData;
        return $this;
    }

    /**
     * 添加验证数据
     *
     * @param array $arrData
     * @return $this
     */
    public function addData(array $arrData)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrData = array_merge($this->arrData, $arrData);
        return $this;
    }

    /**
     * 设置单个字段验证数据
     *
     * @param string $strField
     * @param mixed $mixData
     * @return $this
     */
    public function fieldData($strField, $mixData)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrData[$strField] = $mixData;
        return $this;
    }

    /**
     * 返回验证规则
     *
     * @return array
     */
    public function getRule()
    {
        return $this->arrRule;
    }

    /**
     * 设置验证规则
     *
     * @param array $arrRule
     * @return $this
     */
    public function rule(array $arrRule)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrRule = $this->arrayRule($arrRule);
        return $this;
    }

    /**
     * 设置验证规则,带上条件
     *
     * @param array $arrRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function ruleIf(array $arrRule, $mixCallback)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if ($this->isCallbackValid($mixCallback)) {
            return $this->rule($arrRule);
        }
        return $this;
    }

    /**
     * 添加验证规则
     *
     * @param array $arrRule
     * @return $this
     */
    public function addRule(array $arrRule)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrRule = array_merge($this->arrRule, $this->arrayRule($arrRule));
        return $this;
    }

    /**
     * 添加验证规则,带上条件
     *
     * @param array $arrRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function addRuleIf(array $arrRule, $mixCallback)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if ($this->isCallbackValid($mixCallback)) {
            return $this->addRule($arrRule);
        }
        return $this;
    }

    /**
     * 设置单个字段验证规则
     *
     * @param string $strField
     * @param mixed $mixRule
     * @return $this
     */
    public function fieldRule($strField, $mixRule)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if (! isset($this->arrRule[$strField])) {
            $this->arrRule[$strField] = [];
        }

        $this->arrRule[$strField] = $this->arrayRuleItem($mixRule);
        return $this;
    }

    /**
     * 设置单个字段验证规则,带上条件
     *
     * @param string $strField
     * @param mixed $mixRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function fieldRuleIf($strField, $mixRule, $mixCallback)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if ($this->isCallbackValid($mixCallback)) {
            return $this->fieldRule($strField, $mixRule);
        }
        return $this;
    }

    /**
     * 添加单个字段验证规则
     *
     * @param string $strField
     * @param mixed $mixRule
     * @return $this
     */
    public function addFieldRule($strField, $mixRule)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if (! isset($this->arrRule[$strField])) {
            $this->arrRule[$strField] = [];
        }

        $this->arrRule[$strField] = array_merge($this->arrRule[$strField], $this->arrayRuleItem($mixRule));
        return $this;
    }

    /**
     * 添加单个字段验证规则,带上条件
     *
     * @param string $strField
     * @param mixed $mixRule
     * @param callable|mixed $calCallback
     * @return $this
     */
    public function addFieldRuleIf($strField, $mixRule, $mixCallback)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if ($this->isCallbackValid($mixCallback)) {
            return $this->addFieldRule($strField, $mixRule);
        }
        return $this;
    }

    /**
     * 获取单个字段验证规则
     *
     * @param string $strField
     * @return array
     */
    public function getFieldRule($strField)
    {
        if (isset($this->arrRule[$strField])) {
            return $this->arrRule[$strField];
        }

        return [];
    }

    /**
     * 获取单个字段验证规则，排除掉绕过的规则
     *
     * @param string $strField
     * @return array
     */
    public function getFieldRuleWithoutSkip($strField)
    {
        return array_diff($this->getFieldRule($strField), $this->getSkipRule());
    }

    /**
     * 返回验证消息
     *
     * @return array
     */
    public function getMessage()
    {
        return $this->arrMessage;
    }

    /**
     * 设置验证消息
     *
     * @param array $arrMessage
     * @return $this
     */
    public function message(array $arrMessage)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrMessage = $arrMessage;
        return $this;
    }

    /**
     * 添加验证消息
     *
     * @param array $arrMessage
     * @return $this
     */
    public function addMessage(array $arrMessage)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrMessage = array_merge($this->arrMessage, $this->arrayMessage($arrMessage));
        return $this;
    }

    /**
     * 返回字段名字
     *
     * @return array
     */
    public function getFieldName()
    {
        return $this->arrFieldName;
    }

    /**
     * 设置字段名字
     *
     * @param array $arrFieldName
     * @return $this
     */
    public function fieldName(array $arrFieldName)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrFieldName = $arrFieldName;
        return $this;
    }

    /**
     * 添加字段名字
     *
     * @param array $arrFieldName
     * @return $this
     */
    public function addFieldName(array $arrFieldName)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrFieldName = array_merge($this->arrFieldName, $this->arrayMessage($arrFieldName));
        return $this;
    }

    /**
     * 设置单个字段验证消息
     *
     * @param string $strFieldRule
     * @param string $strMessage
     * @return $this
     */
    public function fieldRuleMessage($strFieldRule, $strMessage)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrMessage[$strFieldRule] = $strMessage;
        return $this;
    }

    /**
     * 设置别名
     *
     * @param strKey $strAlias
     * @param strKey $strFor
     * @return $this
     */
    public function alias($strAlias, $strFor)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if (in_array($strAlias, $this->getSkipRule())) {
            throw new Exception(spintf('You cannot set alias for skip rule %s', $strAlias));
        }

        $this->arrAlias[$strAlias] = $strFor;
        return $this;
    }

    /**
     * 批量设置别名
     *
     * @param array $arrAlias
     * @return $this
     */
    public function aliasMany(array $arrAlias)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        foreach ($arrAlias as $strAlias => $strFor) {
            $this->alias($strAlias, $strFor);
        }
        return $this;
    }

    /**
     * 返回别名
     *
     * @return array
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * 设置验证后事件
     *
     * @param callable|string $mixCallback
     * @return $this
     */
    public function after($mixCallback)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrAfter[] = function () use ($mixCallback) {
            return call_user_func_array($mixCallback, [
                $this
            ]);
        };

        return $this;
    }

    /**
     * 返回所有验证后事件
     *
     * @return array
     */
    public function getAfter()
    {
        return $this->arrAfter;
    }

    /**
     * 返回所有自定义扩展
     *
     * @return array
     */
    public function getExtend()
    {
        return $this->arrExtend;
    }

    /**
     * 注册自定义扩展
     *
     * @param string $rule
     * @param callable|string $mixExtend
     * @return $this
     */
    public function extend($strRule, $mixExtend)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrExtend[strtolower($strRule)] = $mixExtend;
        return $this;
    }

    /**
     * 批量注册自定义扩展
     *
     * @param array $arrExtend
     * @return $this
     */
    public function extendMany(array $arrExtend)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrExtend = array_merge($this->arrExtend, $arrExtend);
        return $this;
    }

    /**
     * 设置 IOC 容器
     *
     * @param \Queryyetsimple\Support\IContainer $objContainer
     * @return $this
     */
    public function container(IContainer $objContainer)
    {
        $this->objContainer = $objContainer;
        return $this;
    }

    /**
     * 获取需要跳过的验证规则
     *
     * @return array
     */
    public function getSkipRule()
    {
        return array_merge([
            static::CONDITION_EXISTS,
            static::CONDITION_MUST,
            static::CONDITION_VALUE,
            static::SKIP_SELF,
            static::SKIP_OTHER
        ], $this->arrSkipRule);
    }

    /**
     * 设置默认的消息
     *
     * @return void
     */
    public static function defaultMessage()
    {
        if (static::$booDefaultMessage) {
            return;
        }

        static::$arrDefaultMessage = [
            'required' => __('{field} 不能为空'),
            'number' => __('{field} 必须是数字'),
            'float' => __('{field} 必须是浮点数'),
            'double' => __('{field} 必须是双精度浮点数'),
            'boolean' => __('{field} 必须是布尔值'),
            'array' => __('{field} 必须是数组'),
            'accepted' => __('{field} 必须是 yes、on、true 或者 1'),
            'date' => __('{field} 不是正确的日期格式'),
            'date_format' => __('{field} 必须使用日期格式 {rule}'),
            'timezone' => __('{field} 不是正确的时区'),
            'alpha' => __('{field} 只能是字母'),
            'alpha_upper' => __('{field} 只能是大写字母'),
            'alpha_lower' => __('{field} 只能是小写字母'),
            'alpha_num' => __('{field} 只能是字母和数字'),
            'alpha_dash' => __('{field} 只能是字母、数字、短横线和下划线'),
            'chinese' => __('{field} 只能是汉字'),
            'chinese_alpha_num' => __('{field} 只能是汉字、字母、数字'),
            'chinese_alpha_dash' => __('{field} 只能是汉字、字母、数字、短横线和下划线'),
            'url' => __('{field} 不是有效的 URL 地址'),
            'active_url' => __('{field} 不是有效的域名或者 IP'),
            'ip' => __('{field} 不是有效的 IP 地址'),
            'ipv4' => __('{field} 不是有效的 IPV4 地址'),
            'ipv6' => __('{field} 不是有效的 IPV6 地址'),
            'in' => __('{field} 必须在 {rule} 范围内'),
            'not_in' => __('{field} 不能在 {rule} 范围内'),
            'between' => __('{field} 只能在 {rule} 和 {rule1} 之间，不包含等于'),
            'not_between' => __('{field} 不在 {rule} 和 {rule1} 之间，不包含等于'),
            'between_equal' => __('{field} 只能在 {rule} 和 {rule1} 之间，包含等于'),
            'not_between_equal' => __('{field} 不在 {rule} 和 {rule1} 之间，包含等于'),
            'greater_than' => __('{field} 必须大于 {rule}'),
            'equal_greater_than' => __('{field} 必须大于等于 {rule}'),
            'less_than' => __('{field} 必须小于 {rule}'),
            'equal_less_than' => __('{field} 必须小于等于 {rule}'),
            'equal' => __('{field} 必须等于 {rule}'),
            'not_equal' => __('{field} 不能等于 {rule}'),
            'equal_to' => __('{field} 必须等于字段 {rule}'),
            'different' => __('{field} 不能等于字段 {rule}'),
            'same' => __('{field} 必须完全等于 {rule}'),
            'not_same' => __('{field} 不能完全等于 {rule}'),
            'empty' => __('{field} 必须为空'),
            'not_empty' => __('{field} 不能为空'),
            'null' => __('{field} 必须 null'),
            'not_null' => __('{field} 不能为 null'),
            'strlen' => __('{field} 长度不符合要求 {rule}'),
            'max' => __('{field} 长度不能超过 {rule}'),
            'min' => __('{field} 长度不能小于 {rule}'),
            'digit' => __('{field} 字符串中的字符必须都是数字'),
            'type' => __('{field} 类型不符合要求 {rule}'),
            'lower' => __('{field} 必须全部是小写'),
            'upper' => __('{field} 必须全部是大写'),
            'min_length' => __('{field} 不满足最小长度 {rule}'),
            'max_length' => __('{field} 不满足最大长度 {rule}'),
            'id_card' => __('{field} 必须是有效的中国大陆身份证'),
            'zip_code' => __('{field} 必须是有效的中国邮政编码'),
            'qq' => __('{field} 必须是有效的 QQ 号码'),
            'phone' => __('{field} 必须是有效的电话号码或者手机号'),
            'mobile' => __('{field} 必须是有效的手机号'),
            'telephone' => __('{field} 必须是有效的电话号码'),
            'email' => __('{field} 必须为正确的电子邮件格式'),
            'luhn' => __('{field} 必须为正确的符合 luhn 格式算法银行卡'),
            'after' => __('{field} 日期不能小于 {rule}'),
            'before' => __('{field} 日期不能超过 {rule}'),
            'allow_ip' => __('{field} 不允许的 IP 访问 {rule}'),
            'deny_ip' => __('{field} 禁止的 IP 访问 {rule}'),
            'method' => __('无效的请求类型 {rule}'),
            'json' => __('{field} 不是有效的 JSON')
        ];

        static::$booDefaultMessage = true;
    }

    /**
     * 不能为空
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateRequired($strField, $mixData, $arrParameter)
    {
        if (is_null($mixData)) {
            return false;
        } elseif (is_string($mixData) && trim($mixData) === '') {
            return false;
        }

        return true;
    }

    /**
     * 是否为日期
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateDate($strField, $mixData, $arrParameter)
    {
        if ($mixData instanceof DateTime) {
            return true;
        }

        if (strtotime($mixData) === false) {
            return false;
        }

        $mixData = date_parse($mixData);
        return checkdate($mixData['month'], $mixData['day'], $mixData['year']);
    }

    /**
     * 是否为时间
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateDateFormat($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        $arrParse = date_parse_from_format($arrParameter[0], $mixData);
        return $arrParse['error_count'] === 0 && $arrParse['warning_count'] === 0;
    }

    /**
     * 是否为正确的时区
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateTimezone($strField, $mixData, $arrParameter)
    {
        try {
            new DateTimeZone($mixData);
        } catch (Exception $oE) {
            return false;
        }

        return true;
    }

    /**
     * 验证在给定日期之后
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAfter($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);

        if ($strFormat = $this->getDateFormat($strField)) {
            return $this->doAfterWithFormat($strFormat, $mixData, $arrParameter);
        }

        if (! ($intTime = strtotime($arrParameter[0]))) {
            return strtotime($mixData) > strtotime($this->getFieldValue($arrParameter[0]));
        }

        return strtotime($mixData) > $intTime;
    }

    /**
     * 验证在给定日期之前
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateBefore($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);

        if ($strFormat = $this->getDateFormat($strField)) {
            return $this->doBeforeWithFormat($strFormat, $mixData, $arrParameter);
        }

        if (! ($intTime = strtotime($arrParameter[0]))) {
            return strtotime($mixData) < strtotime($this->getFieldValue($arrParameter[0]));
        }

        return strtotime($mixData) < $intTime;
    }

    /**
     * 检测字符串中的字符是否都是数字，负数和小数会检测不通过
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateDigit($strField, $mixData, $arrParameter)
    {
        return ctype_digit($mixData);
    }

    /**
     * 是否双精度浮点数
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateDouble($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[-\+]?\d+(\.\d+)?$/', $mixData);
    }

    /**
     * 是否可接受的
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAccepted($strField, $mixData, $arrParameter)
    {
        return $this->validateRequired($strField, $mixData, $arrParameter) && in_array($mixData, [
            'yes',
            'on',
            '1',
            1,
            true,
            'true'
        ], true);
    }

    /**
     * 是否整型数字
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateInteger($strField, $mixData, $arrParameter)
    {
        return filter_var($mixData, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * 验证是否为浮点数
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateFloat($strField, $mixData, $arrParameter)
    {
        return filter_var($mixData, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * 验证是否为数组
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateArray($strField, $mixData, $arrParameter)
    {
        return is_array($mixData);
    }

    /**
     * 验证是否为布尔值
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateBoolean($strField, $mixData, $arrParameter)
    {
        return in_array($mixData, [
            true,
            false,
            0,
            1,
            '0',
            '1'
        ], true);
    }

    /**
     * 是否为数字
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNumber($strField, $mixData, $arrParameter)
    {
        return is_numeric($mixData);
    }

    /**
     * 处于 between 范围，不包含等于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateBetween($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 2);
        return $mixData > $arrParameter[0] && $mixData < $arrParameter[1];
    }

    /**
     * 未处于 between 范围，不包含等于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNotBetween($strField, $mixData, $arrParameter)
    {
        return ! $this->validateBetweenEqual($strField, $mixData, $arrParameter);
    }

    /**
     * 处于 betweenEqual 范围，包含等于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateBetweenEqual($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 2);
        return $mixData >= $arrParameter[0] && $mixData <= $arrParameter[1];
    }

    /**
     * 未处于 betweenEqual 范围，包含等于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNotBetweenEqual($strField, $mixData, $arrParameter)
    {
        return ! $this->validateBetween($strField, $mixData, $arrParameter);
    }

    /**
     * 是否处于某个范围
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateIn($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return in_array($mixData, $arrParameter);
    }

    /**
     * 是否不处于某个范围
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNotIn($strField, $mixData, $arrParameter)
    {
        return ! $this->validateIn($strField, $mixData, $arrParameter);
    }

    /**
     * 是否为合法的 IP 地址
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateIp($strField, $mixData, $arrParameter)
    {
        return filter_var($mixData, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * 是否为 ipv4
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateIpv4($strField, $mixData, $arrParameter)
    {
        return filter_var($mixData, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    /**
     * 是否为 ipv6
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateIpv6($strField, $mixData, $arrParameter)
    {
        return filter_var($mixData, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    /**
     * 大于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateGreaterThan($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData > $arrParameter[0];
    }

    /**
     * 大于或者等于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateEqualGreaterThan($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData >= $arrParameter[0];
    }

    /**
     * 小于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateLessThan($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData < $arrParameter[0];
    }

    /**
     * 小于或者等于
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateEqualLessThan($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData <= $arrParameter[0];
    }

    /**
     * 两个值是否相同
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateEqual($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData == $arrParameter[0];
    }

    /**
     * 两个值是否不相同
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNotEqual($strField, $mixData, $arrParameter)
    {
        return ! $this->validateEqual($strField, $mixData, $arrParameter);
    }

    /**
     * 两个字段是否相同
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateEqualTo($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData == $this->getFieldValue($arrParameter[0]);
    }

    /**
     * 两个字段是否不同
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateDifferent($strField, $mixData, $arrParameter)
    {
        return $this->validateEqualTo($strField, $mixData, $arrParameter);
    }

    /**
     * 两个值是否完全相同
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateSame($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData === $arrParameter[0];
    }

    /**
     * 两个值是否不完全相同
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNotSame($strField, $mixData, $arrParameter)
    {
        return ! $this->validateSame($strField, $mixData, $arrParameter);
    }

    /**
     * 验证值上限
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateMax($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData <= $arrParameter[0];
    }

    /**
     * 验证值下限
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateMin($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return $mixData >= $arrParameter[0];
    }

    /**
     * 值是否为空
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateEmpty($strField, $mixData, $arrParameter)
    {
        return empty($mixData);
    }

    /**
     * 值是否不为空
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNotEmpty($strField, $mixData, $arrParameter)
    {
        return ! $this->validateEmpty($strField, $mixData, $arrParameter);
    }

    /**
     * 是否为 null
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNull($strField, $mixData, $arrParameter)
    {
        return is_null($mixData);
    }

    /**
     * 是否不为 null
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateNotNull($strField, $mixData, $arrParameter)
    {
        return ! $this->validateNull($strField, $mixData, $arrParameter);
    }

    /**
     * 是否为英文字母
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAlpha($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[A-Za-z]+$/', $mixData);
    }

    /**
     * 是否为大写英文字母
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAlphaUpper($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[A-Z]+$/', $mixData);
    }

    /**
     * 是否为小写英文字母
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAlphaLower($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[a-z]+$/', $mixData);
    }

    /**
     * 字符串是否为数字和字母
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAlphaNum($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[A-Za-z0-9]+$/', $mixData);
    }

    /**
     * 字符串是否为数字、下划线、短横线和字母
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAlphaDash($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[A-Za-z0-9\-\_]+$/', $mixData);
    }

    /**
     * 是否为中文
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateChinese($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $mixData);
    }

    /**
     * 是否为中文、数字和字母
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateChineseAlphaNum($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $mixData);
    }

    /**
     * 是否为中文、数字、下划线、短横线和字母
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateChineseAlphaDash($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $mixData);
    }

    /**
     * 是否为大陆身份证
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateIdCard($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/', $mixData);
    }

    /**
     * 是否为中国邮政编码
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateZipCode($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[1-9]\d{5}$/', $mixData);
    }

    /**
     * 是否为 QQ 号码
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateQq($strField, $mixData, $arrParameter)
    {
        return preg_match('/^[1-9]\d{4,11}$/', $mixData);
    }

    /**
     * 值是否为电话号码或者手机号码
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validatePhone($strField, $mixData, $arrParameter)
    {
        return ((strlen($mixData) == 11 && preg_match('/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/', $mixData)) || preg_match('/^\d{3,4}-?\d{7,9}$/', $mixData));
    }

    /**
     * 值是否为手机号码
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateMobile($strField, $mixData, $arrParameter)
    {
        return preg_match('/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/', $mixData);
    }

    /**
     * 值是否为电话号码
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateTelephone($strField, $mixData, $arrParameter)
    {
        return preg_match('/^\d{3,4}-?\d{7,9}$/', $mixData);
    }

    /**
     * 值是否为银行卡等符合 luhn 算法
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateLuhn($strField, $mixData, $arrParameter)
    {
        $intTotal = 0;
        for ($intI = strlen($mixData); $intI >= 1; $intI --) {
            $intIndex = $intI - 1;
            if ($intI % 2 == 0) {
                $intTotal += $mixData{$intIndex};
            } else {
                $intFoo = $mixData{$intIndex} * 2;
                if ($intFoo > 9) {
                    $intFoo = ( int ) ($intFoo / 10) + $intFoo % 10;
                }
                $intTotal += $intFoo;
            }
        }
        return ($intTotal % 10) == 0;
    }

    /**
     * 验证是否为有效的 url 或者 IP 地址
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateActiveUrl($strField, $mixData, $arrParameter)
    {
        return checkdnsrr($mixData);
    }

    /**
     * 验证是否为 url 地址
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateUrl($strField, $mixData, $arrParameter)
    {
        return filter_var($mixData, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * 是否为电子邮件
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateEmail($strField, $mixData, $arrParameter)
    {
        return filter_var($mixData, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 长度验证
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateStrlen($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return strlen($mixData) == ( int ) $arrParameter[0];
    }

    /**
     * 数据类型验证
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateType($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return gettype($mixData) === $arrParameter[0];
    }

    /**
     * 验证是否都是小写
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateLower($strField, $mixData, $arrParameter)
    {
        return ctype_lower($mixData);
    }

    /**
     * 验证是否都是大写
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateUpper($strField, $mixData, $arrParameter)
    {
        return ctype_upper($mixData);
    }

    /**
     * 验证数据最小长度
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateMinLength($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return iconv_strlen($mixData, 'utf-8') >= ( int ) $arrParameter[0];
    }

    /**
     * 验证数据最大长度
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateMaxLength($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return iconv_strlen($mixData, 'utf-8') <= ( int ) $arrParameter[0];
    }

    /**
     * 验证 IP 许可
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateAllowIp($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return in_array($mixData ?  : $_SERVER['REMOTE_ADDR'], $arrParameter);
    }

    /**
     * 验证 IP 禁用
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateDenyIp($strField, $mixData, $arrParameter)
    {
        return ! $this->validateAllowIp($strField, $mixData, $arrParameter);
    }

    /**
     * 验证请求类型
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateMethod($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return strtolower($mixData ?  : (PHP_SAPI == 'cli' ? 'GET' : $_SERVER['REQUEST_METHOD'])) == strtolower($arrParameter[0]);
    }

    /**
     * 验证是否为正常的 JSON 字符串
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateJson($strField, $mixData, $arrParameter)
    {
        if (! is_scalar($mixData) && ! method_exists($mixData, '__toString')) {
            return false;
        }

        json_decode($mixData);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * 数据是否满足正则条件
     *
     * @param string $strField
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function validateRegex($strField, $mixData, $arrParameter)
    {
        $this->checkParameterLength($strField, $arrParameter, 1);
        return preg_match($arrParameter[0], $mixData) > 0;
    }

    /**
     * 验证在给定日期之前
     *
     * @param string $strFormat
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function doBeforeWithFormat($strFormat, $mixData, $arrParameter)
    {
        $arrParameter[0] = $this->getFieldValue($arrParameter[0]) ?  : $arrParameter[0];
        return $this->doCheckBeforeAfter($strFormat, $mixData, $arrParameter[0]);
    }

    /**
     * 验证在给定日期之后
     *
     * @param string $strFormat
     * @param mixed $mixData
     * @param array $arrParameter
     * @return boolean
     */
    protected function doAfterWithFormat($strFormat, $mixData, $arrParameter)
    {
        $arrParameter[0] = $this->getFieldValue($arrParameter[0]) ?  : $arrParameter[0];
        return $this->doCheckBeforeAfter($strFormat, $arrParameter[0], $mixData);
    }

    /**
     * 验证日期顺序
     *
     * @param string $strFormat
     * @param string $strFoo
     * @param string $strBar
     * @return boolean
     */
    protected function doCheckBeforeAfter($strFormat, $strFoo, $strBar)
    {
        $objBefore = $this->makeDateTimeFormat($strFormat, $strFoo);
        $objAfter = $this->makeDateTimeFormat($strFormat, $strBar);
        return $objBefore && $objAfter && $objBefore < $objAfter;
    }

    /**
     * 获取时间格式化
     *
     * @param string $strField
     * @return string|null
     */
    protected function getDateFormat($strField)
    {
        if ($arrResult = $this->getParseRule($strField, 'date_format')) {
            return $arrResult[1][0];
        }
    }

    /**
     * 尝试读取格式化条件
     *
     * @param string $strField
     * @param string|array $mixRule
     * @return array|null
     */
    protected function getParseRule($strField, $mixRule)
    {
        if (! array_key_exists($strField, $this->arrRule)) {
            return;
        }

        $mixRule = ( array ) $mixRule;

        foreach ($this->arrRule[$strField] as $strRule) {
            list($strRule, $arrParameter) = $this->parseRule($strRule);
            if (in_array($strRule, $mixRule)) {
                return [
                    $strRule,
                    $arrParameter
                ];
            }
        }
    }

    /**
     * 创建 DateTime 实例
     *
     * @param string $strFormat
     * @param string $strValue
     * @return \DateTime|null
     */
    protected function makeDateTimeFormat($strFormat, $strValue)
    {
        $date = DateTime::createFromFormat($strFormat, $strValue);

        if ($strValue) {
            return $strValue;
        }

        try {
            return new DateTime($strValue);
        } catch (Exception $oE) {
        }
    }

    /**
     * 数据是否满足正则条件
     *
     * @param string $strField
     * @param array $arrParameter
     * @param int $intLimitLength
     * @return boolean
     */
    protected function checkParameterLength($strField, $arrParameter, $intLimitLength)
    {
        if (count($arrParameter) < $intLimitLength) {
            throw new InvalidArgumentException(sprintf('The rule %s requires at least %d arguments', $strField, $intLimitLength));
        }
    }

    /**
     * 转换消息为数组
     *
     * @param array $arrMessage
     * @return array
     */
    protected function arrayMessage(array $arrMessage)
    {
        $arrResult = [];
        foreach ($arrMessage as $strField => $mixRule) {
            if (strpos($strField, '*') === false) {
                $arrResult = array_merge($arrResult, $this->arrayMessageItem($strField, $mixRule));
            } else {
                $arrResult = array_merge($arrResult, $this->wildcardMessageItem($strField, $mixRule));
            }
        }
        return $arrResult;
    }

    /**
     * 分析通配符消息
     *
     * @param string $strField
     * @param mixed $mixMessage
     * @return array
     */
    protected function wildcardMessageItem($strField, $mixMessage)
    {
        $strField = Helper::prepareRegexForWildcard($strField);

        $arrMessage = [];
        foreach ($this->parseDataKey() as $strKey) {
            if (preg_match($strField, $strKey, $arrRes)) {
                $arrMessage = array_merge($arrMessage, $this->arrayMessageItem($strKey, $mixMessage));
            }
        }

        return $arrMessage;
    }

    /**
     * 转换单条消息为数组
     *
     * @param string $strField
     * @param array|string $mixMessage
     * @return array
     */
    protected function arrayMessageItem($strField, $mixMessage)
    {
        $arrResult = [];

        if (is_array($mixMessage)) {
            foreach ($mixMessage as $strKey => $strMessage) {
                $arrResult[$strField . '.' . $strKey] = $strMessage;
            }
        } else {
            foreach ($this->getFieldRuleWithoutSkip($strField) as $strRule) {
                $arrResult[$strField . '.' . $strRule] = $mixMessage;
            }
        }

        return $arrResult;
    }

    /**
     * 分析验证规则和参数
     *
     * @param string $strRule
     * @return array
     */
    protected function parseRule($strRule)
    {
        $arrParameter = [];

        if (strpos($strRule, ':') !== false) {
            list($strRule, $arrParameter) = explode(':', $strRule, 2);
            if (isset($this->arrAlias[$strRule])) {
                $strRule = $this->arrAlias[$strRule];
            }
            $arrParameter = $this->parseParameters($strRule, $arrParameter);
        }

        return [
            trim($strRule),
            $arrParameter
        ];
    }

    /**
     * 转换规则为数组
     *
     * @param array $arrRule
     * @return array
     */
    protected function arrayRule(array $arrRule)
    {
        $arrResult = [];
        foreach ($arrRule as $strField => $mixRule) {
            if (strpos($strField, '*') === false) {
                $arrResult[$strField] = $this->arrayRuleItem($mixRule);
            } else {
                $arrResult = array_merge($arrResult, $this->wildcardRuleItem($strField, $mixRule));
            }
        }
        return $arrResult;
    }

    /**
     * 转换单条规则为数组
     *
     *
     * @param mixed $mixRule
     * @return array
     */
    protected function arrayRuleItem($mixRule)
    {
        return Helper::arrays($mixRule, '|');
    }

    /**
     * 分析通配符规则
     *
     * @param string $strField
     * @param mixed $mixRule
     * @return array
     */
    protected function wildcardRuleItem($strField, $mixRule)
    {
        $strField = Helper::prepareRegexForWildcard($strField);

        $arrRule = [];
        foreach ($this->parseDataKey() as $strKey) {
            if (preg_match($strField, $strKey, $arrRes)) {
                $arrRule[$strKey] = $this->arrayRuleItem($mixRule);
            }
        }

        return $arrRule;
    }

    /**
     * 返回分析后的数据键
     *
     * @return array
     */
    protected function parseDataKey()
    {
        if (! is_null($this->arrParsedDataKey)) {
            return $this->arrParsedDataKey;
        }

        $this->arrParsedDataKey = [];
        $this->parseDataKeyRecursion($this->getData());
        return $this->arrParsedDataKey;
    }

    /**
     * 清理分析数据键状态
     *
     * @return void
     */
    protected function resetDataKey()
    {
        $this->arrParsedDataKey = null;
    }

    /**
     * 递归分析
     *
     * @param array $arrData
     * @param string $strParentKey
     * @return void
     */
    protected function parseDataKeyRecursion($arrData, $strParentKey = '')
    {
        foreach ($arrData as $strKey => $mixData) {
            $strFoo = ($strParentKey ? $strParentKey . '.' : '') . $strKey;

            if (is_array($mixData)) {
                $this->parseDataKeyRecursion($mixData, $strFoo);
            } else {
                $this->arrParsedDataKey[] = $strFoo;
            }
        }
    }

    /**
     * 是否存在单个字段验证规则
     * 不带条件的简单规则
     *
     * @param string $strField
     * @param string $strRule
     * @return $this
     */
    protected function hasFieldRuleWithoutParameter($strField, $strRule)
    {
        $booFoo = $this->hasFieldRuleWithoutParameterReal($strField, $strRule);

        if (! $booFoo && $strRule == static::DEFAULT_CONDITION) {
            return ! $this->hasFieldRuleWithoutParameterReal($strField, [
                static::CONDITION_MUST,
                static::CONDITION_VALUE
            ]);
        }

        return $booFoo;
    }

    /**
     * 是否存在单个字段验证规则
     * 不带条件的简单规则
     *
     * @param string $strField
     * @param mixed $mixRule
     * @param boolean $booStrict
     * @return $this
     */
    protected function hasFieldRuleWithoutParameterReal($strField, $mixRule, $booStrict = false)
    {
        if (! isset($this->arrRule[$strField])) {
            return false;
        }

        $mixRule = ( array ) $mixRule;

        foreach ($mixRule as $strRule) {
            if ($booStrict) {
                if (! in_array($strRule, $this->arrRule[$strField])) {
                    return false;
                }
            } elseif (in_array($strRule, $this->arrRule[$strField])) {
                return true;
            }
        }

        return false;
    }

    /**
     * 解析变量
     *
     * @param string $strRule
     * @param string $strParameter
     * @return array
     */
    protected function parseParameters($strRule, $strParameter)
    {
        if (strtolower($strRule) == 'regex') {
            return [
                $strParameter
            ];
        }
        return explode(',', $strParameter);
    }

    /**
     * 验证字段规则
     *
     * @param string $strField
     * @param string $strRule
     * @return void|boolean
     */
    protected function doValidateItem($strField, $strRule)
    {
        list($strRule, $arrParameter) = $this->parseRule($strRule);

        if ($strRule == '') {
            return;
        }

        $mixFieldValue = $this->getFieldValue($strField);

        // 默认情况下存在即验证，没有设置字段则跳过
        if (! $this->hasFieldValue($strField) && $this->hasFieldRuleWithoutParameter($strField, static::CONDITION_EXISTS)) {
            return;
        }

        // 值不为空就验证，那么为的空的值将会跳过
        if (empty($mixFieldValue) && $this->hasFieldRuleWithoutParameter($strField, static::CONDITION_VALUE)) {
            return;
        }

        if (! $this->{'validate' . ucwords(Str::camelize($strRule))}($strField, $mixFieldValue, $arrParameter)) {
            $this->addFailure($strField, $strRule, $arrParameter);
            return false;
        }
        unset($mixFieldValue);
        return true;
    }

    /**
     * 是否需要终止其他验证
     *
     * @param string $strField
     * @return boolean
     */
    protected function shouldSkipOther($strField)
    {
        return $this->hasFieldRuleWithoutParameter($strField, static::SKIP_OTHER);
    }

    /**
     * 是否需要终止自己其他验证
     *
     * @param string $strField
     * @return boolean
     */
    protected function shouldSkipSelf($strField)
    {
        return $this->hasFieldRuleWithoutParameter($strField, static::SKIP_SELF);
    }

    /**
     * 添加错误规则和验证错误消息
     *
     * @param string $strField
     * @param string $strRule
     * @param array $arrParameter
     * @return void
     */
    protected function addFailure($strField, $strRule, $arrParameter)
    {
        $this->addError($strField, $strRule, $arrParameter);
        $this->arrFailedRules[$strField][$strRule] = $arrParameter;
    }

    /**
     * 添加验证错误消息
     *
     * @param string $strField
     * @param string $strRule
     * @param array $arrParameter
     * @return void
     */
    protected function addError($strField, $strRule, $arrParameter)
    {
        $strMessage = $this->getFieldRuleMessage($strField, $strRule);

        $arrReplace = [
            'field' => $this->parseFieldName($strField)
        ];

        if (! $this->isImplodeRuleParameter($strRule)) {
            foreach ($arrParameter as $intKey => $mixParameter) {
                $arrReplace['rule' . ($intKey ?  : '')] = $mixParameter;
            }
        } else {
            $arrReplace['rule'] = implode(',', $arrParameter);
        }

        $strMessage = preg_replace_callback("/{(.+?)}/", function ($arrMatche) use ($arrReplace) {
            return $arrReplace[$arrMatche[1]] ?? $arrMatche[0];
        }, $strMessage);

        $this->arrErrorMessages[$strField][] = $strMessage;
        unset($arrReplace, $strMessage);
    }

    /**
     * 获取验证消息
     *
     * @param string $strField
     * @param string $strRule
     * @return string
     */
    protected function getFieldRuleMessage($strField, $strRule)
    {
        return $this->arrMessage[$strField . '.' . $strRule] ?? $this->arrMessage[$strRule] ?? static::$arrDefaultMessage[$strRule] ?? '';
    }

    /**
     * 获取字段名字
     *
     * @param string $strField
     * @return string
     */
    protected function parseFieldName($strField)
    {
        return $this->arrFieldName[$strField] ?? $strField;
    }

    /**
     * 获取字段的值
     *
     * @param string $strRule
     * @return mixed
     */
    protected function getFieldValue($strRule)
    {
        if (strpos($strRule, '.') === false) {
            if (isset($this->arrData[$strRule])) {
                return $this->arrData[$strRule];
            }
        } else {
            $strRule = explode('.', $strRule);

            $strFoo = '$this->arrData';
            for ($nI = 0; $nI < count($strRule); $nI ++) {
                $strFoo .= "['{$strRule[$nI]}']";
            }

            eval("\$strFoo = $strFoo ?? null;");
            return $strFoo;
        }

        return null;
    }

    /**
     * 是否存在字段的值
     *
     * @param string $strRule
     * @return boolean
     */
    protected function hasFieldValue($strRule)
    {
        return isset($this->arrData[$strRule]);
    }

    /**
     * 返回需要合并的规则参数
     *
     * @param string $strRule
     * @return boolean
     */
    protected function isImplodeRuleParameter($strRule)
    {
        return in_array($strRule, [
            'in',
            'not_in',
            'allow_ip',
            'deny_ip'
        ]);
    }

    /**
     * 调用自定义验证器类
     *
     * @param string $strExtend
     * @param array $arrParameter
     * @return bool
     */
    protected function callClassExtend($strExtend, array $arrParameter)
    {
        if (! $this->objContainer) {
            throw new Exception('Container has not set yet');
        }

        if (strpos($strExtend, '@') === false) {
            $strClass = $strExtend;
            $strMethod = 'handle';
        } else {
            list($strClass, $strMethod) = explode('@', $strExtend);
        }

        if (($objExtend = $this->objContainer->make($strClass)) === false) {
            throw new InvalidArgumentException(sprintf('Extend class %s is not valid.', $strClass));
        }

        $strMethod = method_exists($objExtend, $strMethod) ? $strMethod : ($strMethod != 'handle' && method_exists($objExtend, 'handle') ? 'handle' : 'run');

        $arrParameter[] = $this;

        return call_user_func_array([
            $objExtend,
            $strMethod
        ], $arrParameter);
    }

    /**
     * 调用自定义验证器
     *
     * @param string $strRule
     * @param array $arrParameter
     * @return boolean|null
     */
    protected function callExtend($strRule, $arrParameter)
    {
        $mixExtend = $this->arrExtend[$strRule];

        if (is_callable($mixExtend)) {
            $arrParameter[] = $this;
            return call_user_func_array($mixExtend, $arrParameter);
        } elseif (is_string($mixExtend)) {
            return $this->callClassExtend($mixExtend, $arrParameter);
        }
    }

    /**
     * 验证条件是否通过
     *
     * @param callable|mixed $calCallback
     * @return boolean
     */
    protected function isCallbackValid($mixCallback = null)
    {
        $booFoo = false;

        if (! is_string($mixCallback) && is_callable($mixCallback)) {
            $booFoo = call_user_func($mixCallback, $this->getData());
        } else {
            $booFoo = $mixCallback;
        }

        return $booFoo;
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        if ($this->placeholderFlowControl($method)) {
            return $this;
        }

        $sExtend = Str::unCamelize(substr($method, 8));
        if (isset($this->arrExtend[$sExtend])) {
            return $this->callExtend($sExtend, $arrArgs);
        }

        if (count($arrArgs) > 0) {
            $sExtend = 'validate' . ucwords($method);

            $arrParameter = [
                'foobar'
            ];
            $arrParameter[] = array_shift($arrArgs);
            $arrParameter[] = $arrArgs;
            unset($arrArgs);

            if (method_exists($this, $sExtend)) {
                return $this->$sExtend(...$arrParameter);
            }

            $sExtend = Str::unCamelize($method);
            if (isset($this->arrExtend[$sExtend])) {
                return $this->callExtend($sExtend, $arrParameter);
            }
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }
}

if (! function_exists('__')) {
    /**
     * lang
     *
     * @param array $arr
     * @return string
     */
    function __(...$arr)
    {
        return count($arr) == 0 ? '' : (count($arr) > 1 ? sprintf(...$arr) : $arr[0]);
    }
}
