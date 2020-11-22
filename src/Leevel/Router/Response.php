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

namespace Leevel\Router;

use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use SplFileInfo;
use SplFileObject;

/**
 * 响应.
 */
class Response
{
    /**
     * 视图.
     */
    protected IView $view;

    /**
     * 跳转实例.
     */
    protected Redirect $redirect;

    /**
     * 视图正确模板.
     */
    protected string $viewSuccessTemplate = 'success';

    /**
     * 视图错误模板.
    */
    protected string $viewFailTemplate = 'fail';

    /**
     * 构造函数.
     */
    public function __construct(IView $view, Redirect $redirect)
    {
        $this->view = $view;
        $this->redirect = $redirect;
    }

    /**
     * 返回一个响应.
     */
    public function make(mixed $content = '', int $status = 200, array $headers = []): BaseResponse
    {
        return new BaseResponse($content, $status, $headers);
    }

    /**
     * 返回视图响应.
     */
    public function view(string $file, array $vars = [], ?string $ext = null, int $status = 200, array $headers = []): BaseResponse
    {
        return $this->make($this->view->display($file, $vars, $ext), $status, $headers);
    }

    /**
     * 返回视图成功消息.
     */
    public function viewSuccess(string $message, string $url = '', int $time = 1, int $status = 200, array $headers = []): BaseResponse
    {
        $vars = [
            'message' => $message,
            'url'     => $url,
            'time'    => $time,
        ];

        return $this->view($this->viewSuccessTemplate, $vars, null, $status, $headers);
    }

    /**
     * 返回视图失败消息.
     */
    public function viewFail(string $message, string $url = '', int $time = 3, int $status = 404, array $headers = []): BaseResponse
    {
        $vars = [
            'message' => $message,
            'url'     => $url,
            'time'    => $time,
        ];

        return $this->view($this->viewFailTemplate, $vars, null, $status, $headers);
    }

    /**
     * 返回 JSON 响应.
     */
    public function json(mixed $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $json);
    }

    /**
     * 返回 JSONP 响应.
     */
    public function jsonp(string $callback, mixed $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return $this
            ->json($data, $status, $headers, $json)
            ->setCallback($callback);
    }

    /**
     * 返回下载响应.
     */
    public function download(SplFileInfo|SplFileObject|string $file, string $name = null, int $status = 200, array $headers = [], bool $public = true, bool $autoEtag = false, bool $autoLastModified = true): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file, $status, $headers, $public, ResponseHeaderBag::DISPOSITION_ATTACHMENT, $autoEtag, $autoLastModified);
        if (null !== $name) {
            return $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);
        }

        return $response;
    }

    /**
     * 返回文件响应.
     */
    public function file(SplFileInfo|SplFileObject|string $file, int $status = 200, array $headers = [], bool $public = true, bool $autoEtag = false, bool $autoLastModified = true): BinaryFileResponse
    {
        return new BinaryFileResponse($file, $status, $headers, $public, ResponseHeaderBag::DISPOSITION_INLINE, $autoEtag, $autoLastModified);
    }

    /**
     * 返回一个 URL 生成跳转响应.
     */
    public function redirect(string $url, array $params = [], string $subdomain = 'www', null|bool|string $suffix = null, int $status = 302, array $headers = []): RedirectResponse
    {
        return $this->redirect->url($url, $params, $subdomain, $suffix, $status, $headers);
    }

    /**
     * 返回一个跳转响应.
     */
    public function redirectRaw(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return $this->redirect->raw($url, $status, $headers);
    }

    /**
     * 设置视图正确模板.
     */
    public function setViewSuccessTemplate(string $template): self
    {
        $this->viewSuccessTemplate = $template;

        return $this;
    }

    /**
     * 设置视图错误模板.
     */
    public function setViewFailTemplate(string $template): self
    {
        $this->viewFailTemplate = $template;

        return $this;
    }
}
