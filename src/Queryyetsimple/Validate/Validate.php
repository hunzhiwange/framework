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

namespace Leevel\Validate;

use BadMethodCallException;
use DateTime;
use DateTimeZone;
use Exception;
use InvalidArgumentException;
use Leevel\Di\IContainer;
use Leevel\Flow\TControl;
use Leevel\Support\Arr;
use Leevel\Support\Str;

/**
 * validate 数据验证器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.02
 *
 * @version 1.0
 */
class Validate implements IValidate
{
    use TControl;

    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 待验证数据.
     *
     * @var array
     */
    protected $datas = [];

    /**
     * 验证规则.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * 默认验证提示信息.
     *
     * @var array
     */
    protected static $defaultMessages = [];

    /**
     * 是否初始化默认验证提示信息.
     *
     * @var bool
     */
    protected static $initDefaultMessages = false;

    /**
     * 验证提示信息.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * 字段名字.
     *
     * @var array
     */
    protected $fieldName = [];

    /**
     * 错误规则.
     *
     * @var array
     */
    protected $failedRules;

    /**
     * 错误消息.
     *
     * @var array
     */
    protected $errorMessages = [];

    /**
     * 需要跳过的验证规则
     * 用于扩展属性支持
     *
     * @var array
     */
    protected $skipRule = [];

    /**
     * 分析数据键
     * like this hello.world.foobar.
     *
     * @var array
     */
    protected $parsedDataKey;

    /**
     * 扩展验证器.
     *
     * @var array
     */
    protected $extends = [];

    /**
     * 验证后续事件.
     *
     * @var array
     */
    protected $afters = [];

    /**
     * 验证别名.
     *
     * @var array
     */
    protected $alias = [
        'confirm' => 'equal_to',
        'gt'      => 'greater_than',
        '>'       => 'greater_than',
        'egt'     => 'equal_greater_than',
        '>='      => 'equal_greater_than',
        'lt'      => 'less_than',
        '<'       => 'less_than',
        'elt'     => 'equal_less_than',
        '<='      => 'equal_less_than',
        'eq'      => 'equal',
        '='       => 'equal',
        'neq'     => 'not_equal',
        '!='      => 'not_equal',
    ];

    /**
     * 构造函数.
     *
     * @param array $datas
     * @param array $rules
     * @param array $fieldName
     * @param array $messages
     */
    public function __construct(array $datas = [], array $rules = [], array $fieldName = [], array $messages = [])
    {
        $this->data($datas);
        $this->rule($rules);
        $this->fieldName($fieldName);
        $this->message($messages);

        static::defaultMessage();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if ($this->placeholderTControl($method)) {
            return $this;
        }

        $extend = Str::unCamelize(substr($method, 8));

        if (isset($this->extends[$extend])) {
            return $this->callExtend($extend, $args);
        }

        if (count($args) > 0) {
            $extend = 'validate'.ucwords($method);

            $parameter = [
                'foobar',
            ];
            $parameter[] = array_shift($args);
            $parameter[] = $args;
            unset($args);

            if (method_exists($this, $extend)) {
                return $this->{$extend}(...$parameter);
            }

            $extend = Str::unCamelize($method);

            if (isset($this->extends[$extend])) {
                return $this->callExtend($extend, $parameter);
            }
        }

        throw new BadMethodCallException(
            sprintf('Method %s is not exits.', $method)
        );
    }

    /**
     * 初始化验证器.
     *
     * @param array $datas
     * @param array $rules
     * @param array $fieldName
     * @param array $messages
     *
     * @return \Leevel\Validate
     */
    public static function make(array $datas = [], array $rules = [], array $fieldName = [], array $messages = [])
    {
        return new static($datas, $rules, $fieldName, $messages);
    }

    /**
     * 验证是否成功
     *
     * @return bool
     */
    public function success()
    {
        $skipRule = $this->getSkipRule();

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                if (in_array($rule, $skipRule, true)) {
                    continue;
                }

                if (false === $this->doValidateItem($field, $rule)) {
                    // 验证失败跳过剩余验证规则
                    if ($this->shouldSkipOther($field)) {
                        break 2;
                    }

                    // 验证失败跳过自身剩余验证规则
                    if ($this->shouldSkipSelf($field)) {
                        break;
                    }
                }
            }
        }

        unset($skipRule);

        foreach ($this->afters as $calAfter) {
            call_user_func($calAfter);
        }

        return 0 === count($this->errorMessages);
    }

    /**
     * 验证是否失败.
     *
     * @return bool
     */
    public function fail()
    {
        return !$this->success();
    }

    /**
     * 返回所有错误消息.
     *
     * @return array
     */
    public function error()
    {
        return $this->errorMessages;
    }

    /**
     * 返回验证数据.
     *
     * @return array
     */
    public function getData()
    {
        return $this->datas;
    }

    /**
     * 设置验证数据.
     *
     * @param array $datas
     *
     * @return $this
     */
    public function data(array $datas)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->datas = $datas;

        return $this;
    }

    /**
     * 添加验证数据.
     *
     * @param array $datas
     *
     * @return $this
     */
    public function addData(array $datas)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->datas = array_merge($this->datas, $datas);

        return $this;
    }

    /**
     * 设置单个字段验证数据.
     *
     * @param string $field
     * @param mixed  $datas
     *
     * @return $this
     */
    public function fieldData($field, $datas)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->datas[$field] = $datas;

        return $this;
    }

    /**
     * 返回验证规则.
     *
     * @return array
     */
    public function getRule()
    {
        return $this->rules;
    }

    /**
     * 设置验证规则.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function rule(array $rules)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->rules = $this->arrayRule($rules);

        return $this;
    }

    /**
     * 设置验证规则,带上条件.
     *
     * @param array          $rules
     * @param callable|mixed $calCallback
     * @param mixed          $callbacks
     *
     * @return $this
     */
    public function ruleIf(array $rules, $callbacks)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if ($this->isCallbackValid($callbacks)) {
            return $this->rule($rules);
        }

        return $this;
    }

    /**
     * 添加验证规则.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function addRule(array $rules)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->rules = array_merge($this->rules, $this->arrayRule($rules));

        return $this;
    }

    /**
     * 添加验证规则,带上条件.
     *
     * @param array          $rules
     * @param callable|mixed $calCallback
     * @param mixed          $callbacks
     *
     * @return $this
     */
    public function addRuleIf(array $rules, $callbacks)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if ($this->isCallbackValid($callbacks)) {
            return $this->addRule($rules);
        }

        return $this;
    }

    /**
     * 设置单个字段验证规则.
     *
     * @param string $field
     * @param mixed  $rules
     *
     * @return $this
     */
    public function fieldRule($field, $rules)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }

        $this->rules[$field] = $this->arrayRuleItem($rules);

        return $this;
    }

    /**
     * 设置单个字段验证规则,带上条件.
     *
     * @param string         $field
     * @param mixed          $rules
     * @param callable|mixed $calCallback
     * @param mixed          $callbacks
     *
     * @return $this
     */
    public function fieldRuleIf($field, $rules, $callbacks)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if ($this->isCallbackValid($callbacks)) {
            return $this->fieldRule($field, $rules);
        }

        return $this;
    }

    /**
     * 添加单个字段验证规则.
     *
     * @param string $field
     * @param mixed  $rules
     *
     * @return $this
     */
    public function addFieldRule($field, $rules)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (!isset($this->rules[$field])) {
            $this->rules[$field] = [];
        }

        $this->rules[$field] = array_merge(
            $this->rules[$field],
            $this->arrayRuleItem($rules)
        );

        return $this;
    }

    /**
     * 添加单个字段验证规则,带上条件.
     *
     * @param string         $field
     * @param mixed          $rules
     * @param callable|mixed $calCallback
     * @param mixed          $callbacks
     *
     * @return $this
     */
    public function addFieldRuleIf($field, $rules, $callbacks)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if ($this->isCallbackValid($callbacks)) {
            return $this->addFieldRule($field, $rules);
        }

        return $this;
    }

    /**
     * 获取单个字段验证规则.
     *
     * @param string $field
     *
     * @return array
     */
    public function getFieldRule($field)
    {
        if (isset($this->rules[$field])) {
            return $this->rules[$field];
        }

        return [];
    }

    /**
     * 获取单个字段验证规则，排除掉绕过的规则.
     *
     * @param string $field
     *
     * @return array
     */
    public function getFieldRuleWithoutSkip($field)
    {
        return array_diff($this->getFieldRule($field), $this->getSkipRule());
    }

    /**
     * 返回验证消息.
     *
     * @return array
     */
    public function getMessage()
    {
        return $this->messages;
    }

    /**
     * 设置验证消息.
     *
     * @param array $messages
     *
     * @return $this
     */
    public function message(array $messages)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->messages = $messages;

        return $this;
    }

    /**
     * 添加验证消息.
     *
     * @param array $messages
     *
     * @return $this
     */
    public function addMessage(array $messages)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->messages = array_merge(
            $this->messages,
            $this->arrayMessage($messages)
        );

        return $this;
    }

    /**
     * 返回字段名字.
     *
     * @return array
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * 设置字段名字.
     *
     * @param array $fieldName
     *
     * @return $this
     */
    public function fieldName(array $fieldName)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * 添加字段名字.
     *
     * @param array $fieldName
     *
     * @return $this
     */
    public function addFieldName(array $fieldName)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->fieldName = array_merge(
            $this->fieldName,
            $this->arrayMessage($fieldName)
        );

        return $this;
    }

    /**
     * 设置单个字段验证消息.
     *
     * @param string $fieldRule
     * @param string $message
     *
     * @return $this
     */
    public function fieldRuleMessage($fieldRule, $message)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->messages[$fieldRule] = $message;

        return $this;
    }

    /**
     * 设置别名.
     *
     * @param key $alias
     * @param key $for
     *
     * @return $this
     */
    public function alias($alias, $for)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (in_array($alias, $this->getSkipRule(), true)) {
            throw new Exception(
                spintf('You cannot set alias for skip rule %s', $alias)
            );
        }

        $this->alias[$alias] = $for;

        return $this;
    }

    /**
     * 批量设置别名.
     *
     * @param array $alias
     *
     * @return $this
     */
    public function aliasMany(array $alias)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        foreach ($alias as $alias => $for) {
            $this->alias($alias, $for);
        }

        return $this;
    }

    /**
     * 返回别名.
     *
     * @return array
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * 设置验证后事件.
     *
     * @param callable|string $callbacks
     *
     * @return $this
     */
    public function after($callbacks)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->afters[] = function () use ($callbacks) {
            return call_user_func_array($callbacks, [
                $this,
            ]);
        };

        return $this;
    }

    /**
     * 返回所有验证后事件.
     *
     * @return array
     */
    public function getAfter()
    {
        return $this->afters;
    }

    /**
     * 返回所有自定义扩展.
     *
     * @return array
     */
    public function getExtend()
    {
        return $this->extends;
    }

    /**
     * 注册自定义扩展.
     *
     * @param string          $rule
     * @param callable|string $extends
     * @param mixed           $rule
     *
     * @return $this
     */
    public function extend($rule, $extends)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->extends[strtolower($rule)] = $extends;

        return $this;
    }

    /**
     * 批量注册自定义扩展.
     *
     * @param array $extends
     *
     * @return $this
     */
    public function extendMany(array $extends)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->extends = array_merge($this->extends, $extends);

        return $this;
    }

    /**
     * 设置 IOC 容器.
     *
     * @param \Leevel\Di\IContainer $container
     *
     * @return $this
     */
    public function container(IContainer $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * 获取需要跳过的验证规则.
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
            static::SKIP_OTHER,
        ], $this->skipRule);
    }

    /**
     * 设置默认的消息.
     */
    public static function defaultMessage()
    {
        if (static::$initDefaultMessages) {
            return;
        }

        static::$defaultMessages = [
            'required'           => __('{field} 不能为空'),
            'number'             => __('{field} 必须是数字'),
            'float'              => __('{field} 必须是浮点数'),
            'double'             => __('{field} 必须是双精度浮点数'),
            'boolean'            => __('{field} 必须是布尔值'),
            'array'              => __('{field} 必须是数组'),
            'accepted'           => __('{field} 必须是 yes、on、true 或者 1'),
            'date'               => __('{field} 不是正确的日期格式'),
            'date_format'        => __('{field} 必须使用日期格式 {rule}'),
            'timezone'           => __('{field} 不是正确的时区'),
            'alpha'              => __('{field} 只能是字母'),
            'alpha_upper'        => __('{field} 只能是大写字母'),
            'alpha_lower'        => __('{field} 只能是小写字母'),
            'alpha_num'          => __('{field} 只能是字母和数字'),
            'alpha_dash'         => __('{field} 只能是字母、数字、短横线和下划线'),
            'chinese'            => __('{field} 只能是汉字'),
            'chinese_alpha_num'  => __('{field} 只能是汉字、字母、数字'),
            'chinese_alpha_dash' => __('{field} 只能是汉字、字母、数字、短横线和下划线'),
            'url'                => __('{field} 不是有效的 URL 地址'),
            'active_url'         => __('{field} 不是有效的域名或者 IP'),
            'ip'                 => __('{field} 不是有效的 IP 地址'),
            'ipv4'               => __('{field} 不是有效的 IPV4 地址'),
            'ipv6'               => __('{field} 不是有效的 IPV6 地址'),
            'in'                 => __('{field} 必须在 {rule} 范围内'),
            'not_in'             => __('{field} 不能在 {rule} 范围内'),
            'between'            => __('{field} 只能在 {rule} 和 {rule1} 之间，不包含等于'),
            'not_between'        => __('{field} 不在 {rule} 和 {rule1} 之间，不包含等于'),
            'between_equal'      => __('{field} 只能在 {rule} 和 {rule1} 之间，包含等于'),
            'not_between_equal'  => __('{field} 不在 {rule} 和 {rule1} 之间，包含等于'),
            'greater_than'       => __('{field} 必须大于 {rule}'),
            'equal_greater_than' => __('{field} 必须大于等于 {rule}'),
            'less_than'          => __('{field} 必须小于 {rule}'),
            'equal_less_than'    => __('{field} 必须小于等于 {rule}'),
            'equal'              => __('{field} 必须等于 {rule}'),
            'not_equal'          => __('{field} 不能等于 {rule}'),
            'equal_to'           => __('{field} 必须等于字段 {rule}'),
            'different'          => __('{field} 不能等于字段 {rule}'),
            'same'               => __('{field} 必须完全等于 {rule}'),
            'not_same'           => __('{field} 不能完全等于 {rule}'),
            'empty'              => __('{field} 必须为空'),
            'not_empty'          => __('{field} 不能为空'),
            'null'               => __('{field} 必须 null'),
            'not_null'           => __('{field} 不能为 null'),
            'strlen'             => __('{field} 长度不符合要求 {rule}'),
            'max'                => __('{field} 长度不能超过 {rule}'),
            'min'                => __('{field} 长度不能小于 {rule}'),
            'digit'              => __('{field} 字符串中的字符必须都是数字'),
            'type'               => __('{field} 类型不符合要求 {rule}'),
            'lower'              => __('{field} 必须全部是小写'),
            'upper'              => __('{field} 必须全部是大写'),
            'min_length'         => __('{field} 不满足最小长度 {rule}'),
            'max_length'         => __('{field} 不满足最大长度 {rule}'),
            'id_card'            => __('{field} 必须是有效的中国大陆身份证'),
            'zip_code'           => __('{field} 必须是有效的中国邮政编码'),
            'qq'                 => __('{field} 必须是有效的 QQ 号码'),
            'phone'              => __('{field} 必须是有效的电话号码或者手机号'),
            'mobile'             => __('{field} 必须是有效的手机号'),
            'telephone'          => __('{field} 必须是有效的电话号码'),
            'email'              => __('{field} 必须为正确的电子邮件格式'),
            'luhn'               => __('{field} 必须为正确的符合 luhn 格式算法银行卡'),
            'after'              => __('{field} 日期不能小于 {rule}'),
            'before'             => __('{field} 日期不能超过 {rule}'),
            'allow_ip'           => __('{field} 不允许的 IP 访问 {rule}'),
            'deny_ip'            => __('{field} 禁止的 IP 访问 {rule}'),
            'method'             => __('无效的请求类型 {rule}'),
            'json'               => __('{field} 不是有效的 JSON'),
        ];

        static::$initDefaultMessages = true;
    }

    /**
     * 不能为空.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateRequired($field, $datas, $parameter)
    {
        if (null === $datas) {
            return false;
        }

        if (is_string($datas) && '' === trim($datas)) {
            return false;
        }

        return true;
    }

    /**
     * 是否为日期
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateDate($field, $datas, $parameter)
    {
        if ($datas instanceof DateTime) {
            return true;
        }

        if (false === strtotime($datas)) {
            return false;
        }

        $datas = date_parse($datas);

        return checkdate($datas['month'], $datas['day'], $datas['year']);
    }

    /**
     * 是否为时间.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateDateFormat($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        $parse = date_parse_from_format($parameter[0], $datas);

        return 0 === $parse['error_count'] && 0 === $parse['warning_count'];
    }

    /**
     * 是否为正确的时区.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateTimezone($field, $datas, $parameter)
    {
        try {
            new DateTimeZone($datas);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * 验证在给定日期之后.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAfter($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        if ($format = $this->getDateFormat($field)) {
            return $this->doAfterWithFormat($format, $datas, $parameter);
        }

        if (!($time = strtotime($parameter[0]))) {
            return strtotime($datas) > strtotime($this->getFieldValue($parameter[0]));
        }

        return strtotime($datas) > $time;
    }

    /**
     * 验证在给定日期之前.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateBefore($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        if ($format = $this->getDateFormat($field)) {
            return $this->doBeforeWithFormat($format, $datas, $parameter);
        }

        if (!($time = strtotime($parameter[0]))) {
            return strtotime($datas) < strtotime($this->getFieldValue($parameter[0]));
        }

        return strtotime($datas) < $time;
    }

    /**
     * 检测字符串中的字符是否都是数字，负数和小数会检测不通过.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateDigit($field, $datas, $parameter)
    {
        return ctype_digit($datas);
    }

    /**
     * 是否双精度浮点数.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateDouble($field, $datas, $parameter)
    {
        return preg_match('/^[-\+]?\d+(\.\d+)?$/', $datas);
    }

    /**
     * 是否可接受的.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAccepted($field, $datas, $parameter)
    {
        return $this->validateRequired($field, $datas, $parameter) &&
            in_array($datas, [
                'yes',
                'on',
                '1',
                1,
                true,
                'true',
            ], true);
    }

    /**
     * 是否整型数字.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateInteger($field, $datas, $parameter)
    {
        return false !== filter_var($datas, FILTER_VALIDATE_INT);
    }

    /**
     * 验证是否为浮点数.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateFloat($field, $datas, $parameter)
    {
        return false !== filter_var($datas, FILTER_VALIDATE_FLOAT);
    }

    /**
     * 验证是否为数组.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateArray($field, $datas, $parameter)
    {
        return is_array($datas);
    }

    /**
     * 验证是否为布尔值
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateBoolean($field, $datas, $parameter)
    {
        return in_array($datas, [
            true,
            false,
            0,
            1,
            '0',
            '1',
        ], true);
    }

    /**
     * 是否为数字.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNumber($field, $datas, $parameter)
    {
        return is_numeric($datas);
    }

    /**
     * 处于 between 范围，不包含等于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateBetween($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 2);

        return $datas > $parameter[0] && $datas < $parameter[1];
    }

    /**
     * 未处于 between 范围，不包含等于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNotBetween($field, $datas, $parameter)
    {
        return !$this->validateBetweenEqual($field, $datas, $parameter);
    }

    /**
     * 处于 betweenEqual 范围，包含等于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateBetweenEqual($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 2);

        return $datas >= $parameter[0] && $datas <= $parameter[1];
    }

    /**
     * 未处于 betweenEqual 范围，包含等于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNotBetweenEqual($field, $datas, $parameter)
    {
        return !$this->validateBetween($field, $datas, $parameter);
    }

    /**
     * 是否处于某个范围.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateIn($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return in_array($datas, $parameter, true);
    }

    /**
     * 是否不处于某个范围.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNotIn($field, $datas, $parameter)
    {
        return !$this->validateIn($field, $datas, $parameter);
    }

    /**
     * 是否为合法的 IP 地址
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateIp($field, $datas, $parameter)
    {
        return false !== filter_var($datas, FILTER_VALIDATE_IP);
    }

    /**
     * 是否为 ipv4.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateIpv4($field, $datas, $parameter)
    {
        return false !== filter_var($datas, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
    }

    /**
     * 是否为 ipv6.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateIpv6($field, $datas, $parameter)
    {
        return false !== filter_var($datas, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
    }

    /**
     * 大于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateGreaterThan($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas > $parameter[0];
    }

    /**
     * 大于或者等于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateEqualGreaterThan($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas >= $parameter[0];
    }

    /**
     * 小于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateLessThan($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas < $parameter[0];
    }

    /**
     * 小于或者等于.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateEqualLessThan($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas <= $parameter[0];
    }

    /**
     * 两个值是否相同.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateEqual($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas === $parameter[0];
    }

    /**
     * 两个值是否不相同.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNotEqual($field, $datas, $parameter)
    {
        return !$this->validateEqual($field, $datas, $parameter);
    }

    /**
     * 两个字段是否相同.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateEqualTo($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas === $this->getFieldValue($parameter[0]);
    }

    /**
     * 两个字段是否不同.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateDifferent($field, $datas, $parameter)
    {
        return $this->validateEqualTo($field, $datas, $parameter);
    }

    /**
     * 两个值是否完全相同.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateSame($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas === $parameter[0];
    }

    /**
     * 两个值是否不完全相同.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNotSame($field, $datas, $parameter)
    {
        return !$this->validateSame($field, $datas, $parameter);
    }

    /**
     * 验证值上限.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateMax($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas <= $parameter[0];
    }

    /**
     * 验证值下限.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateMin($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return $datas >= $parameter[0];
    }

    /**
     * 值是否为空.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateEmpty($field, $datas, $parameter)
    {
        return empty($datas);
    }

    /**
     * 值是否不为空.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNotEmpty($field, $datas, $parameter)
    {
        return !$this->validateEmpty($field, $datas, $parameter);
    }

    /**
     * 是否为 null.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNull($field, $datas, $parameter)
    {
        return null === $datas;
    }

    /**
     * 是否不为 null.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateNotNull($field, $datas, $parameter)
    {
        return !$this->validateNull($field, $datas, $parameter);
    }

    /**
     * 是否为英文字母.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAlpha($field, $datas, $parameter)
    {
        return preg_match('/^[A-Za-z]+$/', $datas);
    }

    /**
     * 是否为大写英文字母.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAlphaUpper($field, $datas, $parameter)
    {
        return preg_match('/^[A-Z]+$/', $datas);
    }

    /**
     * 是否为小写英文字母.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAlphaLower($field, $datas, $parameter)
    {
        return preg_match('/^[a-z]+$/', $datas);
    }

    /**
     * 字符串是否为数字和字母.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAlphaNum($field, $datas, $parameter)
    {
        return preg_match('/^[A-Za-z0-9]+$/', $datas);
    }

    /**
     * 字符串是否为数字、下划线、短横线和字母.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAlphaDash($field, $datas, $parameter)
    {
        return preg_match('/^[A-Za-z0-9\-\_]+$/', $datas);
    }

    /**
     * 是否为中文.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateChinese($field, $datas, $parameter)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $datas);
    }

    /**
     * 是否为中文、数字和字母.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateChineseAlphaNum($field, $datas, $parameter)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $datas);
    }

    /**
     * 是否为中文、数字、下划线、短横线和字母.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateChineseAlphaDash($field, $datas, $parameter)
    {
        return preg_match('/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u', $datas);
    }

    /**
     * 是否为大陆身份证
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateIdCard($field, $datas, $parameter)
    {
        return preg_match(
            '/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/',
            $datas
        );
    }

    /**
     * 是否为中国邮政编码
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateZipCode($field, $datas, $parameter)
    {
        return preg_match('/^[1-9]\d{5}$/', $datas);
    }

    /**
     * 是否为 QQ 号码
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateQq($field, $datas, $parameter)
    {
        return preg_match('/^[1-9]\d{4,11}$/', $datas);
    }

    /**
     * 值是否为电话号码或者手机号码
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validatePhone($field, $datas, $parameter)
    {
        return (11 === strlen($datas) &&
            preg_match('/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/', $datas)) ||
            preg_match('/^\d{3,4}-?\d{7,9}$/', $datas);
    }

    /**
     * 值是否为手机号码
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateMobile($field, $datas, $parameter)
    {
        return preg_match(
            '/^13[0-9]{9}|15[012356789][0-9]{8}|18[0-9]{9}|14[579][0-9]{8}|17[0-9]{9}$/',
            $datas
        );
    }

    /**
     * 值是否为电话号码
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateTelephone($field, $datas, $parameter)
    {
        return preg_match('/^\d{3,4}-?\d{7,9}$/', $datas);
    }

    /**
     * 值是否为银行卡等符合 luhn 算法.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateLuhn($field, $datas, $parameter)
    {
        $total = 0;

        for ($i = strlen($datas); $i >= 1; $i--) {
            $index = $i - 1;

            if (0 === $i % 2) {
                $total += $datas[$index];
            } else {
                $m = $datas[$index] * 2;

                if ($m > 9) {
                    $m = (int) ($m / 10) + $m % 10;
                }

                $total += $m;
            }
        }

        return 0 === ($total % 10);
    }

    /**
     * 验证是否为有效的 url 或者 IP 地址
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateActiveUrl($field, $datas, $parameter)
    {
        return checkdnsrr($datas);
    }

    /**
     * 验证是否为 url 地址
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateUrl($field, $datas, $parameter)
    {
        return false !== filter_var($datas, FILTER_VALIDATE_URL);
    }

    /**
     * 是否为电子邮件.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateEmail($field, $datas, $parameter)
    {
        return false !== filter_var($datas, FILTER_VALIDATE_EMAIL);
    }

    /**
     * 长度验证
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateStrlen($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return strlen($datas) === (int) $parameter[0];
    }

    /**
     * 数据类型验证
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateType($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return gettype($datas) === $parameter[0];
    }

    /**
     * 验证是否都是小写.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateLower($field, $datas, $parameter)
    {
        return ctype_lower($datas);
    }

    /**
     * 验证是否都是大写.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateUpper($field, $datas, $parameter)
    {
        return ctype_upper($datas);
    }

    /**
     * 验证数据最小长度.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateMinLength($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return iconv_strlen($datas, 'utf-8') >= (int) $parameter[0];
    }

    /**
     * 验证数据最大长度.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateMaxLength($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return iconv_strlen($datas, 'utf-8') <= (int) $parameter[0];
    }

    /**
     * 验证 IP 许可.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateAllowIp($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return in_array($datas ?: $_SERVER['REMOTE_ADDR'], $parameter, true);
    }

    /**
     * 验证 IP 禁用.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateDenyIp($field, $datas, $parameter)
    {
        return !$this->validateAllowIp($field, $datas, $parameter);
    }

    /**
     * 验证请求类型.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateMethod($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return strtolower($datas ?:
            (PHP_SAPI === 'cli' ? 'GET' : $_SERVER['REQUEST_METHOD'])
        ) === strtolower($parameter[0]);
    }

    /**
     * 验证是否为正常的 JSON 字符串.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateJson($field, $datas, $parameter)
    {
        if (!is_scalar($datas) && !method_exists($datas, '__toString')) {
            return false;
        }

        json_decode($datas);

        return JSON_ERROR_NONE === json_last_error();
    }

    /**
     * 数据是否满足正则条件.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function validateRegex($field, $datas, $parameter)
    {
        $this->checkParameterLength($field, $parameter, 1);

        return preg_match($parameter[0], $datas) > 0;
    }

    /**
     * 验证在给定日期之前.
     *
     * @param string $format
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function doBeforeWithFormat($format, $datas, $parameter)
    {
        $parameter[0] = $this->getFieldValue($parameter[0]) ?: $parameter[0];

        return $this->doCheckBeforeAfter($format, $datas, $parameter[0]);
    }

    /**
     * 验证在给定日期之后.
     *
     * @param string $format
     * @param mixed  $datas
     * @param array  $parameter
     *
     * @return bool
     */
    protected function doAfterWithFormat($format, $datas, $parameter)
    {
        $parameter[0] = $this->getFieldValue($parameter[0]) ?: $parameter[0];

        return $this->doCheckBeforeAfter($format, $parameter[0], $datas);
    }

    /**
     * 验证日期顺序.
     *
     * @param string $format
     * @param string $first
     * @param string $second
     *
     * @return bool
     */
    protected function doCheckBeforeAfter($format, $first, $second)
    {
        $before = $this->makeDateTimeFormat($format, $first);
        $after = $this->makeDateTimeFormat($format, $second);

        return $before && $after && $before < $after;
    }

    /**
     * 获取时间格式化.
     *
     * @param string $field
     *
     * @return null|string
     */
    protected function getDateFormat($field)
    {
        if ($result = $this->getParseRule($field, 'date_format')) {
            return $result[1][0];
        }
    }

    /**
     * 尝试读取格式化条件.
     *
     * @param string       $field
     * @param array|string $rules
     *
     * @return null|array
     */
    protected function getParseRule($field, $rules)
    {
        if (!array_key_exists($field, $this->rules)) {
            return;
        }

        $rules = (array) $rules;

        foreach ($this->rules[$field] as $rule) {
            list($rule, $parameter) = $this->parseRule($rule);

            if (in_array($rule, $rules, true)) {
                return [
                    $rule,
                    $parameter,
                ];
            }
        }
    }

    /**
     * 创建 DateTime 实例.
     *
     * @param string $format
     * @param string $value
     *
     * @return null|\DateTime
     */
    protected function makeDateTimeFormat($format, $value)
    {
        $date = DateTime::createFromFormat($format, $value);

        if ($value) {
            return $value;
        }

        try {
            return new DateTime($value);
        } catch (Exception $e) {
        }
    }

    /**
     * 数据是否满足正则条件.
     *
     * @param string $field
     * @param array  $parameter
     * @param int    $limitLength
     *
     * @return bool
     */
    protected function checkParameterLength($field, $parameter, $limitLength)
    {
        if (count($parameter) < $limitLength) {
            throw new InvalidArgumentException(
                sprintf(
                    'The rule %s requires at least %d arguments',
                    $field,
                    $limitLength
                )
            );
        }
    }

    /**
     * 转换消息为数组.
     *
     * @param array $messages
     *
     * @return array
     */
    protected function arrayMessage(array $messages)
    {
        $result = [];

        foreach ($messages as $field => $rules) {
            if (false === strpos($field, '*')) {
                $result = array_merge(
                    $result,
                    $this->arrayMessageItem($field, $rules)
                );
            } else {
                $result = array_merge(
                    $result,
                    $this->wildcardMessageItem($field, $rules)
                );
            }
        }

        return $result;
    }

    /**
     * 分析通配符消息.
     *
     * @param string $field
     * @param mixed  $message
     *
     * @return array
     */
    protected function wildcardMessageItem($field, $message)
    {
        $field = $this->prepareRegexForWildcard($field);

        $messages = [];

        foreach ($this->parseDataKey() as $key) {
            if (preg_match($field, $key, $matche)) {
                $messages = array_merge(
                    $messages,
                    $this->arrayMessageItem($key, $message)
                );
            }
        }

        return $messages;
    }

    /**
     * 通配符正则.
     *
     * @param string $first
     * @param bool   $strict
     * @param mixed  $regex
     *
     * @return string
     */
    protected function prepareRegexForWildcard($regex, $strict = true)
    {
        return '/^'.
            str_replace(
                '6084fef57e91a6ecb13fff498f9275a7',
                '(\S+)',
                $this->escapeRegexCharacter(
                    str_replace('*', '6084fef57e91a6ecb13fff498f9275a7', $regex)
                )
            ).
            ($strict ? '$' : '').
            '/';
    }

    /**
     * 转义正则表达式特殊字符.
     *
     * @param string $txt
     *
     * @return string
     */
    protected function escapeRegexCharacter($txt)
    {
        $txt = str_replace([
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
            '|',
            '\\',
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
            '\\|',
            '\\\\',
        ], $txt);

        return $txt;
    }

    /**
     * 转换单条消息为数组.
     *
     * @param string       $field
     * @param array|string $message
     *
     * @return array
     */
    protected function arrayMessageItem($field, $message)
    {
        $result = [];

        if (is_array($message)) {
            foreach ($message as $key => $message) {
                $result[$field.'.'.$key] = $message;
            }
        } else {
            foreach ($this->getFieldRuleWithoutSkip($field) as $rule) {
                $result[$field.'.'.$rule] = $message;
            }
        }

        return $result;
    }

    /**
     * 分析验证规则和参数.
     *
     * @param string $rule
     *
     * @return array
     */
    protected function parseRule($rule)
    {
        $parameter = [];

        if (false !== strpos($rule, ':')) {
            list($rule, $parameter) = explode(':', $rule, 2);

            if (isset($this->alias[$rule])) {
                $rule = $this->alias[$rule];
            }

            $parameter = $this->parseParameters($rule, $parameter);
        }

        return [
            trim($rule),
            $parameter,
        ];
    }

    /**
     * 转换规则为数组.
     *
     * @param array $rules
     *
     * @return array
     */
    protected function arrayRule(array $rules)
    {
        $result = [];

        foreach ($rules as $field => $rules) {
            if (false === strpos($field, '*')) {
                $result[$field] = $this->arrayRuleItem($rules);
            } else {
                $result = array_merge(
                    $result,
                    $this->wildcardRuleItem($field, $rules)
                );
            }
        }

        return $result;
    }

    /**
     * 转换单条规则为数组.
     *
     *
     * @param mixed $rules
     *
     * @return array
     */
    protected function arrayRuleItem($rules)
    {
        return Arr::normalize($rules, '|');
    }

    /**
     * 分析通配符规则.
     *
     * @param string $field
     * @param mixed  $rules
     *
     * @return array
     */
    protected function wildcardRuleItem($field, $rules)
    {
        $field = $this->prepareRegexForWildcard($field);

        $rules = [];

        foreach ($this->parseDataKey() as $key) {
            if (preg_match($field, $key, $matche)) {
                $rules[$key] = $this->arrayRuleItem($rules);
            }
        }

        return $rules;
    }

    /**
     * 返回分析后的数据键.
     *
     * @return array
     */
    protected function parseDataKey()
    {
        if (null !== $this->parsedDataKey) {
            return $this->parsedDataKey;
        }

        $this->parsedDataKey = [];
        $this->parseDataKeyRecursion($this->getData());

        return $this->parsedDataKey;
    }

    /**
     * 清理分析数据键状态
     */
    protected function resetDataKey()
    {
        $this->parsedDataKey = null;
    }

    /**
     * 递归分析.
     *
     * @param array  $datas
     * @param string $parentKey
     */
    protected function parseDataKeyRecursion($datas, $parentKey = '')
    {
        foreach ($datas as $key => $datas) {
            $first = ($parentKey ? $parentKey.'.' : '').$key;

            if (is_array($datas)) {
                $this->parseDataKeyRecursion($datas, $first);
            } else {
                $this->parsedDataKey[] = $first;
            }
        }
    }

    /**
     * 是否存在单个字段验证规则
     * 不带条件的简单规则.
     *
     * @param string $field
     * @param string $rule
     *
     * @return $this
     */
    protected function hasFieldRuleWithoutParameter($field, $rule)
    {
        $result = $this->hasFieldRuleWithoutParameterReal($field, $rule);

        if (!$result && $rule === static::DEFAULT_CONDITION) {
            return !$this->hasFieldRuleWithoutParameterReal($field, [
                static::CONDITION_MUST,
                static::CONDITION_VALUE,
            ]);
        }

        return $result;
    }

    /**
     * 是否存在单个字段验证规则
     * 不带条件的简单规则.
     *
     * @param string $field
     * @param mixed  $rules
     * @param bool   $strict
     *
     * @return $this
     */
    protected function hasFieldRuleWithoutParameterReal($field, $rules, $strict = false)
    {
        if (!isset($this->rules[$field])) {
            return false;
        }

        $rules = (array) $rules;

        foreach ($rules as $rule) {
            if ($strict) {
                if (!in_array($rule, $this->rules[$field], true)) {
                    return false;
                }
            } elseif (in_array($rule, $this->rules[$field], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 解析变量.
     *
     * @param string $rule
     * @param string $parameter
     *
     * @return array
     */
    protected function parseParameters($rule, $parameter)
    {
        if ('regex' === strtolower($rule)) {
            return [
                $parameter,
            ];
        }

        return explode(',', $parameter);
    }

    /**
     * 验证字段规则.
     *
     * @param string $field
     * @param string $rule
     *
     * @return bool|void
     */
    protected function doValidateItem($field, $rule)
    {
        list($rule, $parameter) = $this->parseRule($rule);

        if ('' === $rule) {
            return;
        }

        $fieldValue = $this->getFieldValue($field);

        // 默认情况下存在即验证，没有设置字段则跳过
        if (!$this->hasFieldValue($field) &&
            $this->hasFieldRuleWithoutParameter($field, static::CONDITION_EXISTS)) {
            return;
        }

        // 值不为空就验证，那么为的空的值将会跳过
        if (empty($fieldValue) &&
            $this->hasFieldRuleWithoutParameter($field, static::CONDITION_VALUE)) {
            return;
        }

        if (!$this->{'validate'.ucwords(Str::camelize($rule))}(
            $field,
            $fieldValue,
            $parameter
        )) {
            $this->addFailure($field, $rule, $parameter);

            return false;
        }

        unset($fieldValue);

        return true;
    }

    /**
     * 是否需要终止其他验证
     *
     * @param string $field
     *
     * @return bool
     */
    protected function shouldSkipOther($field)
    {
        return $this->hasFieldRuleWithoutParameter($field, static::SKIP_OTHER);
    }

    /**
     * 是否需要终止自己其他验证
     *
     * @param string $field
     *
     * @return bool
     */
    protected function shouldSkipSelf($field)
    {
        return $this->hasFieldRuleWithoutParameter($field, static::SKIP_SELF);
    }

    /**
     * 添加错误规则和验证错误消息.
     *
     * @param string $field
     * @param string $rule
     * @param array  $parameter
     */
    protected function addFailure($field, $rule, $parameter)
    {
        $this->addError($field, $rule, $parameter);

        $this->failedRules[$field][$rule] = $parameter;
    }

    /**
     * 添加验证错误消息.
     *
     * @param string $field
     * @param string $rule
     * @param array  $parameter
     */
    protected function addError($field, $rule, $parameter)
    {
        $message = $this->getFieldRuleMessage($field, $rule);

        $replace = [
            'field' => $this->parseFieldName($field),
        ];

        if (!$this->isImplodeRuleParameter($rule)) {
            foreach ($parameter as $key => $parameter) {
                $replace['rule'.($key ?: '')] = $parameter;
            }
        } else {
            $replace['rule'] = implode(',', $parameter);
        }

        $message = preg_replace_callback('/{(.+?)}/', function ($matche) use ($replace) {
            return $replace[$matche[1]] ?? $matche[0];
        }, $message);

        $this->errorMessages[$field][] = $message;

        unset($replace, $message);
    }

    /**
     * 获取验证消息.
     *
     * @param string $field
     * @param string $rule
     *
     * @return string
     */
    protected function getFieldRuleMessage($field, $rule)
    {
        return $this->messages[$field.'.'.$rule] ??
            $this->messages[$rule] ??
            static::$defaultMessages[$rule] ??
            '';
    }

    /**
     * 获取字段名字.
     *
     * @param string $field
     *
     * @return string
     */
    protected function parseFieldName($field)
    {
        return $this->fieldName[$field] ?? $field;
    }

    /**
     * 获取字段的值
     *
     * @param string $rule
     *
     * @return mixed
     */
    protected function getFieldValue($rule)
    {
        if (false === strpos($rule, '.')) {
            if (isset($this->datas[$rule])) {
                return $this->datas[$rule];
            }
        } else {
            $rule = explode('.', $rule);

            $first = '$this->datas';

            for ($i = 0; $i < count($rule); $i++) {
                $first .= "['{$rule[$i]}']";
            }

            eval("\$first = ${first} ?? null;");

            return $first;
        }
    }

    /**
     * 是否存在字段的值
     *
     * @param string $rule
     *
     * @return bool
     */
    protected function hasFieldValue($rule)
    {
        return isset($this->datas[$rule]);
    }

    /**
     * 返回需要合并的规则参数.
     *
     * @param string $rule
     *
     * @return bool
     */
    protected function isImplodeRuleParameter($rule)
    {
        return in_array($rule, [
            'in',
            'not_in',
            'allow_ip',
            'deny_ip',
        ], true);
    }

    /**
     * 调用自定义验证器类.
     *
     * @param string $extend
     * @param array  $parameter
     *
     * @return bool
     */
    protected function callClasextend($extend, array $parameter)
    {
        if (!$this->container) {
            throw new Exception('Container has not set yet');
        }

        if (false === strpos($extend, '@')) {
            $className = $extend;
            $method = 'handle';
        } else {
            list($className, $method) = explode('@', $extend);
        }

        if (false === ($extend = $this->container->make($className))) {
            throw new InvalidArgumentException(
                sprintf('Extend class %s is not valid.', $className)
            );
        }

        $method = method_exists($extend, $method) ?
            $method :
            ('handle' !== $method &&
                method_exists($extend, 'handle') ?
                'handle' :
                'run');

        $parameter[] = $this;

        return call_user_func_array([
            $extend,
            $method,
        ], $parameter);
    }

    /**
     * 调用自定义验证器.
     *
     * @param string $rule
     * @param array  $parameter
     *
     * @return null|bool
     */
    protected function callExtend($rule, $parameter)
    {
        $extends = $this->extends[$rule];

        if (is_callable($extends)) {
            $parameter[] = $this;

            return call_user_func_array($extends, $parameter);
        }

        if (is_string($extends)) {
            return $this->callClasextend($extends, $parameter);
        }
    }

    /**
     * 验证条件是否通过.
     *
     * @param callable|mixed $calCallback
     * @param null|mixed     $callbacks
     *
     * @return bool
     */
    protected function isCallbackValid($callbacks = null)
    {
        $result = false;

        if (!is_string($callbacks) && is_callable($callbacks)) {
            $result = call_user_func($callbacks, $this->getData());
        } else {
            $result = $callbacks;
        }

        return $result;
    }
}

if (!function_exists('__')) {
    /**
     * lang.
     *
     * @param array $arr
     *
     * @return string
     */
    function __(...$arr)
    {
        return sprintf(...$arr);
    }
}
