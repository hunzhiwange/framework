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

namespace Leevel\Mail;

use Closure;
use Leevel\Event\IDispatch;
use Leevel\Mvc\IView;
use Swift_Attachment;
use Swift_Image;
use Swift_Message;

/**
 * mail 存储.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 */
class Mail implements IMail
{
    /**
     * 连接驱动.
     *
     * @var \Leevel\Mail\IConnect
     */
    protected $connect;

    /**
     * 视图.
     *
     * @var \leevel\Mvc\IView
     */
    protected $view;

    /**
     * 事件处理器.
     *
     * @var null|\Leevel\Event\IDispatch
     */
    protected $dispatch;

    /**
     * 邮件错误消息.
     *
     * @var array
     */
    protected $failedRecipients = [];

    /**
     * 消息.
     *
     * @var \Leevel\Mail\Message
     */
    protected $message;

    /**
     * 消息配置.
     *
     * @var array
     */
    protected $messageData = [
        'html'  => [],
        'plain' => [],
    ];

    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'global_from' => [
            'address' => null,
            'name'    => null,
        ],
        'global_to' => [
            'address' => null,
            'name'    => null,
        ],
    ];

    /**
     * 构造函数.
     *
     * @param \Leevel\Mail\IConnect        $connect
     * @param \leevel\Mvc\IView            $view
     * @param null|\Leevel\Event\IDispatch $dispatch
     * @param array                        $option
     */
    public function __construct(IConnect $connect, IView $view, IDispatch $dispatch = null, array $option = [])
    {
        $this->connect = $connect;
        $this->view = $view;
        $this->dispatch = $dispatch;

        $this->option = array_merge($this->option, $option);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption(string $name, $value)
    {
        $this->option[$name] = $value;

        return $this;
    }

    /**
     * 设置邮件发送来源.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return $this
     */
    public function globalFrom($address, $name = null)
    {
        $this->setOption('global_from', compact('address', 'name'));

        return $this;
    }

    /**
     * 设置邮件发送地址
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return $this
     */
    public function globalTo($address, $name = null)
    {
        $this->setOption('global_to', compact('address', 'name'));

        return $this;
    }

    /**
     * 视图 html 邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return $this
     */
    public function view($file, array $data = [])
    {
        $this->messageData['html'][] = [
            'file' => $file,
            'data' => $data,
        ];

        return $this;
    }

    /**
     * html 邮件内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function html($content)
    {
        $this->messageData['html'][] = $content;

        return $this;
    }

    /**
     * 纯文本邮件内容.
     *
     * @param string $content
     *
     * @return $this
     */
    public function plain($content)
    {
        $this->messageData['plain'][] = $content;

        return $this;
    }

    /**
     * 视图纯文本邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return $this
     */
    public function viewPlain($file, array $data = [])
    {
        $this->messageData['plain'][] = [
            'file' => $file,
            'data' => $data,
        ];

        return $this;
    }

    /**
     * 消息回调处理.
     *
     * @param \Closure $callbacks
     *
     * @return $this
     */
    public function message(Closure $callbacks)
    {
        $this->callbackMessage($callbacks, $this->makeMessage());

        return $this;
    }

    /**
     * 添加附件.
     *
     * @param string        $file
     * @param null|\Closure $callbacks
     *
     * @return $this
     */
    public function attach($file, Closure $callbacks = null)
    {
        $this->makeMessage();

        return $this->callbackAttachment(
            $this->createPathAttachment($file),
            $callbacks
        );
    }

    /**
     * 添加内存内容附件
     * file_get_content(path).
     *
     * @param string        $data
     * @param string        $name
     * @param null|\Closure $callbacks
     *
     * @return $this
     */
    public function attachData($data, $name, Closure $callbacks = null)
    {
        $this->makeMessage();

        return $this->callbackAttachment(
            $this->createDataAttachment($data, $name),
            $callbacks
        );
    }

    /**
     * 图片嵌入邮件.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachView($file)
    {
        $this->makeMessage();

        return $this->message->embed(Swift_Image::fromPath($file));
    }

    /**
     * 内存内容图片嵌入邮件.
     *
     * @param string      $data
     * @param string      $name
     * @param null|string $contentType
     *
     * @return string
     */
    public function attachDataView($data, $name, $contentType = null)
    {
        $this->makeMessage();

        return $this->message->embed(
            new Swift_Image($data, $name, $contentType)
        );
    }

    /**
     * 格式化中文附件名字.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachChinese($file)
    {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        if ($ext) {
            $file = substr($file, 0, strrpos($file, '.'.$ext));
        }

        return '=?UTF-8?B?'.base64_encode($file).'?='.($ext ? '.'.$ext : '');
    }

    /**
     * 发送邮件.
     *
     * @param \Closure $callbacks
     * @param bool     $htmlPriority
     *
     * @return int
     */
    public function send(Closure $callbacks = null, bool $htmlPriority = true)
    {
        $this->makeMessage();

        $this->parseMailContent($htmlPriority);

        if ($callbacks) {
            $this->message($callbacks);
        }

        if (!empty($this->option['global_to']['address'])) {
            $this->message->addTo(
                $this->option['global_to']['address'],
                $this->option['global_to']['name']
            );
        }

        $this->handleDispatch($this->message);

        return $this->sendMessage($this->message);
    }

    /**
     * 错误消息.
     *
     * @return array
     */
    public function failedRecipients()
    {
        return $this->failedRecipients;
    }

    /**
     * 事件派发.
     *
     * @param \Swift_Message $message
     */
    protected function handleDispatch(Swift_Message $message): void
    {
        if ($this->dispatch) {
            $this->dispatch->handle(self::MAIL_EVENT, $message);
        }
    }

    /**
     * 试图渲染数据.
     *
     * @param string $file
     * @param array  $data
     *
     * @return string
     */
    protected function getViewData($file, array $data)
    {
        return $this->view->
        clearAssign()->

        assign('mail', $this)->

        assign($data)->

        display($file, [], null);
    }

    /**
     * 解析邮件内容.
     *
     * @param bool $htmlPriority
     */
    protected function parseMailContent(bool $htmlPriority = true)
    {
        $findBody = false;

        $messageData = $this->messageData;

        if (!empty($messageData['html']) && !empty($messageData['plain'])) {
            unset($messageData[true === $htmlPriority ? 'plain' : 'html']);
        }

        if (!empty($messageData['html'])) {
            foreach ($messageData['html'] as $view) {
                if (false === $findBody) {
                    $method = 'setBody';
                    $findBody = true;
                } else {
                    $method = 'addPart';
                }

                $this->message->{$method}(
                    is_array($view) ?
                        $this->getViewData($view['file'], $view['data']) :
                        $view,
                    'text/html'
                );
            }
        }

        if (!empty($messageData['plain'])) {
            foreach ($messageData['plain'] as $view) {
                if (false === $findBody) {
                    $method = 'setBody';
                    $findBody = true;
                } else {
                    $method = 'addPart';
                }

                $this->message->{$method}(
                    is_array($view) ?
                        $this->getViewData($view['file'], $view['data']) :
                        $view,
                    'text/plain'
                );
            }
        }
    }

    /**
     * 发送消息对象
     *
     * @param \Swift_Message $message
     *
     * @return int
     */
    protected function sendMessage(Swift_Message $message)
    {
        return $this->connect->send($message, $this->failedRecipients);
    }

    /**
     * 创建消息对象
     *
     * @return \Swift_Message
     */
    protected function makeMessage()
    {
        if (null !== $this->message) {
            return $this->message;
        }

        $message = new Swift_Message();

        if (!empty($this->option['global_from']['address'])) {
            $message->setFrom(
                $this->option['global_from']['address'],
                $this->option['global_from']['name']
            );
        }

        return $this->message = $message;
    }

    /**
     * 邮件消息回调处理.
     *
     * @param \Closure       $callbacks
     * @param \Swift_Message $message
     *
     * @return mixed
     */
    protected function callbackMessage(Closure $callbacks, Swift_Message $message)
    {
        return $callbacks($message, $this);
    }

    /**
     * 路径创建 Swift_Attachment.
     *
     * @param string $file
     *
     * @return \Swift_Attachment
     */
    protected function createPathAttachment($file)
    {
        return Swift_Attachment::fromPath($file);
    }

    /**
     * 内存内容创建 Swift_Attachment.
     *
     * @param string $data
     * @param string $name
     *
     * @return \Swift_Attachment
     */
    protected function createDataAttachment($data, $name)
    {
        return new Swift_Attachment($data, $name);
    }

    /**
     * 邮件附件消息回调处理.
     *
     * @param \Swift_Attachment $attachment
     * @param null|\Closure     $callbacks
     *
     * @return $this
     */
    protected function callbackAttachment($attachment, Closure $callbacks = null)
    {
        if ($callbacks) {
            $callbacks($attachment, $this);
        }

        $this->message->attach($attachment);

        return $this;
    }
}
