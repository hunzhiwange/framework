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

use Closure;
use Leevel\Di\IContainer;

/**
 * IValidator 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.26
 *
 * @version 1.0
 */
interface IValidator
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
     * 返回所有错误消息.
     *
     * @param \Closure $callback
     *
     * @return string
     */
    public function errorMessage(Closure $callback = null): string;

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
     * @param array         $rules
     * @param null|\Closure $calCallback
     *
     * @return $this
     */
    public function rule(array $rules, Closure $callbacks = null);

    /**
     * 添加验证规则.
     *
     * @param array         $rules
     * @param null|\Closure $calCallback
     *
     * @return $this
     */
    public function addRule(array $rules, Closure $callbacks = null);

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
     * @param string $name
     * @param string $alias
     *
     * @return $this
     */
    public function alias(string $name, string $alias);

    /**
     * 批量设置别名.
     *
     * @param array $alias
     *
     * @return $this
     */
    public function aliasMany(array $alias);

    /**
     * 设置验证后事件.
     *
     * @param \Closure $callbacks
     *
     * @return $this
     */
    public function after(Closure $callbacks);

    /**
     * 注册自定义扩展.
     *
     * @param string          $rule
     * @param \Closure|string $extends
     *
     * @return $this
     */
    public function extend($rule, $extends);

    /**
     * 设置 ioc 容器.
     *
     * @param \Leevel\Di\IContainer $container
     *
     * @return $this
     */
    public function setContainer(IContainer $container);

    /**
     * 初始化默认的消息.
     *
     * @param array $messages
     */
    public static function initMessages(array $messages): void;
}
