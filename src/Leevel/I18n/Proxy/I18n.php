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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\I18n\Proxy;

use Leevel\Di\Container;
use Leevel\I18n\I18n as BaseI18n;

/**
 * 代理 i18n.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class I18n
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 获取语言 text.
     *
     * @param string $text
     * @param array  ...$data
     *
     * @return string
     */
    public static function __(string $text, ...$data): string
    {
        return self::proxy()->__($text, ...$data);
    }

    /**
     * 获取语言 text.
     *
     * @param string $text
     * @param array  ...$data
     *
     * @return string
     */
    public static function gettext(string $text, ...$data): string
    {
        return self::proxy()->gettext($text, ...$data);
    }

    /**
     * 添加语言包.
     *
     * @param string $i18n 语言名字
     * @param array  $data 语言包数据
     */
    public static function addtext(string $i18n, array $data = []): void
    {
        self::proxy()->addtext($i18n, $data);
    }

    /**
     * 设置当前语言包上下文环境.
     *
     * @param string $i18n
     */
    public static function setI18n(string $i18n): void
    {
        self::proxy()->setI18n($i18n);
    }

    /**
     * 获取当前语言包.
     *
     * @return string
     */
    public static function getI18n(): string
    {
        return self::proxy()->getI18n();
    }

    /**
     * 返回所有语言包.
     *
     * @return array
     */
    public static function all(): array
    {
        return self::proxy()->all();
    }

    /**
     * 代理服务.
     *
     * @return \Leevel\I18n\I18n
     */
    public static function proxy(): BaseI18n
    {
        return Container::singletons()->make('i18n');
    }
}
