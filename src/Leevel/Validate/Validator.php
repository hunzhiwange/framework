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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Validate;

use BadMethodCallException;
use Closure;
use InvalidArgumentException;
use Leevel\Di\IContainer;
use Leevel\Flow\FlowControl;
use function Leevel\Support\Arr\normalize;
use Leevel\Support\Arr\normalize;
use function Leevel\Support\Str\camelize;
use Leevel\Support\Str\camelize;
use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * Validator 数据验证器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.02
 *
 * @version 1.0
 */
class Validator implements IValidator
{
    use FlowControl;

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
    protected $data = [];

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
    protected $names = [];

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
     * @param array $data
     * @param array $rules
     * @param array $names
     * @param array $messages
     */
    public function __construct(array $data = [], array $rules = [], array $names = [], array $messages = [])
    {
        $this->data($data);
        $this->rule($rules);
        $this->name($names);
        $this->message($messages);
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if ($this->placeholderFlowControl($method)) {
            return $this;
        }

        if (0 === strpos($method, 'validate')) {
            $extend = un_camelize(substr($method, 8));

            if (isset($this->extends[$extend])) {
                return $this->callExtend($extend, $args);
            }
        }

        if (count($args) > 0) {
            $extend = 'validate'.ucfirst($method);

            $parameter = [''];
            $parameter[] = array_shift($args);
            $parameter[] = $args;
            unset($args);

            if (class_exists($fn = __NAMESPACE__.'\\Helper\\validate_'.un_camelize($method))) {
                array_shift($parameter);

                return $fn(...$parameter);
            }

            $extend = un_camelize($method);

            if (isset($this->extends[$extend])) {
                return $this->callExtend($extend, $parameter);
            }
        }

        $e = sprintf('Method %s is not exits.', $method);

        throw new BadMethodCallException($e);
    }

    /**
     * 初始化验证器.
     *
     * @param array $data
     * @param array $rules
     * @param array $names
     * @param array $messages
     *
     * @return \Leevel\Validate\IValidator
     */
    public static function make(array $data = [], array $rules = [], array $names = [], array $messages = []): IValidator
    {
        return new static($data, $rules, $names, $messages);
    }

    /**
     * 验证是否成功
     *
     * @return bool
     */
    public function success(): bool
    {
        $skipRule = $this->getSkipRule();

        $this->failedRules = $this->errorMessages = [];

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

        foreach ($this->afters as $after) {
            $after();
        }

        return 0 === count($this->errorMessages);
    }

    /**
     * 验证是否失败.
     *
     * @return bool
     */
    public function fail(): bool
    {
        return !$this->success();
    }

    /**
     * 返回所有错误消息.
     *
     * @return array
     */
    public function error(): array
    {
        return $this->errorMessages;
    }

    /**
     * 返回验证数据.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 设置验证数据.
     *
     * @param array $data
     *
     * @return \Leevel\Validate\IValidator
     */
    public function data(array $data): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->data = $data;

        return $this;
    }

    /**
     * 添加验证数据.
     *
     * @param array $data
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addData(array $data): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * 返回验证规则.
     *
     * @return array
     */
    public function getRule(): array
    {
        return $this->rules;
    }

    /**
     * 设置验证规则.
     *
     * @param array         $rules
     * @param null|\Closure $calCallback
     *
     * @return \Leevel\Validate\IValidator
     */
    public function rule(array $rules, ?Closure $callbacks = null): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $callbacks || $this->isCallbackValid($callbacks)) {
            $this->rules = $this->arrayRule($rules);
        }

        return $this;
    }

    /**
     * 添加验证规则.
     *
     * @param array         $rules
     * @param null|\Closure $calCallback
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addRule(array $rules, ?Closure $callbacks = null): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $callbacks || $this->isCallbackValid($callbacks)) {
            $this->rules = array_merge($this->rules, $this->arrayRule($rules));
        }

        return $this;
    }

    /**
     * 返回验证消息.
     *
     * @return array
     */
    public function getMessage(): array
    {
        return $this->messages;
    }

    /**
     * 设置验证消息.
     *
     * @param array $messages
     *
     * @return \Leevel\Validate\IValidator
     */
    public function message(array $messages): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->messages = $this->arrayMessage($messages);

        return $this;
    }

    /**
     * 添加验证消息.
     *
     * @param array $messages
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addMessage(array $messages): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->messages = array_merge(
            $this->messages,
            $this->arrayMessage($messages)
        );

        return $this;
    }

    /**
     * 返回名字.
     *
     * @return array
     */
    public function getName(): array
    {
        return $this->names;
    }

    /**
     * 设置名字.
     *
     * @param array $names
     *
     * @return \Leevel\Validate\IValidator
     */
    public function name(array $names): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->names = $names;

        return $this;
    }

    /**
     * 添加名字.
     *
     * @param array $names
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addName(array $names): IValidator
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->names = array_merge($this->names, $names);

        return $this;
    }

    /**
     * 设置别名.
     *
     * @param string $name
     * @param string $alias
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Validate\IValidator
     */
    public function alias(string $name, string $alias): IValidator
    {
        if (in_array($name, $this->getSkipRule(), true)) {
            $e = sprintf('You cannot set alias for skip rule %s.', $name);

            throw new InvalidArgumentException($e);
        }

        $this->alias[$alias] = $name;

        return $this;
    }

    /**
     * 批量设置别名.
     *
     * @param array $alias
     *
     * @return \Leevel\Validate\IValidator
     */
    public function aliasMany(array $alias): IValidator
    {
        foreach ($alias as $alias => $value) {
            $this->alias($alias, $value);
        }

        return $this;
    }

    /**
     * 设置验证后事件.
     *
     * @param \Closure $callbacks
     *
     * @return \Leevel\Validate\IValidator
     */
    public function after(Closure $callbacks): IValidator
    {
        $this->afters[] = function () use ($callbacks) {
            return $callbacks($this);
        };

        return $this;
    }

    /**
     * 注册自定义扩展.
     *
     * @param string          $rule
     * @param \Closure|string $extends
     *
     * @return \Leevel\Validate\IValidator
     */
    public function extend(string $rule, $extends): IValidator
    {
        $this->extends[strtolower($rule)] = $extends;

        return $this;
    }

    /**
     * 设置 IOC 容器.
     *
     * @param \Leevel\Di\IContainer $container
     *
     * @return \Leevel\Validate\IValidator
     */
    public function setContainer(IContainer $container): IValidator
    {
        $this->container = $container;

        return $this;
    }

    /**
     * 初始化默认的消息.
     *
     * @param array $messages
     */
    public static function initMessages(array $messages): void
    {
        static::$defaultMessages = $messages;
    }

    /**
     * 尝试读取格式化条件.
     *
     * @param string       $field
     * @param array|string $rules
     *
     * @return array
     */
    public function getParseRule(string $field, $rules): array
    {
        $rules = (array) $rules;

        foreach ($this->rules[$field] as $rule) {
            list($rule, $parameter) = $this->parseRule($rule);

            if (in_array($rule, $rules, true)) {
                return [$rule, $parameter];
            }
        }

        return [];
    }

    /**
     * 获取字段的值
     *
     * @param string $rule
     *
     * @return mixed
     */
    public function getFieldValue(string $rule)
    {
        if (false === strpos($rule, '.')) {
            if (isset($this->data[$rule])) {
                return $this->data[$rule];
            }
        } else {
            $parts = explode('.', $rule);
            $data = $this->data;

            foreach ($parts as $part) {
                if (!isset($data[$part])) {
                    return;
                }

                $data = $data[$part];
            }

            return $data;
        }
    }

    /**
     * 转换消息为数组.
     *
     * @param array $messages
     *
     * @return array
     */
    protected function arrayMessage(array $messages): array
    {
        $result = [];

        foreach ($messages as $field => $message) {
            // 字段消息或者通配符
            // ['name' => ['required' => '{field} required']]
            // ['na*' => 'foo bar']
            if (is_array($message) || false !== strpos($field, '*')) {
                if (false === strpos($field, '*')) {
                    $result = array_merge($result,
                        $this->arrayMessageItem($field, $message)
                    );
                } else {
                    $result = array_merge($result,
                        $this->wildcardMessageItem($field, $message)
                    );
                }
            }

            // 直接消息
            // ['required' => '{field} required']
            // ['name.required' => '{field} required']
            else {
                $result[$field] = $message;
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
    protected function wildcardMessageItem(string $field, $message): array
    {
        $field = $this->prepareRegexForWildcard($field);

        $messages = [];

        foreach ($this->parseDataKey() as $key) {
            if (preg_match($field, $key, $matche)) {
                $messages = array_merge($messages,
                    $this->arrayMessageItem($key, $message)
                );
            }
        }

        return $messages;
    }

    /**
     * 通配符正则.
     *
     * @param string $regex
     * @param bool   $strict
     *
     * @return string
     */
    protected function prepareRegexForWildcard(string $regex, bool $strict = true): string
    {
        $regex = preg_quote($regex, '/');
        $regex = '/^'.str_replace('\*', '(\S+)', $regex).($strict ? '$' : '').'/';

        return $regex;
    }

    /**
     * 转换单条消息为数组.
     *
     * @param string       $field
     * @param array|string $message
     *
     * @return array
     */
    protected function arrayMessageItem(string $field, $message): array
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
     * 获取单个字段验证规则，排除掉绕过的规则.
     *
     * @param string $field
     *
     * @return array
     */
    protected function getFieldRuleWithoutSkip(string $field): array
    {
        return array_diff($this->getFieldRule($field), $this->getSkipRule());
    }

    /**
     * 获取需要跳过的验证规则.
     *
     * @return array
     */
    protected function getSkipRule(): array
    {
        return array_merge([
            static::CONDITION_OPTIONAL,
            static::CONDITION_MUST,
            static::SKIP_SELF,
            static::SKIP_OTHER,
        ], $this->skipRule);
    }

    /**
     * 获取单个字段验证规则.
     *
     * @param string $field
     *
     * @return array
     */
    protected function getFieldRule(string $field): array
    {
        if (isset($this->rules[$field])) {
            return $this->rules[$field];
        }

        return [];
    }

    /**
     * 分析验证规则和参数.
     *
     * @param string $rule
     *
     * @return array
     */
    protected function parseRule(string $rule): array
    {
        $parameter = [];

        if (false !== strpos($rule, ':')) {
            list($rule, $parameter) = explode(':', $rule, 2);

            if (isset($this->alias[$rule])) {
                $rule = $this->alias[$rule];
            }

            $parameter = $this->parseParameters($rule, $parameter);
        }

        return [trim($rule), $parameter];
    }

    /**
     * 转换规则为数组.
     *
     * @param array $rules
     *
     * @return array
     */
    protected function arrayRule(array $rules): array
    {
        $result = [];

        foreach ($rules as $field => $rules) {
            if (false === strpos($field, '*')) {
                $result[$field] = $this->arrayRuleItem($rules);
            } else {
                $result = array_merge($result, $this->wildcardRuleItem($field, $rules));
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
    protected function arrayRuleItem($rules): array
    {
        return normalize($rules, '|');
    }

    /**
     * 分析通配符规则.
     *
     * @param string $field
     * @param mixed  $rules
     *
     * @return array
     */
    protected function wildcardRuleItem(string $field, $rules): array
    {
        $field = $this->prepareRegexForWildcard($field);

        $result = [];

        foreach ($this->parseDataKey() as $key) {
            if (preg_match($field, $key, $matche)) {
                $result[$key] = $this->arrayRuleItem($rules);
            }
        }

        return $result;
    }

    /**
     * 返回分析后的数据键.
     *
     * @return array
     */
    protected function parseDataKey(): array
    {
        return $this->parseDataKeyRecursion($this->getData());
    }

    /**
     * 递归分析.
     *
     * @param array  $data
     * @param string $parentKey
     *
     * @return array
     */
    protected function parseDataKeyRecursion(array $data, string $parentKey = ''): array
    {
        $dataKeys = [];

        foreach ($data as $key => $d) {
            $first = ($parentKey ? $parentKey.'.' : '').$key;

            if (is_array($d)) {
                $dataKeys = array_merge($dataKeys, $this->parseDataKeyRecursion($d, $first));
            } else {
                $dataKeys[] = $first;
            }
        }

        return $dataKeys;
    }

    /**
     * 是否存在单个字段验证规则.
     *
     * @param string $field
     * @param array  $rules
     *
     * @return bool
     */
    protected function hasFieldRuleWithParameter(string $field, string $rule): bool
    {
        if (!isset($this->rules[$field])) {
            return false;
        }

        return in_array($rule, $this->rules[$field], true);
    }

    /**
     * 解析变量.
     *
     * @param string $rule
     * @param string $parameter
     *
     * @return array
     */
    protected function parseParameters(string $rule, string $parameter): array
    {
        if ('regex' === strtolower($rule)) {
            return [$parameter];
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
    protected function doValidateItem(string $field, string $rule)
    {
        list($rule, $parameter) = $this->parseRule($rule);

        if ('' === $rule) {
            return;
        }

        $fieldValue = $this->getFieldValue($field);

        // 可选字段无需验证
        if (!$this->hasFieldValue($field) &&
            $this->hasFieldRuleWithParameter($field, static::CONDITION_OPTIONAL)) {
            return;
        }

        if (class_exists($fn = __NAMESPACE__.'\\Helper\\validate_'.$rule)) {
            if (!$fn($fieldValue, $parameter, $this, $field)) {
                $this->addFailure($field, $rule, $parameter);

                return false;
            }
        } elseif (class_exists($className = __NAMESPACE__.'\\'.($camelizeRule = ucwords(camelize($rule))).'Rule')) {
            if ($this->container) {
                $validateRule = $this->container->make($className);
            } else {
                $validateRule = new $className();
            }

            if (false === $validateRule->validate($fieldValue, $parameter, $this, $field)) {
                $this->addFailure($field, $rule, $parameter);

                return false;
            }
        } elseif (!$this->{'validate'.$camelizeRule}($fieldValue, $parameter, $this, $field)) {
            $this->addFailure($field, $rule, $parameter);

            return false;
        }

        return true;
    }

    /**
     * 是否需要终止其他验证
     *
     * @param string $field
     *
     * @return bool
     */
    protected function shouldSkipOther(string $field): bool
    {
        return $this->hasFieldRuleWithParameter($field, static::SKIP_OTHER);
    }

    /**
     * 是否需要终止自己其他验证
     *
     * @param string $field
     *
     * @return bool
     */
    protected function shouldSkipSelf(string $field): bool
    {
        return $this->hasFieldRuleWithParameter($field, static::SKIP_SELF);
    }

    /**
     * 添加错误规则和验证错误消息.
     *
     * @param string $field
     * @param string $rule
     * @param array  $parameter
     */
    protected function addFailure(string $field, string $rule, array $parameter): void
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
    protected function addError(string $field, string $rule, array $parameter): void
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
    }

    /**
     * 获取验证消息.
     *
     * @param string $field
     * @param string $rule
     *
     * @return string
     */
    protected function getFieldRuleMessage(string $field, string $rule): string
    {
        return $this->messages[$field.'.'.$rule] ??
            ($this->messages[$rule] ?? (static::$defaultMessages[$rule] ?? ''));
    }

    /**
     * 获取字段名字.
     *
     * @param string $field
     *
     * @return string
     */
    protected function parseFieldName(string $field): string
    {
        return $this->names[$field] ?? $field;
    }

    /**
     * 是否存在字段的值
     *
     * @param string $rule
     *
     * @return bool
     */
    protected function hasFieldValue(string $rule): bool
    {
        return array_key_exists($rule, $this->data);
    }

    /**
     * 返回需要合并的规则参数.
     *
     * @param string $rule
     *
     * @return bool
     */
    protected function isImplodeRuleParameter(string $rule): bool
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
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected function callClassExtend(string $extend, array $parameter): bool
    {
        if (!$this->container) {
            $e = 'Container was not set.';

            throw new InvalidArgumentException($e);
        }

        if (false === strpos($extend, '@')) {
            $className = $extend;
            $method = 'handle';
        } else {
            list($className, $method) = explode('@', $extend);
        }

        if (!is_object($extend = $this->container->make($className))) {
            $e = sprintf('Extend class %s is not valid.', $className);

            throw new InvalidArgumentException($e);
        }

        $parameter[] = $this;

        return $extend->{$method}(...$parameter);
    }

    /**
     * 调用自定义验证器.
     *
     * @param string $rule
     * @param array  $parameter
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected function callExtend(string $rule, array $parameter): bool
    {
        $extends = $this->extends[$rule];

        if (is_callable($extends)) {
            $parameter[] = $this;

            return $extends(...$parameter);
        }

        if (is_string($extends)) {
            return $this->callClassExtend($extends, $parameter);
        }

        $e = sprintf('Extend in rule %s is not valid.', $rule);

        throw new InvalidArgumentException($e);
    }

    /**
     * 验证条件是否通过.
     *
     * @param \Closure $calCallback
     *
     * @return bool
     */
    protected function isCallbackValid(Closure $callbacks): bool
    {
        return $callbacks($this->getData());
    }
}

// import fn.
class_exists(normalize::class);
class_exists(un_camelize::class);
class_exists(camelize::class);
