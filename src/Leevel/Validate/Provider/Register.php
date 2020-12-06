<?php

declare(strict_types=1);

namespace Leevel\Validate\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Validate\IValidate;
use Leevel\Validate\Validate;

/**
 * validate 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->container
            ->singleton(
                'validate',
                fn (IContainer $container): Validate => new Validate($container),
            );
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'validate' => [Validate::class, IValidate::class],
        ];
    }

     /**
     * {@inheritDoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }
}
