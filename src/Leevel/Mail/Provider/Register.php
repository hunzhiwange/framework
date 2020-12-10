<?php

declare(strict_types=1);

namespace Leevel\Mail\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Mail\IMail;
use Leevel\Mail\Mail;
use Leevel\Mail\Manager;

/**
 * 邮件服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->mails();
        $this->mail();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'mails' => Manager::class,
            'mail'  => [IMail::class, Mail::class],
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
     * 注册 mails 服务.
     */
    protected function mails(): void
    {
        $this->container
            ->singleton(
                'mails',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 mail 服务.
     */
    protected function mail(): void
    {
        $this->container
            ->singleton(
                'mail',
                fn (IContainer $container): IMail => $container['mails']->connect(),
            );
    }
}
