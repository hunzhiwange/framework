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
use Leevel\Http\FileResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Router\Response as IBaseResponse;
use Leevel\Router\Response as RouterResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

/**
 * 代理 response.
 *
 * @codeCoverageIgnore
 */
class Response
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 返回一个响应.
     *
     * @param mixed $content
     * @param int   $status
     */
    public static function make($content = '', $status = 200, array $headers = []): BaseResponse
    {
        return self::proxy()->make($content, $status, $headers);
    }

    /**
     * 返回视图响应.
     */
    public static function view(string $file, array $vars = [], ?string $ext = null, int $status = 200, array $headers = []): BaseResponse
    {
        return self::proxy()->view($file, $vars, $ext, $status, $headers);
    }

    /**
     * 返回视图成功消息.
     */
    public static function viewSuccess(string $message, string $url = '', int $time = 1, int $status = 200, array $headers = []): BaseResponse
    {
        return self::proxy()->viewSuccess($message, $url, $time, $status, $headers);
    }

    /**
     * 返回视图失败消息.
     */
    public static function viewFail(string $message, string $url = '', int $time = 3, int $status = 404, array $headers = []): BaseResponse
    {
        return self::proxy()->viewFail($message, $url, $time, $status, $headers);
    }

    /**
     * 返回 JSON 响应.
     *
     * @param null|mixed $data
     */
    public static function json($data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return self::proxy()->json($data, $status, $headers, $json);
    }

    /**
     * 返回 JSONP 响应.
     *
     * @param null|mixed $data
     */
    public static function jsonp(string $callback, $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return self::proxy()->jsonp($callback, $data, $status, $headers, $json);
    }

    /**
     * 返回下载响应.
     *
     * @param \SplFileInfo|\SplFileObject|string $file
     */
    public static function download($file, ?string $name = null, int $status = 200, array $headers = [], bool $autoEtag = false, bool $autoLastModified = true): FileResponse
    {
        return self::proxy()->download($file, $name, $status, $headers, $autoEtag, $autoLastModified);
    }

    /**
     * 返回文件响应.
     *
     * @param \SplFileInfo|\SplFileObject|string $file
     */
    public static function file($file, int $status = 200, array $headers = [], bool $autoEtag = false, bool $autoLastModified = true): FileResponse
    {
        return self::proxy()->file($file, $status, $headers, $autoEtag, $autoLastModified);
    }

    /**
     * 返回一个 URL 生成跳转响应.
     *
     * @param null|bool|string $suffix
     */
    public static function redirect(string $url, array $params = [], string $subdomain = 'www', $suffix = null, int $status = 302, array $headers = []): RedirectResponse
    {
        return self::proxy()->redirect($url, $params, $subdomain, $suffix, $status, $headers);
    }

    /**
     * 返回一个跳转响应.
     */
    public static function redirectRaw(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return self::proxy()->redirectRaw($url, $status, $headers);
    }

    /**
     * 设置视图正确模板.
     */
    public static function setViewSuccessTemplate(string $template): IBaseResponse
    {
        return self::proxy()->setViewSuccessTemplate($template);
    }

    /**
     * 设置视图错误模板.
     */
    public static function setViewFailTemplate(string $template): IBaseResponse
    {
        return self::proxy()->setViewFailTemplate($template);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): RouterResponse
    {
        return Container::singletons()->make('response');
    }
}
