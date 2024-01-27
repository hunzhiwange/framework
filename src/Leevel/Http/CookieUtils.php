<?php

declare(strict_types=1);

namespace Leevel\Http;

use Symfony\Component\HttpFoundation\Cookie;

/**
 * COOKIE 助手.
 */
class CookieUtils
{
    /**
     * 配置.
     */
    protected static array $config = [
        'expire' => 86400,
        'domain' => '',
        'path' => '/',
        'secure' => false,
        'httponly' => false,
        'samesite' => null,
        'raw' => false,
    ];

    /**
     * 初始化配置.
     */
    public static function initConfig(array $config = []): void
    {
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * 生成 COOKIE.
     *
     * @throws \Exception
     */
    public static function makeCookie(string $name, ?string $value = null, array $config = []): Cookie
    {
        $config = self::normalizeConfigs($config);
        self::normalizeExpire($config);

        return new Cookie(
            $name,
            $value,
            $config['expire'],
            $config['path'],
            $config['domain'],
            $config['secure'],
            $config['httponly'],
            $config['raw'],
            $config['samesite'],
        );
    }

    /**
     * 整理配置.
     */
    protected static function normalizeConfigs(array $config = []): array
    {
        return $config ? array_merge(self::$config, $config) : self::$config;
    }

    /**
     * 整理过期时间.
     *
     * @throws \Exception
     */
    protected static function normalizeExpire(array &$config): void
    {
        $config['expire'] = (int) $config['expire'];
        if ($config['expire'] < 0) {
            throw new \Exception('Cookie expire date must greater than or equal 0.');
        }

        if ($config['expire'] > 0) {
            $config['expire'] = time() + $config['expire'];
        }
    }
}
