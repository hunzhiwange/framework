<?php

declare(strict_types=1);

namespace Leevel\Encryption\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Encryption\Encryption;
use Leevel\Encryption\IEncryption;

/**
 * 加密组件服务提供者.
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
                'encryption',
                function (IContainer $container): Encryption {
                    $config = $container['config'];

                    return new Encryption(
                        $config['auth_key'],
                        $config['auth_cipher'],
                        $config['auth_rsa_private'],
                        $config['auth_rsa_public']
                    );
                },
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'encryption' => [IEncryption::class, Encryption::class],
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
