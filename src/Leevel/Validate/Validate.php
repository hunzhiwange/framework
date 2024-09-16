<?php

declare(strict_types=1);

namespace Leevel\Validate;

use Leevel\Di\IContainer;

/**
 * 验证工厂.
 */
class Validate implements IValidate
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 构造函数.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
        $this->initMessages();
    }

    public function make(array $data = [], array $rules = [], array $names = [], array $messages = []): IValidator
    {
        $validator = new Validator($data, $rules, $names, $messages);
        $validator->setContainer($this->container);

        return $validator;
    }

    public static function initMessages(): void
    {
        Validator::initMessages([]);
    }
}
