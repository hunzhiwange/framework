<?php

declare(strict_types=1);

namespace Leevel\Auth;

use Leevel\Session\ISession;

/**
 * Auth session.
 */
class Session extends Auth implements IAuth
{
    /**
     * 构造函数.
     */
    public function __construct(protected ISession $session, array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * 数据持久化.
     */
    protected function setPersistence(string $key, string $value, ?int $expire = null): string
    {
        $this->session->setExpire($expire);
        $this->session->set($key, $value);

        return $key;
    }

    /**
     * 获取持久化数据.
     */
    protected function getPersistence(string $key): mixed
    {
        return $this->session->get($key);
    }

    /**
     * 删除持久化数据.
     */
    protected function deletePersistence(string $key): void
    {
        $this->session->delete($key);
    }
}
