<?php

declare(strict_types=1);

namespace Leevel\Validate;

use Leevel\Di\IContainer;
use Leevel\Support\Arr\Normalize;
use Leevel\Support\FlowControl;
use Leevel\Support\Str\Camelize;
use Leevel\Support\Str\UnCamelize;
use Leevel\Support\Type\StringDecode;

/**
 * 数据验证器.
 */
class Validator implements IValidator
{
    use FlowControl;

    /**
     * IOC 容器.
     */
    protected ?IContainer $container = null;

    /**
     * 待验证数据.
     */
    protected array $data = [];

    /**
     * 验证规则.
     */
    protected array $rules = [];

    /**
     * 默认验证提示信息.
     */
    protected static array $defaultMessages = [];

    /**
     * 验证提示信息.
     */
    protected array $messages = [];

    /**
     * 字段名字.
     */
    protected array $names = [];

    /**
     * 错误规则.
     */
    protected array $failedRules = [];

    /**
     * 错误消息.
     */
    protected array $errorMessages = [];

    /**
     * 需要跳过的验证规则.
     *
     * - 用于扩展属性支持
     */
    protected array $skipRule = [];

    /**
     * 扩展验证器.
     */
    protected array $extends = [];

    /**
     * 验证后续事件.
     */
    protected array $afters = [];

    /**
     * 构造函数.
     */
    final public function __construct(array $data = [], array $rules = [], array $names = [], array $messages = [])
    {
        $this->data($data);
        $this->rule($rules);
        $this->name($names);
        $this->message($messages);
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $args): mixed
    {
        if (str_starts_with($method, 'validate')) {
            $extend = UnCamelize::handle(substr($method, 8));
            if (isset($this->extends[$extend])) {
                return $this->callExtend($extend, $args);
            }
        }

        if (\count($args) > 0) {
            $sourceArgs = $args;
            $param = [''];
            $param[] = array_shift($args);
            $param[] = $args;
            unset($args);

            if (class_exists($helperClass = __NAMESPACE__.'\\Helper\\'.ucfirst($method))) {
                array_shift($param);

                return $helperClass::handle(...$param);
            }

            $extend = UnCamelize::handle($method);
            if (isset($this->extends[$extend])) {
                return $this->callExtend($extend, $param);
            }

            // 自定义静态验证器
            $customExtendClass = substr($method, 8);
            $customExtendMethod = 'handle';
            if (str_contains($customExtendClass, '@')) {
                [$customExtendClass, $customExtendMethod] = explode('@', $customExtendClass);
            }
            if (class_exists($customExtendClass) && \is_callable([$customExtendClass, $customExtendMethod])) {
                return $customExtendClass::$customExtendMethod(...$sourceArgs);
            }
        }

        $e = sprintf('Method %s is not exits.', $method);

        throw new \BadMethodCallException($e);
    }

    /**
     * {@inheritDoc}
     */
    public static function make(array $data = [], array $rules = [], array $names = [], array $messages = []): static
    {
        return new static($data, $rules, $names, $messages);
    }

    /**
     * {@inheritDoc}
     */
    public function success(): bool
    {
        $skipRule = $this->getSkipRule();
        $this->failedRules = $this->errorMessages = [];

        foreach ($this->rules as $field => $rules) {
            foreach ($rules as $rule) {
                if (\in_array($rule[0], $skipRule, true)) {
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

        return 0 === \count($this->errorMessages);
    }

    /**
     * {@inheritDoc}
     */
    public function fail(): bool
    {
        return !$this->success();
    }

    /**
     * {@inheritDoc}
     */
    public function error(): array
    {
        return $this->errorMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getRule(): array
    {
        return $this->rules;
    }

    /**
     * {@inheritDoc}
     */
    public function rule(array $rules, ?\Closure $callbacks = null): IValidator
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
     * {@inheritDoc}
     */
    public function addRule(array $rules, ?\Closure $callbacks = null): IValidator
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
     * {@inheritDoc}
     */
    public function getMessage(): array
    {
        return $this->messages;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function getName(): array
    {
        return $this->names;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function after(\Closure $callbacks): IValidator
    {
        $this->afters[] = function () use ($callbacks) {
            return $callbacks($this);
        };

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function extend(string $rule, \Closure|string $extends): IValidator
    {
        $this->extends[strtolower($rule)] = $extends;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setContainer(IContainer $container): IValidator
    {
        $this->container = $container;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public static function initMessages(array $messages): void
    {
        static::$defaultMessages = $messages;
    }

    /**
     * {@inheritDoc}
     */
    public function getParseRule(string $field, array|string $rules): array
    {
        $rules = (array) $rules;
        foreach ($this->rules[$field] as $rule) {
            [$rule, $param] = $this->parseRule($rule);
            if (\in_array($rule, $rules, true)) {
                return [$rule, $param];
            }
        }

        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getFieldValue(string $rule): mixed
    {
        if (!str_contains($rule, '.')) {
            return $this->data[$rule] ?? null;
        }
        $parts = explode('.', $rule);
        $data = $this->data;
        foreach ($parts as $part) {
            if (!isset($data[$part])) {
                return null;
            }
            $data = $data[$part];
        }

        return $data;
    }

    /**
     * 转换消息为数组.
     */
    protected function arrayMessage(array $messages): array
    {
        $result = [];
        foreach ($messages as $field => $message) {
            // 字段消息或者通配符
            // ['name' => ['required' => '{field} required']]
            // ['na*' => 'foo bar']
            if (\is_array($message) || str_contains($field, '*')) {
                if (!str_contains($field, '*')) {
                    $result = array_merge(
                        $result,
                        $this->arrayMessageItem($field, $message)
                    );
                } else {
                    $result = array_merge(
                        $result,
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
     */
    protected function wildcardMessageItem(string $field, mixed $message): array
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
     */
    protected function prepareRegexForWildcard(string $regex, bool $strict = true): string
    {
        $regex = preg_quote($regex, '/');

        return '/^'.str_replace('\*', '(\S+)', $regex).($strict ? '$' : '').'/';
    }

    /**
     * 转换单条消息为数组.
     */
    protected function arrayMessageItem(string $field, array|string $message): array
    {
        $result = [];
        if (\is_array($message)) {
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
     */
    protected function getFieldRuleWithoutSkip(string $field): array
    {
        return array_diff(array_column($this->getFieldRule($field), 0), $this->getSkipRule());
    }

    /**
     * 获取需要跳过的验证规则.
     */
    protected function getSkipRule(): array
    {
        return array_merge([
            static::OPTIONAL,
            static::MUST,
            static::SKIP_SELF,
            static::SKIP_OTHER,
        ], $this->skipRule);
    }

    /**
     * 获取单个字段验证规则.
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
     */
    protected function parseRule(string|array $rule): array
    {
        [$rule, $params] = array_pad(\is_array($rule) ? $rule : explode(':', $rule, 2), 2, []);
        if (\is_string($params)) {
            $params = $this->parseParams($rule, $params);
        } elseif (!\is_array($params)) {
            $params = [$params];
        }
        $params = array_map(fn (string $item) => StringDecode::handle($item), $params);

        return [$rule, $params];
    }

    /**
     * 转换规则为数组.
     */
    protected function arrayRule(array $rules): array
    {
        $result = [];
        foreach ($rules as $field => $rules) {
            if (!str_contains($field, '*')) {
                $result[$field] = $this->arrayRuleItem($rules);
            } else {
                $result = array_merge($result, $this->wildcardRuleItem($field, $rules));
            }
        }

        return $result;
    }

    /**
     * 转换单条规则为数组.
     */
    protected function arrayRuleItem(string|array $rules): array
    {
        $parsedRules = [];
        foreach ((array) Normalize::handle($rules, '|') as $item) {
            if (\is_string($item)) {
                $parsedRules = array_merge($parsedRules, (array) Normalize::handle($item, '|'));
            } else {
                $parsedRules[] = $item;
            }
        }

        foreach ($parsedRules as &$v) {
            $v = $this->parseRule($v);
        }

        return $parsedRules;
    }

    /**
     * 分析通配符规则.
     */
    protected function wildcardRuleItem(string $field, mixed $rules): array
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
     */
    protected function parseDataKey(): array
    {
        return $this->parseDataKeyRecursion($this->getData());
    }

    /**
     * 递归分析.
     */
    protected function parseDataKeyRecursion(array $data, string $parentKey = ''): array
    {
        $dataKeys = [];
        foreach ($data as $key => $d) {
            $first = ($parentKey ? $parentKey.'.' : '').$key;
            if (\is_array($d)) {
                $dataKeys = array_merge($dataKeys, $this->parseDataKeyRecursion($d, $first));
            } else {
                $dataKeys[] = $first;
            }
        }

        return $dataKeys;
    }

    /**
     * 是否存在单个字段验证规则.
     */
    protected function hasFieldRuleWithParam(string $field, string $rule): bool
    {
        if (!isset($this->rules[$field])) {
            return false;
        }

        return \in_array($rule, array_column($this->rules[$field], 0), true);
    }

    /**
     * 解析变量.
     */
    protected function parseParams(string $rule, string $param): array
    {
        if ('regex' === strtolower($rule)) {
            return [$param];
        }

        return explode(',', $param);
    }

    /**
     * 验证字段规则.
     *
     * @throws \Exception
     */
    protected function doValidateItem(string $field, array $rule): bool
    {
        [$rule, $param] = $rule;
        if ('' === $rule) {
            return true;
        }

        $fieldValue = $this->getFieldValue($field);

        // 可选字段无需验证
        if (null === $fieldValue
            && $this->hasFieldRuleWithParam($field, static::OPTIONAL)) {
            return true;
        }

        // 反义规则
        $isNotRule = str_starts_with($rule, '!');
        if ($isNotRule) {
            $rule = substr($rule, 1);
        }

        $camelizeRule = ucfirst(Camelize::handle($rule));

        if (class_exists($classHelper = __NAMESPACE__.'\\Helper\\'.$camelizeRule)) {
            $validateResult = $classHelper::handle($fieldValue, $param, $this, $field);
            if ($isNotRule) {
                $validateResult = !$validateResult;
            }
            if (!$validateResult) {
                $this->addFailure($field, $rule, $param, $isNotRule);

                return false;
            }
        } elseif (class_exists($className = __NAMESPACE__.'\\'.$camelizeRule.'Rule')) {
            if ($this->container) {
                $validateRule = $this->container->make($className);
            } else {
                $validateRule = new $className();
            }
            if (!\is_callable([$validateRule, 'handle'])) {
                // @phpstan-ignore-next-line
                throw new \Exception(sprintf('Validate rule %s is invalid.', $validateRule::class));
            }

            /** @phpstan-ignore-next-line */
            $validateResult = $validateRule->handle($fieldValue, $param, $this, $field);
            if ($isNotRule) {
                $validateResult = !$validateResult;
            }
            if (false === $validateResult) {
                $this->addFailure($field, $rule, $param, $isNotRule);

                return false;
            }
        } else {
            $validateResult = $this->{'validate'.$camelizeRule}($fieldValue, $param, $this, $field);
            if ($isNotRule) {
                $validateResult = !$validateResult;
            }
            if (!$validateResult) {
                $this->addFailure($field, $rule, $param, $isNotRule);

                return false;
            }
        }

        return true;
    }

    /**
     * 是否需要终止其他验证.
     */
    protected function shouldSkipOther(string $field): bool
    {
        return $this->hasFieldRuleWithParam($field, static::SKIP_OTHER);
    }

    /**
     * 是否需要终止自己其他验证.
     */
    protected function shouldSkipSelf(string $field): bool
    {
        return $this->hasFieldRuleWithParam($field, static::SKIP_SELF);
    }

    /**
     * 添加错误规则和验证错误消息.
     */
    protected function addFailure(string $field, string $rule, array $param, bool $isNotRule = false): void
    {
        $this->addError($field, $rule, $param, $isNotRule);
        $this->failedRules[$field][$rule] = $param;
    }

    /**
     * 添加验证错误消息.
     */
    protected function addError(string $field, string $rule, array $param, bool $isNotRule = false): void
    {
        $message = $this->getFieldRuleMessage($field, $rule);
        $replace = ['field' => $this->parseFieldName($field)];

        if ($this->isImplodeRuleParam($rule)) {
            $replace['rule'] = implode(',', $param);
        } elseif ('equal_to' === $rule) {
            $replace['rule'] = $this->parseFieldName($param[0] ?? '');
        } else {
            foreach ($param as $key => $param) {
                $replace['rule'.($key ?: '')] = $param;
            }
        }

        $message = preg_replace_callback('/{(.+?)}/', function ($matches) use ($replace) {
            return $replace[$matches[1]] ?? $matches[0];
        }, $message);

        if ($isNotRule) {
            $message = sprintf('不满足【%s】的反义规则', $message);
        }

        $this->errorMessages[$field][] = $message;
    }

    /**
     * 获取验证消息.
     */
    protected function getFieldRuleMessage(string $field, string $rule): string
    {
        return $this->messages[$field.'.'.$rule] ??
            ($this->messages[$rule] ?? (static::$defaultMessages[$rule] ?? ''));
    }

    /**
     * 获取字段名字.
     */
    protected function parseFieldName(string $field): string
    {
        return $this->names[$field] ?? $field;
    }

    /**
     * 返回需要合并的规则参数.
     */
    protected function isImplodeRuleParam(string $rule): bool
    {
        return \in_array($rule, ['in', 'not_in', 'allow_ip', 'deny_ip'], true);
    }

    /**
     * 调用自定义验证器类.
     *
     * @throws \InvalidArgumentException
     */
    protected function callClassExtend(string $extend, array $param): bool
    {
        if (!$this->container) {
            $e = 'Container was not set.';

            throw new \InvalidArgumentException($e);
        }

        if (!str_contains($extend, '@')) {
            $className = $extend;
            $method = 'handle';
        } else {
            [$className, $method] = explode('@', $extend);
        }

        // @phpstan-ignore-next-line
        if (!\is_object($extend = $this->container->make($className))) {
            $e = sprintf('Extend class %s is not valid.', $className);

            throw new \InvalidArgumentException($e);
        }

        // @phpstan-ignore-next-line
        $param[] = $this;

        return $extend->{$method}(...$param);
    }

    /**
     * 调用自定义验证器.
     *
     * @throws \InvalidArgumentException
     */
    protected function callExtend(string $rule, array $param): bool
    {
        $extends = $this->extends[$rule];
        if (\is_callable($extends)) {
            $param[] = $this;

            return $extends(...$param);
        }

        if (\is_string($extends)) {
            return $this->callClassExtend($extends, $param);
        }

        $e = sprintf('Extend in rule %s is not valid.', $rule);

        throw new \InvalidArgumentException($e);
    }

    /**
     * 验证条件是否通过.
     */
    protected function isCallbackValid(\Closure $callbacks): bool
    {
        return $callbacks($this->getData());
    }
}
