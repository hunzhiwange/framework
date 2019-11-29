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

namespace Leevel\Validate\Proxy;

use Leevel\Di\Container;
use Leevel\Validate\IValidator;
use Leevel\Validate\Validate as BaseValidate;

/**
 * 代理 validate.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.26
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Validate
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 创建一个验证器.
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
        return self::proxy()->make($data, $rules, $names, $messages);
    }

    /**
     * 初始化默认验证消息.
     */
    public static function initMessages(): void
    {
        self::proxy()->initMessages();
    }

    /**
     * 代理服务.
     *
     * @return \Leevel\Validate\Validate
     */
    public static function proxy(): BaseValidate
    {
        return Container::singletons()->make('validate');
    }
}
