<?php

declare(strict_types=1);

namespace Leevel\Session;

/**
 * session.test.
 *
 * @internal
 *
 * @coversNothing
 */
final class Test extends Session implements ISession
{
    /**
     * 配置.
     */
    protected array $option = [
        'id' => null,
        'name' => null,
    ];

    /**
     * 构造函数.
     */
    public function __construct(array $option = [])
    {
        $this->option = array_merge($this->option, $option);
        $this->setName($this->option['name']);
    }

    /**
     * {@inheritDoc}
     */
    public function open(string $savePath, string $sessionName): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function read(string $sessionId): string
    {
        return serialize([]);
    }

    /**
     * {@inheritDoc}
     */
    public function write(string $sessionId, string $sessionData): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function destroy(string $sessionId): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc(int $maxLifetime): int
    {
        return 0;
    }
}
