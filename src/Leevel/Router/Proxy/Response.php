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

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Router\Response as RouterResponse;

/**
 * 代理 response.
 *
 * @method static \Symfony\Component\HttpFoundation\Response make($content = '', $status = 200, array $headers = [])                                                                                                             返回一个响应.
 * @method static \Symfony\Component\HttpFoundation\Response view(string $file, array $vars = [], ?string $ext = null, int $status = 200, array $headers = [])                                                                   返回视图响应.
 * @method static \Symfony\Component\HttpFoundation\Response viewSuccess(string $message, string $url = '', int $time = 1, int $status = 200, array $headers = [])                                                               返回视图成功消息.
 * @method static \Symfony\Component\HttpFoundation\Response viewFail(string $message, string $url = '', int $time = 3, int $status = 404, array $headers = [])                                                                  返回视图失败消息.
 * @method static \Leevel\Http\JsonResponse json($data = null, int $status = 200, array $headers = [], bool $json = false)                                                                                                       返回 JSON 响应.
 * @method static \Leevel\Http\JsonResponse jsonp(string $callback, $data = null, int $status = 200, array $headers = [], bool $json = false)                                                                                    返回 JSONP 响应.
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse download($file, ?string $name = null, int $status = 200, array $headers = [], bool $public = true, bool $autoEtag = false, bool $autoLastModified = true) 返回下载响应.
 * @method static \Symfony\Component\HttpFoundation\BinaryFileResponse file($file, int $status = 200, array $headers = [], bool $public = true, bool $autoEtag = false, bool $autoLastModified = true)                           返回文件响应.
 * @method static \Leevel\Http\RedirectResponse redirect(string $url, array $params = [], string $subdomain = 'www', null|bool|string $suffix = null, int $status = 302, array $headers = [])                                                     返回一个 URL 生成跳转响应.
 * @method static \Leevel\Http\RedirectResponse redirectRaw(string $url, int $status = 302, array $headers = [])                                                                                                                 返回一个跳转响应.
 * @method static \Leevel\Router\Response setViewSuccessTemplate(string $template)                                                                                                                                               设置视图正确模板.
 * @method static \Leevel\Router\Response setViewFailTemplate(string $template)                                                                                                                                                  设置视图错误模板.
 */
class Response
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): RouterResponse
    {
        return Container::singletons()->make('response');
    }
}
