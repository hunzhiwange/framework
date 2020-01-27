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
     *
     * @var array
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
     * 设置配置.
     *
     * @param mixed $value
     */
    public static function setOption(string $name, $value): void
    {
        self::$option[$name] = $value;
    }

    /**
     * 生成 COOKIE.
     *
     * @param null|array|string $value
     *
     * @throws \Exception
     */
    public static function makeCookie(string $name, $value = null, array $option = []): Cookie
    {
        $option = self::normalizeOptions($option);

        if (is_array($value)) {
            $value = json_encode($value);
        } elseif (!is_string($value) && null !== $value) {
            $e = 'Cookie value must be string,array or null.';

            throw new Exception($e);
        }

        $option['expire'] = (int) ($option['expire']);
        if ($option['expire'] < 0) {
            $e = 'Cookie expire date must greater than or equal 0.';

            throw new Exception($e);
        }

        if ($option['expire'] > 0) {
            $option['expire'] = time() + $option['expire'];
        }

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
}
