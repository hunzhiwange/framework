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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Http;

/**
 * HTTP 请求接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.27
 *
 * @version 1.0
 */
interface IRequest
{
    /**
     * METHOD_HEAD.
     *
     * @var string
     */
    const METHOD_HEAD = 'HEAD';

    /**
     * METHOD_GET.
     *
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * METHOD_POST.
     *
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * METHOD_PUT.
     *
     * @var string
     */
    const METHOD_PUT = 'PUT';

    /**
     * METHOD_PATCH.
     *
     * @var string
     */
    const METHOD_PATCH = 'PATCH';

    /**
     * METHOD_DELETE.
     *
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * METHOD_PURGE.
     *
     * @var string
     */
    const METHOD_PURGE = 'PURGE';

    /**
     * METHOD_OPTIONS.
     *
     * @var string
     */
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * METHOD_TRACE.
     *
     * @var string
     */
    const METHOD_TRACE = 'TRACE';

    /**
     * METHOD_CONNECT.
     *
     * @var string
     */
    const METHOD_CONNECT = 'CONNECT';

    /**
     * 服务器 url 重写支持 pathInfo.
     *
     * Nginx
     * location @rewrite {
     *     rewrite ^/(.*)$ /index.php?_url=/$1;
     * }
     *
     * @var string
     */
    const PATHINFO_URL = '_url';

    /**
     * 请求方法伪装.
     *
     * @var string
     */
    const VAR_METHOD = '_method';

    /**
     * AJAX 伪装.
     *
     * @var string
     */
    const VAR_AJAX = '_ajax';

    /**
     * PJAX 伪装.
     *
     * @var string
     */
    const VAR_PJAX = '_pjax';

    /**
     * JSON 伪装.
     *
     * @var string
     */
    const VAR_JSON = '_json';

    /**
     * 接受 JSON 伪装.
     *
     * @var string
     */
    const VAR_ACCEPT_JSON = '_acceptjson';
}
