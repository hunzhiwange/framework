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

/**
 * 函数库.
 */
class I18nMock
{
    /**
     * lang.
     *
     * @param array $data
     *
     * @return string
     */
    public function __(string $text, ...$data)
    {
        return sprintf($text, ...$data);
    }

    /**
     * lang.
     *
     * @param array $data
     *
     * @return string
     */
    public function gettext(string $text, ...$data)
    {
        return sprintf($text, ...$data);
    }
}

if (!function_exists('env')) {
    /**
     * 获取应用的环境变量.
     *
     * - 支持 bool, empty 和 null 值.
     * - 因为与 vendor/cakephp/core/functions.php 中的 env 方法存在冲突，所以提前替代该函数
     *
     * @param null|mixed $defaults
     *
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    function env(string $name, $defaults = null)
    {
        if (false === $value = getenv($name)) {
            $value = $defaults;
        }

        switch ($value) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (is_string($value) && strlen($value) > 1 &&
            '"' === $value[0] && '"' === $value[strlen($value) - 1]) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
