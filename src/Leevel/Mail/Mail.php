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
use Leevel\Router\IView;
use Swift_Attachment;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport;

/**
 * mail 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 */
abstract class Mail implements IMail
{
    use Proxy;
    use ProxyMessage;

    /**
     * swift mailer.
     *
     * @var \Swift_Mailer
     */
    protected Swift_Mailer $swiftMailer;

    /**
     * 视图.
     *
     * @var \Leevel\Router\IView
     */
    protected IView $view;

    /**
     * 事件处理器.
     *
     * @var null|\Leevel\Event\IDispatch
     */
    protected ?IDispatch $dispatch = null;

    /**
     * 邮件错误消息.
     *
     * @var array
     */
    protected $failedRecipients = [];

    /**
     * 消息.
     *
     * @var \Swift_Message
     */
    protected ?Swift_Message $message = null;

    /**
     * 消息配置.
     *
     * @var array
     */
    protected array $messageData = [
        'html'  => [],
        'plain' => [],
    ];

    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
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
     * @param \Leevel\Router\IView         $view
     * @param null|\Leevel\Event\IDispatch $dispatch
     * @param array                        $option
     */
    public function __construct(IView $view, ?IDispatch $dispatch = null, array $option = [])
    {
        $this->view = $view;
        $this->dispatch = $dispatch;
        $this->option = array_merge($this->option, $option);

        $this->swiftMailer();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    public function __call(string $method, array $args)
    {
        $this->message->{$method}(...$args);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Mail\IMail
     */
    public function setOption(string $name, $value): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function globalFrom(string $address, ?string $name = null): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function globalTo(string $address, ?string $name = null): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function view(string $file, array $data = []): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function html(string $content): IMail
    {
        $this->messageData['html'][] = $content;

        return $this;
    }

    /**
     * 纯文本邮件内容.
     *
     * @param string $content
     *
     * @return \Leevel\Mail\IMail
     */
    public function plain(string $content): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function viewPlain(string $file, array $data = []): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function message(Closure $callbacks): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function attachMail(string $file, ?Closure $callbacks = null): IMail
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
     * @return \Leevel\Mail\IMail
     */
    public function attachData(string $data, string $name, ?Closure $callbacks = null): IMail
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
    public function attachView(string $file): string
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
    public function attachDataView(string $data, string $name, ?string $contentType = null): string
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
    public function attachChinese(string $file): string
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
     * @param null|\Closure $callbacks
     * @param bool          $htmlPriority
     *
     * @return int
     */
    public function flush(?Closure $callbacks = null, bool $htmlPriority = true): int
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
    public function failedRecipients(): array
    {
        return $this->failedRecipients;
    }

    /**
     * 返回代理.
     *
     * @return \Swift_Transport
     */
    public function proxy(): Swift_Transport
    {
        return $this->swiftMailer->getTransport();
    }

    /**
     * 返回代理.
     *
     * @return \Swift_Message
     * @codeCoverageIgnore
     */
    public function proxyMessage(): Swift_Message
    {
        return $this->makeMessage();
    }

    /**
     * 创建 transport.
     *
     * @return object
     */
    abstract protected function makeTransport(): object;

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
    protected function getViewData(string $file, array $data): string
    {
        return $this->view
            ->clearVar()
            ->setVar('mail', $this)
            ->setVar($data)
            ->display($file, [], null);
    }

    /**
     * 解析邮件内容.
     *
     * @param bool $htmlPriority
     */
    protected function parseMailContent(bool $htmlPriority = true): void
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
     * 发送消息对象.
     *
     * @param \Swift_Message $message
     *
     * @return int
     */
    protected function sendMessage(Swift_Message $message): int
    {
        return $this->send($message, $this->failedRecipients);
    }

    /**
     * 创建消息对象.
     *
     * @return \Swift_Message
     */
    protected function makeMessage(): Swift_Message
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
    protected function createPathAttachment(string $file): Swift_Attachment
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
    protected function createDataAttachment(string $data, string $name): Swift_Attachment
    {
        return new Swift_Attachment($data, $name);
    }

    /**
     * 邮件附件消息回调处理.
     *
     * @param \Swift_Attachment $attachment
     * @param null|\Closure     $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    protected function callbackAttachment(Swift_Attachment $attachment, ?Closure $callbacks = null): IMail
    {
        if ($callbacks) {
            $callbacks($attachment, $this);
        }

        $this->message->attach($attachment);

        return $this;
    }

    /**
     * 生成 Swift Mailer.
     *
     * @return \Swift_Mailer
     */
    protected function swiftMailer(): Swift_Mailer
    {
        return $this->swiftMailer = new Swift_Mailer(
            $this->makeTransport()
        );
    }
}
