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

use Leevel\Di\IContainer;

/**
 * IValidate 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.26
 *
 * @version 1.0
 */
interface IValidate
{
    /**
     * 默认验证条件.
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
     * 失败后跳过.
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
     * 初始化验证器.
     *
     * @param array $datas
     * @param array $rules
     * @param array $names
     * @param array $message
     *
     * @return \Leevel\Validate
     */
    public static function make(array $datas = [], array $rules = [], array $names = [], array $message = []);

    /**
     * 验证是否成功
     *
     * @return bool
     */
    public function success(): bool;

    /**
     * 验证是否失败.
     *
     * @return bool
     */
    public function fail(): bool;

    /**
     * 返回所有错误消息.
     *
     * @return array
     */
    public function error(): array;

    /**
     * 返回验证数据.
     *
     * @return array
     */
    public function getData(): array;

    /**
     * 设置验证数据.
     *
     * @param array $datas
     *
     * @return $this
     */
    public function data(array $datas);

    /**
     * 添加验证数据.
     *
     * @param array $datas
     *
     * @return $this
     */
    public function addData(array $datas);

    /**
     * 返回验证规则.
     *
     * @return array
     */
    public function getRule();

    /**
     * 设置验证规则.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function rule(array $rules);

    /**
     * 设置验证规则,带上条件.
     *
     * @param array         $rules
     * @param null|callable $calCallback
     * @param mixed         $callbacks
     *
     * @return $this
     */
    public function ruleIf(array $rules, callable $callbacks = null);

    /**
     * 添加验证规则.
     *
     * @param array $rules
     *
     * @return $this
     */
    public function addRule(array $rules);

    /**
     * 添加验证规则,带上条件.
     *
     * @param array         $rules
     * @param null|callable $calCallback
     * @param mixed         $callbacks
     *
     * @return $this
     */
    public function addRuleIf(array $rules, callable $callbacks = null);

    /**
     * 返回验证消息.
     *
     * @return array
     */
    public function getMessage(): array;

    /**
     * 设置验证消息.
     *
     * @param array $message
     *
     * @return $this
     */
    public function message(array $message);

    /**
     * 添加验证消息.
     *
     * @param array $message
     *
     * @return $this
     */
    public function addMessage(array $message);

    /**
     * 添加字段验证消息.
     *
     * @param array $messages
     *
     * @return $this
     */
    public function messageWithField(array $messages);

    /**
     * 返回名字.
     *
     * @return array
     */
    public function getName(): array;

    /**
     * 设置名字.
     *
     * @param array $names
     *
     * @return $this
     */
    public function name(array $names);

    /**
     * 添加名字.
     *
     * @param array $names
     *
     * @return $this
     */
    public function addName(array $names);

    /**
     * 设置别名.
     *
     * @param strKey $alias
     * @param strKey $for
     *
     * @return $this
     */
    public function alias($alias, $for);

    /**
     * 批量设置别名.
     *
     * @param array $alias
     *
     * @return $this
     */
    public function aliasMany(array $alias);

    /**
     * 返回别名.
     *
     * @return array
     */
    public function getAlias();

    /**
     * 设置验证后事件.
     *
     * @param callable|string $callbacks
     *
     * @return $this
     */
    public function after($callbacks);

    /**
     * 返回所有验证后事件.
     *
     * @return array
     */
    public function getAfter();

    /**
     * 返回所有自定义扩展.
     *
     * @return array
     */
    public function getExtend();

    /**
     * 注册自定义扩展.
     *
     * @param string          $rule
     * @param callable|string $extends
     * @param mixed           $rule
     *
     * @return $this
     */
    public function extend($rule, $extends);

    /**
     * 批量注册自定义扩展.
     *
     * @param array $extends
     *
     * @return $this
     */
    public function extendMany(array $extends);

    /**
     * 设置 ioc 容器.
     *
     * @param \Leevel\Di\IContainer $container
     *
     * @return $this
     */
    public function container(IContainer $container);

    /**
     * 获取需要跳过的验证规则.
     *
     * @return array
     */
    public function getSkipRule();

    /**
     * 设置默认的消息.
     */
    public static function defaultMessage();
}
