<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Http;

use Exception;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * COOKIE 助手.
 */
class CookieUtils
{
    /**
     * 配置.
     */
    protected static array $option = [
        'expire'   => 86400,
        'domain'   => '',
        'path'     => '/',
        'secure'   => false,
        'httponly' => false,
        'samesite' => null,
        'raw'      => false,
    ];

    /**
     * 初始化配置.
     */
    public static function initOption(array $option = []): void
    {
        self::$option = array_merge(self::$option, $option);
    }

    /**
     * 生成 COOKIE.
     *
     * @throws \Exception
     */
    public static function makeCookie(string $name, ?string $value = null, array $option = []): Cookie
    {
        $option = self::normalizeOptions($option);
        self::normalizeExpire($option);

        return new Cookie(
            $name,
            $value,
            $option['expire'],
            $option['path'],
            $option['domain'],
            $option['secure'],
            $option['httponly'],
            $option['raw'],
            $option['samesite'],
        );
    }

    /**
     * 整理配置.
     */
    protected static function normalizeOptions(array $option = []): array
    {
        return $option ? array_merge(self::$option, $option) : self::$option;
    }

    /**
     * 整理过期时间.
     *
     * @throws \Exception
     */
    protected static function normalizeExpire(array &$option): void
    {
        $option['expire'] = (int) ($option['expire']);
        if ($option['expire'] < 0) {
            $e = 'Cookie expire date must greater than or equal 0.';

            throw new Exception($e);
        }

        if ($option['expire'] > 0) {
            $option['expire'] = time() + $option['expire'];
        }
    }
}
