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

use Leevel\Http\ApiResponse;
use Leevel\Http\FileResponse;
use Leevel\Http\JsonResponse;
use Leevel\Http\RedirectResponse;
use Leevel\Http\Response as BaseResponse;
use Leevel\Http\ResponseHeaderBag;

/**
 * 响应.
 */
class Response
{
    /**
     * 视图.
     *
     * @var \Leevel\Router\IView
     */
    protected IView $view;

    /**
     * 跳转实例.
     *
     * @var \Leevel\Router\Redirect
     */
    protected Redirect $redirect;

    /**
     * 视图正确模板.
     *
     * @var string
     */
    protected string $viewSuccessTemplate = 'success';

    /**
     * 视图错误模板.
     *
     * @var string
     */
    protected string $viewFailTemplate = 'fail';

    /**
     * 构造函数.
     *
     * @param \Leevel\Router\IView    $view
     * @param \Leevel\Router\Redirect $redirect
     */
    public function __construct(IView $view, Redirect $redirect)
    {
        $this->view = $view;
        $this->redirect = $redirect;
    }

    /**
     * 返回一个响应.
     *
     * @param mixed $content
     */
    public function make($content = '', int $status = 200, array $headers = []): BaseResponse
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
     *
     * @param null|mixed $data
     */
    public function json($data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return new JsonResponse($data, $status, $headers, $json);
    }

    /**
     * 返回 JSONP 响应.
     *
     * @param null|mixed $data
     */
    public function jsonp(string $callback, $data = null, int $status = 200, array $headers = [], bool $json = false): JsonResponse
    {
        return $this
            ->json($data, $status, $headers, $json)
            ->setCallback($callback);
    }

    /**
     * 返回下载响应.
     *
     * @param \SplFileInfo|\SplFileObject|string $file
     */
    public function download($file, string $name = null, int $status = 200, array $headers = [], bool $autoEtag = false, bool $autoLastModified = true): FileResponse
    {
        $response = new FileResponse($file, $status, $headers, ResponseHeaderBag::DISPOSITION_ATTACHMENT, $autoEtag, $autoLastModified);
        if (null !== $name) {
            return $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);
        }

        return $response;
    }

    /**
     * 返回文件响应.
     *
     * @param \SplFileInfo|\SplFileObject|string $file
     */
    public function file($file, int $status = 200, array $headers = [], bool $autoEtag = false, bool $autoLastModified = true): FileResponse
    {
        return new FileResponse($file, $status, $headers, ResponseHeaderBag::DISPOSITION_INLINE, $autoEtag, $autoLastModified);
    }

    /**
     * 返回一个 URL 生成跳转响应.
     *
     * @param null|bool|string $suffix
     */
    public function redirect(string $url, array $params = [], string $subdomain = 'www', $suffix = null, int $status = 302, array $headers = []): RedirectResponse
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
     * 请求成功.
     *
     * - 一般用于GET与POST请求： 200.
     *
     * @param mixed $content
     */
    public function apiOk($content = '', ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->ok($content, $text);
    }

    /**
     * 已创建.
     *
     * - 成功请求并创建了新的资源: 201.
     *
     * @param mixed $content
     */
    public function apiCreated(?string $location = null, $content = ''): ApiResponse
    {
        return $this->createApiResponse()->created($location, $content);
    }

    /**
     * 已接受.
     *
     * - 已经接受请求，但未处理完成: 202.
     *
     * @param mixed $content
     */
    public function apiAccepted(?string $location = null, $content = ''): ApiResponse
    {
        return $this->createApiResponse()->accepted($location, $content);
    }

    /**
     * 无内容.
     *
     * - 服务器成功处理，但未返回内容: 204.
     */
    public function apiNoContent(): ApiResponse
    {
        return $this->createApiResponse()->noContent();
    }

    /**
     * 错误请求
     */
    public function apiError(string $message, int $statusCode, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->error($message, $statusCode, $text);
    }

    /**
     * 错误请求.
     *
     * - 服务器不理解请求的语法: 400.
     */
    public function apiBadRequest(?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->badRequest($message, $text);
    }

    /**
     * 未授权.
     *
     * - 对于需要登录的网页，服务器可能返回此响应: 401.
     */
    public function apiUnauthorized(?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->unauthorized($message, $text);
    }

    /**
     * 禁止.
     *
     * - 服务器拒绝请求: 403.
     */
    public function apiForbidden(?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->forbidden($message, $text);
    }

    /**
     * 未找到.
     *
     * - 用户发出的请求针对的是不存在的记录: 404.
     */
    public function apiNotFound(?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->notFound($message, $text);
    }

    /**
     * 方法禁用.
     *
     * - 禁用请求中指定的方法: 405.
     */
    public function apiMethodNotAllowed(?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->methodNotAllowed($message, $text);
    }

    /**
     * 无法处理的实体.
     *
     * - 请求格式正确，但是由于含有语义错误，无法响应: 422.
     */
    public function apiUnprocessableEntity(?array $errors = null, ?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->unprocessableEntity($errors, $message, $text);
    }

    /**
     * 太多请求.
     *
     * - 用户在给定的时间内发送了太多的请求: 429.
     */
    public function apiTooManyRequests(?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->tooManyRequests($message, $text);
    }

    /**
     * 服务器内部错误.
     *
     * - 服务器遇到错误，无法完成请求: 500.
     */
    public function apiInternalServerError(?string $message = null, ?string $text = null): ApiResponse
    {
        return $this->createApiResponse()->internalServerError($message, $text);
    }

    /**
     * 设置视图正确模板.
     *
     * @return \Leevel\Router\Response
     */
    public function setViewSuccessTemplate(string $template): self
    {
        $this->viewSuccessTemplate = $template;

        return $this;
    }

    /**
     * 设置视图错误模板.
     *
     * @return \Leevel\Router\Response
     */
    public function setViewFailTemplate(string $template): self
    {
        $this->viewFailTemplate = $template;

        return $this;
    }

    /**
     * 创建基础 API 响应.
     */
    protected function createApiResponse(): ApiResponse
    {
        return new ApiResponse();
    }
}
