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

namespace Leevel\Encryption\Safe;

/**
 * url 安全过滤.
 *
 * @param string     $url
 * @param null|array $protocols
 * @param bool       $show
 *
 * @return string
 */
function esc_url(string $url, ?array $protocols = null, bool $show = true): string
{
    if ('' === trim($url)) {
        return $url;
    }

    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);

    // 回车编码
    $url = deep_replace(['%0d', '%0a', '%0D', '%0A'], $url);

    // 防止拼写错误
    $url = str_replace(';//', '://', $url);

    // 加上 http:// ，防止导入一个脚本如 php，从而引发安全问题
    if (false === strpos($url, ':') &&
        '/' !== substr($url, 0, 1) &&
        '#' !== substr($url, 0, 1) &&
        !preg_match('/^[a-z0-9-]+?\.php/i', $url)) {
        $url = 'http://'.$url;
    }

    if (true === $show) {
        $url = str_replace('&amp;', '&#038;', $url);
        $url = str_replace("'", '&#039;', $url);
    }

    // 协议检查
    if (!is_array($protocols)) {
        $protocols = [
            'http', 'https', 'ftp', 'ftps', 'mailto',
            'news', 'irc', 'gopher', 'nntp', 'feed',
            'telnet', 'mms', 'rtsp', 'svn',
        ];
    }

    return $url;
}

class esc_url
{
}

// import fn.
class_exists(deep_replace::class);
