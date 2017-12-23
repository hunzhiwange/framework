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
namespace queryyetsimple\http;

use InvalidArgumentException;
use queryyetsimple\{
    mvc\iview,
    support\xml,
    router\router,
    support\assert,
    support\option,
    filesystem\fso,
    cookie\icookie,
    support\infinity,
    session\isession,
    support\flow_control
};

/**
 * 响应请求
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.18
 * @version 1.0
 */
class response
{
    use option{
        option as infinityOption;
        options as infinityOptions;
    }
    use infinity {
        __call as infinityCall;
    }
    use flow_control;

    /**
     * view
     *
     * @var \queryyetsimple\mvc\iview
     */
    protected $objView;

    /**
     * session 处理
     *
     * @var \queryyetsimple\session\isession
     */
    protected $objSession;

    /**
     * cookie 处理
     *
     * @var \queryyetsimple\cookie\icookie
     */
    protected $objCookie;

    /**
     * router
     *
     * @var \queryyetsimple\router\router
     */
    protected $objRouter;

    /**
     * 响应数据
     *
     * @var mixed
     */
    protected $mixData;

    /**
     * 设置内容
     *
     * @var string
     */
    protected $strContent;

    /**
     * 追加内容
     *
     * @var string
     */
    protected $strAppendContent;

    /**
     * 响应状态
     *
     * @var int
     */
    protected $intCode = 200;

    /**
     * 消息内容
     *
     * @var int
     */
    protected $strMessage = '';

    /**
     * 响应头
     *
     * @var array
     */
    protected $arrHeader = [];

    /**
     * 响应类型
     *
     * @var string
     */
    protected $strContentType = 'text/html';

    /**
     * 字符编码
     *
     * @var string
     */
    protected $strCharset = 'utf-8';

    /**
     * 响应类型
     *
     * @var string
     */
    protected $strResponseType = 'default';

    /**
     * json 配置
     *
     * @var array
     */
    protected static $arrJsonOption = [
        'json_callback' => '',
        'json_options' => JSON_UNESCAPED_UNICODE
    ];

    /**
     * 配置
     *
     * @var array
     */
    protected $arrOption = [
        'action_fail' => 'public+fail',
        'action_success' => 'public+success',
        'default_response' => 'default'
    ];

    /**
     * 构造函数
     *
     * @param \queryyetsimple\router\router $objRouter
     * @param \queryyetsimple\mvc\iview $objView
     * @param \queryyetsimple\session\isession $objSession
     * @param \queryyetsimple\cookie\icookie $objCookie
     * @param array $arrOption
     * @return void
     */
    public function __construct(router $objRouter, iview $objView, isession $objSession, icookie $objCookie, array $arrOption = [])
    {
        $this->objRouter = $objRouter;
        $this->objView = $objView;
        $this->objSession = $objSession;
        $this->objCookie = $objCookie;
        $this->options($arrOption);
    }

    /**
     * 创建一个响应
     *
     * @param mixed $mixData
     * @param int $intCode
     * @param string $strMessage
     * @param array $arrHeader
     * @param array $arrOption
     * @return $this
     */
    public function make($mixData = '', $intCode = 200, $strMessage = '', array $arrHeader = [], $arrOption = [])
    {
        return $this->data($mixData)->code(intval($intCode))->message($strMessage)->headers($arrHeader)->options($arrOption);
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        if ($this->placeholderFlowControl($sMethod)) {
            return $this;
        }

        // 调用 trait __call 实现扩展方法
        $mixData = $this->infinityCall($sMethod, $arrArgs);
        if ($mixData instanceof response) {
            return $mixData;
        } else {
            return $this->data($mixData);
        }
    }

    /**
     * 输出内容
     *
     * @param boolean $booSend
     * @return void
     */
    public function output($booSend = true)
    {
        // 组装编码
        if ($booSend === true) {
            $this->contentTypeAndCharset($this->getContentType(), $this->getrCharset());
        }

        // 发送头部 header
        if ($booSend === true && ! headers_sent() && ! empty($this->arrHeader)) {
            http_response_code($this->intCode);
            foreach ($this->arrHeader as $strName => $strValue) {
                header($strName . ':' . $strValue);
            }
        }

        // 输出内容
        $sContent = $this->getContent() . (! $this->getContent() || ! $this->isJson($this->getContent()) ? $this->getAppendContent() : '');
        if ($booSend === true) {
            echo $sContent;
        } else {
            return $sContent;
        }

        // 提高响应速速
        if ($booSend === true && function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * 设置头部参数
     *
     * @param string $strName
     * @param string $strValue
     * @return $this
     */
    public function header($strName, $strValue)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrHeader[$strName] = $strValue;
        return $this;
    }

    /**
     * 批量设置头部参数
     *
     * @param array $arrHeader
     * @return $this
     */
    public function headers($arrHeader)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->arrHeader = array_merge($this->arrHeader, $arrHeader);
        return $this;
    }

    /**
     * 返回头部参数
     *
     * @param string $strHeaderName
     * @return mixed
     */
    public function getHeader($strHeaderName = null)
    {
        if (is_null($strHeaderName)) {
            return $this->arrHeader;
        } else {
            return $this->arrHeader[$strHeaderName] ?? null;
        }
    }

    /**
     * 修改单个配置
     *
     * @param string $strName
     * @param mixed $mixValue
     * @return $this
     */
    public function option($strName, $mixValue)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        return $this->infinityOption($strName, $mixValue);
    }

    /**
     * 修改多个配置
     *
     * @param string $strName
     * @param mixed $mixValue
     * @return $this
     */
    public function options($arrOption)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        return $this->infinityOptions($arrOption);
    }

    /**
     * 设置响应 cookie
     *
     * @param string $sName
     * @param mixed $mixValue
     * @param array $arrOption
     * @return $this
     */
    public function withCookie($sName, $mixValue = '', array $arrOption = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->objCookie->set($sName, $mixValue, $arrOption);
        return $this;
    }

    /**
     * 批量设置响应 cookie
     *
     * @param array $arrCookie
     * @param array $arrOption
     * @return $this
     */
    public function withCookies(array $arrCookie, array $arrOption = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        foreach ($arrCookie as $sName => $mixValue) {
            $this->objCookie->set($sName, $mixValue, $arrOption);
        }
        return $this;
    }

    /**
     * 闪存消息
     *
     * @param string $mixFlash
     * @param mixed $mixValue
     * @return $this
     */
    public function with($strFlash, $mixValue)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->objSession->flash($strFlash, $mixValue);
        return $this;
    }

    /**
     * 批量闪存消息
     *
     * @param array $$arrFlash
     * @param mixed $mixValue
     * @return $this
     */
    public function withs(array $arrFlash)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->objSession->flashs($arrFlash);
        return $this;
    }

    /**
     * 闪存错误信息
     *
     * @param array $arrErrors
     * @return $this
     */
    public function withErrors(array $arrErrors)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->objSession->flash('errors', array_merge($this->objSession->getFlash('errors', []), $arrErrors));
        return $this;
    }

    /**
     * 清理错误信息
     *
     * @return $this
     */
    public function clearErrors()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->objSession->flash('errors', []);
        return $this;
    }

    /**
     * 闪存输入信息
     *
     * @param array $arrInputs
     * @return $this
     */
    public function withInputs(array $arrInputs)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->objSession->flash('inputs', array_merge($this->objSession->getFlash('inputs', []), $arrInputs));
        return $this;
    }

    /**
     * 设置原始数据
     *
     * @param mixed $mixData
     * @return $this
     */
    public function data($mixData)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->mixData = $mixData;
        return $this;
    }

    /**
     * 返回原始数据
     *
     * @return $this
     */
    public function getData()
    {
        return $this->mixData;
    }

    /**
     * 响应状态
     *
     * @param int $intCode
     * @return $this
     */
    public function code($intCode)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->intCode = intval($intCode);
        return $this;
    }

    /**
     * 返回响应状态
     *
     * @return number
     */
    public function getCode()
    {
        return $this->intCode;
    }

    /**
     * 消息内容
     *
     * @param string $strMessage
     * @return $this
     */
    public function message($strMessage)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->strMessage = $strMessage;
        return $this;
    }

    /**
     * 返回消息内容
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->strMessage;
    }

    /**
     * contentType
     *
     * @param string $strContentType
     * @return $this
     */
    public function contentType($strContentType)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->strContentType = $strContentType;
        return $this;
    }

    /**
     * 返回 contentType
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->strContentType;
    }

    /**
     * 编码设置
     *
     * @param string $strCharset
     * @return $this
     */
    public function charset($strCharset)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->strCharset = $strCharset;
        return $this;
    }

    /**
     * 获取编码
     *
     * @return string
     */
    public function getrCharset()
    {
        return $this->strCharset;
    }

    /**
     * 设置内容
     *
     * @param string $strContent
     * @return $this
     */
    public function content($strContent)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->strContent = $strContent;
        return $this;
    }

    /**
     * 追加内容
     *
     * @param string $strContent
     * @return $this
     */
    public function appendContent($strContent)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->strAppendContent .= $strContent;
        return $this;
    }

    /**
     * 返回追加内容
     *
     * @return string
     */
    public function getAppendContent()
    {
        return $this->strAppendContent;
    }

    /**
     * 清理追加内容
     *
     * @return $this
     */
    public function restAppendContent()
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->strAppendContent = '';
        return $this;
    }

    /**
     * 解析并且返回内容
     *
     * @return string
     */
    public function getContent()
    {
        if (! is_null($this->strContent)) {
            return $this->strContent;
        }

        $mixContent = $this->getData();
        switch ($this->getResponseType()) {
            case 'json':
                if ($this->isApi()) {
                    $mixContent = $this->api($mixContent, null, null, true);
                } else {
                    $mixContent = json_encode($mixContent, $this->getJsonOption()['json_options']);
                }
                if ($this->getJsonOption()['json_callback']) {
                    $mixContent = $this->getJsonOption()['json_callback'] . '(' . $mixContent . ');';
                }
                break;
            case 'xml':
                $mixContent = xml::serialize($mixContent);
                break;
            case 'file':
                ob_end_clean();
                $resFp = fopen($this->getOption('file_name'), 'rb');
                fpassthru($resFp);
                fclose($resFp);
                break;
            case 'redirect':
                $this->objRouter->redirect($this->getOption('redirect_url'), $this->getOption('option'));
                break;
            case 'view':
                $mixContent = $this->objView->display($this->getOption('file'), $this->getOption('option'));
                break;
            default:
                if (! is_string($mixContent) && is_callable($mixContent)) {
                    $mixTemp = call_user_func_array($mixContent, []);
                    if ($mixTemp !== null) {
                        $mixContent = $mixTemp;
                    }
                    unset($mixTemp);
                } elseif (is_array($mixContent)) {
                    if (! $this->isApi()) {
                        $mixContent = json_encode($mixContent, $this->getJsonOption()['json_options']);
                    }
                }
                $mixContent = $this->varString($mixContent);
                if ($this->isApi()) {
                    $mixContent = $this->api($mixContent, null, null, true);
                }
                break;
        }
        $this->content($mixContent);
        unset($mixContent);

        return $this->strContent;
    }

    /**
     * api 接口形式
     *
     * @param mixed $mixContent
     * @param int|null $intCode
     * @param string|null $mixMessage
     * @param boolean $booReturn
     * @return json|$this mixed
     */
    public function api($mixContent = [], $intCode = null, $mixMessage = null, $booReturn = false)
    {
        $mixContent = $this->varString($mixContent);

        if (is_null($intCode)) {
            if (is_array($mixContent) && isset($mixContent['code'])) {
                $intCode = $mixContent['code'];
                unset($mixContent['code']);
            } else {
                $intCode = $this->getCode();
            }
        }

        if (is_null($mixMessage)) {
            if (is_array($mixContent) && isset($mixContent['message'])) {
                $mixMessage = $mixContent['message'];
                unset($mixContent['message']);
            } else {
                $mixMessage = $this->getMessage();
            }
        }

        list($mixMessage, $strKey) = is_array($mixMessage) ? $mixMessage : (strpos($mixMessage, '\@') !== false ? explode('\@', $mixMessage) : [
            $mixMessage,
            ''
        ]);

        $strReturn = json_encode([
            // 反码状态
            'code' => $intCode,

            // 描述信息
            'message' => $mixMessage,

            // 描述信息英文
            'key' => $strKey,

            // 响应时间
            'time' => time(),

            // 数据
            'data' => is_array($mixContent) ? $mixContent : [
                'content' => $mixContent
            ]
        ], $this->getJsonOption()['json_options']);

        if ($booReturn === true) {
            return $strReturn;
        } else {
            $this->content($strReturn);
            unset($strReturn);
            return $this;
        }
    }

    /**
     * api error
     *
     * @param string|null $mixMessage
     * @param mixed $mixContent
     * @param int|null $intCode
     * @return $this
     */
    public function apiError($mixMessage = null, $mixContent = [], $intCode = 400)
    {
        return $this->api($mixContent, $intCode, $mixMessage, false);
    }

    /**
     * api success
     *
     * @param string|null $mixMessage
     * @param mixed $mixContent
     * @param int|null $intCode
     * @return $this
     */
    public function apiSuccess($mixMessage = null, $mixContent = [], $intCode = 200)
    {
        return $this->api($mixContent, $intCode, $mixMessage, false);
    }

    /**
     * 判断是否 api 模式
     *
     * @return boolean
     */
    public function isApi()
    {
        return $this->getOption('default_response') == 'api';
    }

    /**
     * 返回 JSON 配置
     *
     * @return array
     */
    public function getJsonOption()
    {
        return array_merge(static::$arrJsonOption, $this->getOptions());
    }

    /**
     * 设置相应类型
     *
     * @param string $strResponseType
     * @return $this
     */
    public function responseType($strResponseType)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->strResponseType = $strResponseType;
        return $this;
    }

    /**
     * 返回相应类型
     *
     * @return string
     */
    public function getResponseType()
    {
        return $this->strResponseType;
    }

    /**
     * jsonp
     *
     * @param array $arrData
     * @param int $intOptions
     * @param string $strCharset
     * @return $this
     */
    public function json($arrData = null, $intOptions = JSON_UNESCAPED_UNICODE, $strCharset = 'utf-8')
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if (is_array($arrData)) {
            $this->data($arrData);
        }

        $this->

        responseType('json')->

        contentType('application/json')->

        charset($strCharset)->

        option('json_options', $intOptions);
        
        return $this;
    }

    /**
     * json callback
     *
     * @param string $strJsonCallback
     * @return $this
     */
    public function jsonCallback($strJsonCallback)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        return $this->option('json_callback', $strJsonCallback);
    }

    /**
     * jsonp
     *
     * @param string $strJsonCallback
     * @param array $arrData
     * @param int $intOptions
     * @param string $strCharset
     * @return $this
     */
    public function jsonp($strJsonCallback, $arrData = null, $intOptions = JSON_UNESCAPED_UNICODE, $strCharset = 'utf-8')
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        return $this->

        jsonCallback($strJsonCallback)->

        json($arrData, $intOptions, $strCharset);
    }

    /**
     * view 加载视图文件
     *
     * @param string $sFile
     * @param array $arrOption
     * @sub string charset 编码
     * @sub string content_type 内容类型
     * @return void|string
     */
    public function view($sFile = '', $arrOption = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        if (! empty($arrOption['charset'])) {
            $this->charset($arrOption['charset']);
        }
        if (! empty($arrOption['content_type'])) {
            $this->contentType($arrOption['content_type']);
        }

        return $this->responseType('view')->

        option('file', $sFile)->

        option('option', $arrOption)->

        assign($arrOption)->

        message($arrOption['message'] ?? '')->

        header('Cache-control', 'protected');
    }

    /**
     * view 变量赋值
     *
     * @param mixed $mixName
     * @param mixed $mixValue
     * @return $this
     */
    public function assign($mixName, $mixValue = null)
    {
        if ($this->checkFlowControl()) {
            return $this;
        }
        $this->objView->assign($mixName, $mixValue);
        return $this;
    }

    /**
     * 正确返回消息
     *
     * @param string $sMessage 消息
     * @param array $arrOption
     * @sub string charset 编码
     * @sub string content_type 内容类型
     * @sub string url 跳转 url 地址
     * @sub int time 停留时间
     * @return void|string
     */
    public function viewSuccess($sMessage = '', $arrOption = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $arrOption = array_merge([
            'message' => $sMessage ?  : 'Succeed',
            'url' => '',
            'time' => 1
        ], $arrOption);

        return $this->view($this->getOption('action_success'), $arrOption);
    }

    /**
     * 错误返回消息
     *
     * @param string $sMessage 消息
     * @param array $arrOption
     * @sub string charset 编码
     * @sub string content_type 内容类型
     * @sub string url 跳转 url 地址
     * @sub int time 停留时间
     * @return void|string
     */
    public function viewError($sMessage = '', $arrOption = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $arrOption = array_merge([
            'message' => $sMessage ?  : 'Failed',
            'url' => '',
            'time' => 3
        ], $arrOption);

        return $this->view($this->getOption('action_fail'), $arrOption);
    }

    /**
     * 路由 URL 跳转
     *
     * @param string $sUrl
     * @param array $arrOption
     * @sub string make 是否使用 url 生成地址
     * @sub string params url 额外参数
     * @sub string message 消息
     * @sub int time 停留时间，0表示不停留
     * @return void
     */
    public function redirect($sUrl, $arrOption = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        assert::string($sUrl);

        return $this->

        responseType('redirect')->

        option('redirect_url', $sUrl)->

        option('option', $arrOption);
    }

    /**
     * xml
     *
     * @param mixed $arrData
     * @param string $strCharset
     * @return $this
     */
    public function xml($arrData = null, $strCharset = 'utf-8')
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (is_array($arrData)) {
            $this->data($arrData);
        }

        return $this->responseType('xml')->

        contentType('text/xml')->

        charset($strCharset);
    }

    /**
     * 下载文件
     *
     * @param string $sFileName
     * @param string $sDownName
     * @param array $arrHeader
     * @return $this
     */
    public function download($sFileName, $sDownName = '', array $arrHeader = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (! $sDownName) {
            $sDownName = basename($sFileName);
        } else {
            $sDownName = $sDownName . '.' . fso::getExtension($sFileName);
        }

        return $this->

        downloadAndFile($sFileName, $arrHeader)->

        header('Content-Disposition', 'attachment;filename=' . $sDownName);
    }

    /**
     * 读取文件
     *
     * @param string $sFileName
     * @param array $arrHeader
     * @return $this
     */
    public function file($sFileName, array $arrHeader = [])
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        return $this->

        downloadAndFile($sFileName, $arrHeader)->

        header('Content-Disposition', 'inline;filename=' . basename($sFileName));
    }

    /**
     * 页面输出类型
     *
     * @param string $strContentType
     * @param string $strCharset
     * @return $this
     */
    protected function contentTypeAndCharset($strContentType, $strCharset = 'utf-8')
    {
        return $this->header('Content-Type', $strContentType . '; charset=' . $strCharset);
    }

    /**
     * 下载或者读取文件
     *
     * @param string $sFileName
     * @param array $arrHeader
     * @return $this
     */
    protected function downloadAndFile($sFileName, array $arrHeader = [])
    {
        if (! is_file($sFileName)) {
            throw new InvalidArgumentException(sprintf('File %s does not exist.', $sFileName));
        }
        $sFileName = realpath($sFileName);

        // 读取类型
        $resFinfo = finfo_open(FILEINFO_MIME);
        $strMimeType = finfo_file($resFinfo, $sFileName);
        finfo_close($resFinfo);

        $arrHeader = array_merge([
            'Cache-control' => 'max-age=31536000',
            'Content-Encoding' => 'none',
            'Content-type' => $strMimeType,
            'Content-Length' => filesize($sFileName)
        ], $arrHeader);

        $this->responseType('file')->

        headers($arrHeader)->

        option('file_name', $sFileName);

        return $this;
    }

    /**
     * PHP 变量转为字符串
     *
     * @param mixed $mixVar
     * @return string
     */
    protected function varString($mixVar)
    {
        if (! is_scalar($mixVar) && ! is_array($mixVar)) {
            ob_start();
            print_r($mixVar);
            $mixVar = ob_get_contents();
            ob_end_clean();
        }
        return $mixVar;
    }

    /**
     * 验证是否为正常的 JSON 字符串
     *
     * @param mixed $mixData
     * @return boolean
     */
    protected function isJson($mixData)
    {
        if (! is_scalar($mixData) && ! method_exists($mixData, '__toString')) {
            return false;
        }

        json_decode($mixData);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
