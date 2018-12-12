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
 * Api 响应请求
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.01
 *
 * @version 1.0
 */
class ApiResponse extends JsonResponse
{
    /**
     * 创建一个 API 响应.
     *
     * @param string $data
     * @param int    $status
     * @param array  $headers
     *
     * @return static
     */
    public static function create($data = '', int $status = 200, array $headers = [])
    {
        return new static($data, $status, $headers);
    }

    /**
     * 请求成功
     * 一般用于GET与POST请求: 200.
     *
     * @param mixed  $content
     * @param string $text
     *
     * @return $this
     */
    public function ok($content = '', $text = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setStatusCode(static::HTTP_OK, $text);

        return $this->setData($content);
    }

    /**
     * 已创建
     * 成功请求并创建了新的资源: 201.
     *
     * @param null|string $location
     * @param mixed       $content
     *
     * @return $this
     */
    public function created($location = null, $content = '')
    {
        if ($this->checkTControl()) {
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
     * 已接受
     * 已经接受请求，但未处理完成: 202.
     *
     * @param null|string $location
     * @param mixed       $content
     *
     * @return $this
     */
    public function accepted($location = null, $content = '')
    {
        if ($this->checkTControl()) {
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
     * 无内容
     * 服务器成功处理，但未返回内容: 204.
     *
     * @return $this
     */
    public function noContent()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        return $this->setStatusCode(static::HTTP_NO_CONTENT);
    }

    /**
     * 无法处理的实体
     * 请求格式正确，但是由于含有语义错误，无法响应: 422.
     *
     * @param array  $errors
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function unprocessableEntity(array $errors = null, ?string $message = null, ?string $text = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setStatusCode(static::HTTP_UNPROCESSABLE_ENTITY, $text);

        $this->setData([
            'message' => $this->parseErrorMessage($message), 'errors'  => $errors ?: [],
        ]);

        return $this;
    }

    /**
     * 错误请求
     * 服务器不理解请求的语法: 400.
     *
     * @param string $message
     * @param int    $statusCode
     * @param string $text
     * @param mixed  $statusCode
     *
     * @return $this
     */
    public function error(?string $message, int $statusCode, ?string $text = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setStatusCode($statusCode, $text);

        return $this->normalizeErrorMessage($message);
    }

    /**
     * 错误请求
     * 服务器不理解请求的语法: 400.
     *
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function badRequest(?string $message = null, ?string $text = null)
    {
        return $this->error($message, static::HTTP_BAD_REQUEST, $text);
    }

    /**
     * 未授权
     * 对于需要登录的网页，服务器可能返回此响应: 401.
     *
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function unauthorized(?string $message = null, ?string $text = null)
    {
        return $this->error($message, static::HTTP_UNAUTHORIZED, $text);
    }

    /**
     * 禁止
     * 服务器拒绝请求: 403.
     *
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function forbidden(?string $message = null, ?string $text = null)
    {
        return $this->error($message, static::HTTP_FORBIDDEN, $text);
    }

    /**
     * 未找到
     * 用户发出的请求针对的是不存在的记录: 404.
     *
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function notFound(?string $message = null, ?string $text = null)
    {
        return $this->error($message, static::HTTP_NOT_FOUND, $text);
    }

    /**
     * 方法禁用
     * 禁用请求中指定的方法: 405.
     *
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function methodNotAllowed(?string $message = null, ?string $text = null)
    {
        return $this->error($message, static::HTTP_METHOD_NOT_ALLOWED, $text);
    }

    /**
     * 太多请求
     * 用户在给定的时间内发送了太多的请求: 429.
     *
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function tooManyRequests(?string $message = null, ?string $text = null)
    {
        return $this->error($message, static::HTTP_TOO_MANY_REQUESTS, $text);
    }

    /**
     * 服务器内部错误
     * 服务器遇到错误，无法完成请求: 500.
     *
     * @param string $message
     * @param string $text
     *
     * @return $this
     */
    public function internalServerError(?string $message = null, ?string $text = null)
    {
        return $this->error($message, static::HTTP_INTERNAL_SERVER_ERROR, $text);
    }

    /**
     * 格式化错误消息.
     *
     * @param string      $message
     * @param null|string $text
     *
     * @return $this
     */
    protected function normalizeErrorMessage(?string $message = null, ?string $text = null)
    {
        return $this->setData([
            'message' => $this->parseErrorMessage($message),
        ]);
    }

    /**
     * 分析错误消息.
     *
     * @param string $message
     *
     * @return string
     */
    protected function parseErrorMessage(?string $message = null)
    {
        return $message ?: $this->statusText;
    }
}
