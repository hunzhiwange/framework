<?php

declare(strict_types=1);

namespace Leevel\Auth\Provider;

use Leevel\Auth\Auth;
use Leevel\Auth\IAuth;
use Leevel\Auth\Manager;
use Leevel\Auth\Middleware\Auth as MiddlewareAuth;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * auth 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->auths();
        $this->auth();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'auths' => Manager::class,
            'auth'  => [IAuth::class, Auth::class],
            MiddlewareAuth::class,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 auths 服务.
     */
    protected function auths(): void
    {
        $this->container
            ->singleton(
                'auths',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 auth 服务.
     */
    protected function auth(): void
    {
        $this->container
            ->singleton(
                'auth',
                fn (IContainer $container): IAuth => $container['auths']->connect(),
            );
    }
}
