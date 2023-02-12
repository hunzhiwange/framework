<?php

declare(strict_types=1);

namespace Leevel\Validate\Proxy;

use Leevel\Di\Container;
use Leevel\Validate\Validate as BaseValidate;

/**
 * 代理 validate.
 *
 * @method static \Leevel\Validate\IValidator make(array $data = [], array $rules = [], array $names = [], array $messages = []) 创建一个验证器.
 * @method static void                        initMessages()                                                                     初始化默认验证消息.
 */
class Validate
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseValidate
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('validate');
    }
}
