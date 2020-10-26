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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Validate;

use Closure;
use Leevel\Di\IContainer;

/**
 * IValidator 接口.
 */
interface IValidator
{
    /**
     * 可选字段.
     *
     * @var string
     */
    const OPTIONAL = 'optional';

    /**
     * 无论是否是可选字段都验证.
     *
     * @var string
     */
    const MUST = 'must';

    /**
     * 失败后跳过.
     *
     * @var string
     */
    const SKIP_SELF = 'self';

    /**
     * 跳过其它验证.
     *
     * @var string
     */
    const SKIP_OTHER = 'other';

    /**
     * 初始化验证器.
     *
     * @return \Leevel\Validate\IValidator
     */
    public static function make(array $data = [], array $rules = [], array $names = [], array $message = []): self;

    /**
     * 验证是否成功.
     */
    public function success(): bool;

    /**
     * 验证是否失败.
     */
    public function fail(): bool;

    /**
     * 返回所有错误消息.
     */
    public function error(): array;

    /**
     * 返回验证数据.
     */
    public function getData(): array;

    /**
     * 设置验证数据.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function data(array $data): self;

    /**
     * 添加验证数据.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addData(array $data): self;

    /**
     * 返回验证规则.
     *
     * @return array
     */
    public function getRule();

    /**
     * 设置验证规则.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function rule(array $rules, ?Closure $callbacks = null): self;

    /**
     * 添加验证规则.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addRule(array $rules, ?Closure $callbacks = null): self;

    /**
     * 返回验证消息.
     */
    public function getMessage(): array;

    /**
     * 设置验证消息.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function message(array $message): self;

    /**
     * 添加验证消息.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addMessage(array $message): self;

    /**
     * 返回名字.
     */
    public function getName(): array;

    /**
     * 设置名字.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function name(array $names): self;

    /**
     * 添加名字.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function addName(array $names): self;

    /**
     * 设置别名.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function alias(string $name, string $alias): self;

    /**
     * 批量设置别名.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function aliasMany(array $alias): self;

    /**
     * 设置验证后事件.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function after(Closure $callbacks): self;

    /**
     * 注册自定义扩展.
     *
     * @param \Closure|string $extends
     *
     * @return \Leevel\Validate\IValidator
     */
    public function extend(string $rule, Closure|string $extends): self;

    /**
     * 设置 IOC 容器.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function setContainer(IContainer $container): self;

    /**
     * 初始化默认的消息.
     */
    public static function initMessages(array $messages): void;

    /**
     * 尝试读取格式化条件.
     *
     * @param array|string $rules
     */
    public function getParseRule(string $field, array|string $rules): array;

    /**
     * 获取字段的值.
     *
     * @return mixed
     */
    public function getFieldValue(string $rule): mixed;
}
