<?php

declare(strict_types=1);

namespace Leevel\View\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\View\IView;
use Leevel\View\Manager;
use Leevel\View\View;

/**
 * view 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->views();
        $this->view();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'views' => Manager::class,
            'view'  => [IView::class, View::class],
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
     * 注册 views 服务.
     */
    protected function views(): void
    {
        $this->container
            ->singleton(
                'views',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 view 服务.
     */
    protected function view(): void
    {
        $this->container
            ->singleton(
                'view',
                fn (IContainer $container): IView => $container['views']->connect(),
            );
    }
}
