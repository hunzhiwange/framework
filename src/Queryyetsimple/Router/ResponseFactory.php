<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Router;

use Queryyetsimple\{
    Mvc\IView,
    Http\Response,
    Http\ApiResponse,
    Http\FileResponse,
    Http\JsonResponse
};

/**
 * 响应工厂
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.03
 * @version 1.0
 */
class ResponseFactory
{

    /**
     * 视图
     *
     * @var \Queryyetsimple\Mvc\IView
     */
    protected $view;

    /**
     * 跳转实例
     *
     * @var \Queryyetsimple\Router\Redirector
     */
    protected $redirector;

    /**
     * 视图正确模板
     *
     * @var string
     */
    protected $viewSuccessTemplate = 'public+success';

    /**
     * 视图错误模板
     *
     * @var string
     */
    protected $viewFailTemplate = 'public+fail';

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Mvc\IViewy $view
     * @param \Queryyetsimple\Router\Redirect $redirector
     * @return void
     */
    public function __construct(IView $view, Redirect $redirector)
    {
        $this->view = $view;
        $this->redirector = $redirector;
    }

    /**
     * 返回一个响应
     * 
     * @param string $content
     * @param integer $status
     * @param array $headers
     * @return \Queryyetsimple\Http\Response
     */
    public function make($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }

    /**
     * 返回视图响应
     *
     * @param string $file
     * @param array $vars
     * @param string $ext
     * @param int $status
     * @param array $headers
     * @return \Queryyetsimple\Http\Response
     */
    public function view($file = null, array $vars = [], string $ext = '', $status = 200, array $headers = [])
    {
        return $this->make($this->view->display($file, $vars, $ext), $status, $headers);
    }

    /**
     * 返回视图正确消息
     *
     * @param string $message
     * @param string $url
     * @param int $time
     * @param int $status
     * @param array $headers
     * @return \Queryyetsimple\Http\Response
     */
    public function viewSuccess($message = '', $url = '', $time = 1, $status = 200, array $headers = [])
    {
        $vars = [
            'message' => $message ?: 'Succeed',
            'url' => $url,
            'time' => $time  
        ];

        return $this->view($this->viewSuccessTemplate, $vars, '', $status, $headers);
    }

    /**
     * 返回视图错误消息
     *
     * @param string $message
     * @param string $url
     * @param int $time
     * @param int $status
     * @param array $headers
     * @return \Queryyetsimple\Http\Response
     */
    public function viewError($message = '', $url = '', $time = 3, $status = 200, array $headers = [])
    {
        $vars = [
            'message' => $message ?: 'Failed',
            'url' => $url,
            'time' => $time  
        ];

        return $this->view($this->viewFailTemplate, $vars, '', $status, $headers);
    }

    /**
     * 返回 JSON 响应
     *
     * @param string $data
     * @param integer $status
     * @param array $headers
     * @param bool $json
     * @return \Queryyetsimple\Http\JsonResponse
     */
    public function json($data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        return new JsonResponse($data, $status, $headers, $json);
    }

    /**
     * 返回 JSONP 响应
     *
     * @param string $callback
     * @param string $data
     * @param integer $status
     * @param array $headers
     * @param bool $json
     * @return \Queryyetsimple\Http\JsonResponse
     */
    public function jsonp(string $callback, $data = null, int $status = 200, array $headers = [], bool $json = false)
    {
        return $this->json($data, $status, $headers, $json)->

        setCallback($callback);
    }

    /**
     * 返回下载响应
     *
     * @param \SplFileObject|\SplFileInfo|string $file
     * @param string $name
     * @param integer $status
     * @param array $headers
     * @param bool $autoEtag
     * @param bool $autoLastModified
     * @return \Queryyetsimple\Http\FileResponse
     */
    public function download($file, string $name = null, int $status = 200, array $headers = [], bool $autoEtag = false, bool $autoLastModified = true)
    {
        $response = new FileResponse($file, $status, $headers, FileResponse::DISPOSITION_ATTACHMENT, $autoEtag, $autoLastModified);

        if (! is_null($name)) {
            return $response->setContentDisposition(FileResponse::DISPOSITION_ATTACHMENT, $name);
        }

        return $response;
    }

    /**
     * 返回文件响应
     *
     * @param \SplFileObject|\SplFileInfo|string $file
     * @param integer $status
     * @param array $headers
     * @param bool $autoEtag
     * @param bool $autoLastModified
     * @return \Queryyetsimple\Http\FileResponse
     */
    public function file($file, int $status = 200, array $headers = [], bool $autoEtag = false, bool $autoLastModified = true)
    {
        return new FileResponse($file, $status, $headers, FileResponse::DISPOSITION_INLINE, $autoEtag, $autoLastModified);
    }

    /*
     * 返回一个 URL 生成跳转响应
     *
     * @param string $url
     * @param array $params
     * @param array $option
     * @sub boolean suffix 是否包含后缀
     * @sub boolean normal 是否为普通 url
     * @sub string subdomain 子域名
     * @param int $status
     * @param array $headers
     * @return \Queryyetsimple\Http\RedirectResponse
     */
    public function redirect(?string $url, $params = [], $option = [], int $status = 302, array $headers = [])
    {
        return $this->redirector->url($url, $params, $option, $status, $headers);
    }

    /**
     * 返回一个跳转响应
     *
     * @param string $url
     * @param int $status
     * @param array $headers
     * @return \Queryyetsimple\Http\RedirectResponse
     */
    public function redirectRaw(?string $url, int $status = 302, array $headers = [])
    {
        return $this->redirector->raw($url, $status, $headers);
    }

    /**
     * 请求成功
     * 一般用于GET与POST请求： 200
     * 
     * @param mixed $content
     * @param string $text
     * @return $this
     */
    public function apiOk($content = '', $text = null) {
        return $this->createApiResponse()->ok($location, $text);
    }

    /**
     * 已创建
     * 成功请求并创建了新的资源: 201
     *
     * @param null|string $location
     * @return $this
     */
    public function apiCreated($location = '', $content = '')
    {
        return $this->createApiResponse()->created($location, $content);
    }

    /**
     * 已接受
     * 已经接受请求，但未处理完成: 202
     *
     * @param null|string $location
     * @param mixed $content
     * @return $this
     */
    public function apiAccepted($location = null, $content = '')
    {
        return $this->createApiResponse()->accepted($location, $content);
    }

    /**
     * 无内容
     * 服务器成功处理，但未返回内容: 204
     *
     * @return $this
     */
    public function apiNoContent()
    {
        return $this->createApiResponse()->noContent();
    }

    /**
     * 无法处理的实体
     * 请求格式正确，但是由于含有语义错误，无法响应: 422
     * 
     * @param array $errors
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiUnprocessableEntity(array $errors = null, $message = null, $text = null) {
        return $this->createApiResponse()->unprocessableEntity($errors, $message, $text);
    }

    /**
     * 错误请求
     * 服务器不理解请求的语法: 400
     * 
     * @param string $message
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiError($message, $statusCode, $text = null) {
        return $this->createApiResponse()->error($message, $statusCode, $text);
    }

    /**
     * 错误请求
     * 服务器不理解请求的语法: 400
     * 
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiBadRequest($message = null, $text = null) {
        return $this->createApiResponse()->badRequest($message, $text);
    }

    /**
     * 未授权
     * 对于需要登录的网页，服务器可能返回此响应: 401
     * 
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiUnauthorized($message = null, $text = null) {
        return $this->createApiResponse()->unauthorized($message, $text);
    }

    /**
     * 禁止
     * 服务器拒绝请求: 403
     * 
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiForbidden($message = null, $text = null) {
        return $this->createApiResponse()->forbidden($message, $text);
    }

    /**
     * 未找到
     * 用户发出的请求针对的是不存在的记录: 404
     * 
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiNotFound($message = null, $text = null) {
        return $this->createApiResponse()->notFound($message, $text);
    }

    /**
     * 方法禁用
     * 禁用请求中指定的方法: 405
     * 
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiMethodNotAllowed($message = null, $text = null) {
        return $this->createApiResponse()->methodNotAllowed($message, $text);
    }

    /**
     * 太多请求
     * 用户在给定的时间内发送了太多的请求: 429
     * 
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiTooManyRequests($message = null, $text = null) {
        return $this->createApiResponse()->tooManyRequests($message, $text);
    }

    /**
     * 服务器内部错误
     * 服务器遇到错误，无法完成请求: 500
     * 
     * @param string $message
     * @param string $text
     * @return $this
     */
    public function apiInternalServerError($message = null, $text = null) {
        return $this->createApiResponse()->internalServerError($message, $text);
    }

    /**
     * 创建基础 API 响应
     * 
     * @return \Queryyetsimple\Http\ApiResponse
     */
    protected function createApiResponse() {
        return new ApiResponse();
    }

    /**
     * 设置视图正确模板
     * 
     * @param string $template
     * @return $this
     */
    public function setViewSuccessTemplate(string $template) {
        $this->viewSuccessTemplate = $template;

        return $this;
    }

    /**
     * 设置视图错误模板
     * 
     * @param string $template
     * @return $this
     */
    public function setViewFailTemplate(string $template) {
        $this->viewFailTemplate = $template;

        return $this;
    }
}
