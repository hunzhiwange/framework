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

/**
 * Api 响应请求
 */
class ApiResponse extends JsonResponse
{
    /**
     * 创建一个 API 响应.
     *
     * @param mixed $data
     *
     * @return static
     */
    public static function create($data = '', int $status = 200, array $headers = []): IResponse
    {
        return new static($data, $status, $headers);
    }

    /**
     * 请求成功.
     *
     * - 一般用于 GET 与 POST 请求: 200.
     *
     * @param mixed $content
     *
     * @return \Leevel\Http\IResponse
     */
    public function ok($content = '', ?string $text = null): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setStatusCode(static::HTTP_OK, $text);

        return $this->setData($content);
    }

    /**
     * 已创建.
     *
     * - 成功请求并创建了新的资源: 201.
     *
     * @param mixed $content
     *
     * @return \Leevel\Http\IResponse
     */
    public function created(?string $location = null, $content = ''): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setData($content);
        $this->setStatusCode(static::HTTP_CREATED);

        if (null !== $location) {
            $this->setHeader('Location', $location);
        }

        return $this;
    }

    /**
     * 已接受.
     *
     * - 已经接受请求，但未处理完成: 202.
     *
     * @param mixed $content
     *
     * @return \Leevel\Http\IResponse
     */
    public function accepted(?string $location = null, $content = ''): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setData($content);
        $this->setStatusCode(static::HTTP_ACCEPTED);

        if (null !== $location) {
            $this->setHeader('Location', $location);
        }

        return $this;
    }

    /**
     * 无内容.
     *
     * - 服务器成功处理，但未返回内容: 204.
     *
     * @return \Leevel\Http\IResponse
     */
    public function noContent(): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->setStatusCode(static::HTTP_NO_CONTENT);
    }

    /**
     * 无法处理的实体.
     *
     * - 请求格式正确，但是由于含有语义错误，无法响应: 422.
     *
     * @return \Leevel\Http\IResponse
     */
    public function unprocessableEntity(?array $errors = null, ?string $message = null, ?string $text = null): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setStatusCode(static::HTTP_UNPROCESSABLE_ENTITY, $text);
        $this->setData([
            'message' => $this->parseErrorMessage($message), 'errors'  => $errors ?: [],
        ]);

        return $this;
    }

    /**
     * 错误请求.
     *
     * - 服务器不理解请求的语法: 400.
     *
     * @return \Leevel\Http\IResponse
     */
    public function error(?string $message, int $statusCode, ?string $text = null): IResponse
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setStatusCode($statusCode, $text);

        return $this->normalizeErrorMessage($message);
    }

    /**
     * 错误请求.
     *
     * - 服务器不理解请求的语法: 400.
     *
     * @return \Leevel\Http\IResponse
     */
    public function badRequest(?string $message = null, ?string $text = null): IResponse
    {
        return $this->error($message, static::HTTP_BAD_REQUEST, $text);
    }

    /**
     * 未授权.
     *
     * - 对于需要登录的网页，服务器可能返回此响应: 401.
     *
     * @return \Leevel\Http\IResponse
     */
    public function unauthorized(?string $message = null, ?string $text = null): IResponse
    {
        return $this->error($message, static::HTTP_UNAUTHORIZED, $text);
    }

    /**
     * 禁止.
     *
     * - 服务器拒绝请求: 403.
     *
     * @return \Leevel\Http\IResponse
     */
    public function forbidden(?string $message = null, ?string $text = null): IResponse
    {
        return $this->error($message, static::HTTP_FORBIDDEN, $text);
    }

    /**
     * 未找到.
     *
     * - 用户发出的请求针对的是不存在的记录: 404.
     *
     * @return \Leevel\Http\IResponse
     */
    public function notFound(?string $message = null, ?string $text = null): IResponse
    {
        return $this->error($message, static::HTTP_NOT_FOUND, $text);
    }

    /**
     * 方法禁用.
     *
     * - 禁用请求中指定的方法: 405.
     *
     * @return \Leevel\Http\IResponse
     */
    public function methodNotAllowed(?string $message = null, ?string $text = null): IResponse
    {
        return $this->error($message, static::HTTP_METHOD_NOT_ALLOWED, $text);
    }

    /**
     * 太多请求.
     *
     * - 用户在给定的时间内发送了太多的请求: 429.
     *
     * @return \Leevel\Http\IResponse
     */
    public function tooManyRequests(?string $message = null, ?string $text = null): IResponse
    {
        return $this->error($message, static::HTTP_TOO_MANY_REQUESTS, $text);
    }

    /**
     * 服务器内部错误.
     *
     * - 服务器遇到错误，无法完成请求: 500.
     *
     * @return \Leevel\Http\IResponse
     */
    public function internalServerError(?string $message = null, ?string $text = null): IResponse
    {
        return $this->error($message, static::HTTP_INTERNAL_SERVER_ERROR, $text);
    }

    /**
     * 格式化错误消息.
     *
     * @return \Leevel\Http\IResponse
     */
    protected function normalizeErrorMessage(?string $message = null, ?string $text = null): IResponse
    {
        return $this->setData([
            'message' => $this->parseErrorMessage($message),
        ]);
    }

    /**
     * 分析错误消息.
     */
    protected function parseErrorMessage(?string $message = null): string
    {
        return $message ?: $this->statusText;
    }
}
