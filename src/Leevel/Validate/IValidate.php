<?php

declare(strict_types=1);

namespace Leevel\Validate;

/**
 * 验证工厂接口.
 */
interface IValidate
{
    /**
     * 创建一个验证器.
     */
    public function make(array $data = [], array $rules = [], array $names = [], array $messages = []): IValidator;

    /**
     * 初始化默认验证消息.
     */
    public static function initMessages(): void;
}
