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

/**
 * Mail 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 *
 * @method static bool isStarted()                                                                    传输机制是否已经启动.
 * @method static void start()                                                                        启动传输机制.
 * @method static void stop()                                                                         停止传输机制.
 * @method static bool ping()                                                                         检查此传输机制是否处于活动状态.
 * @method static int send(\Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null)      发送消息.
 * @method static void registerPlugin(\Swift_Events_EventListener $plugin)                            注册一个插件.
 * @method static string generateId()                                                                 为此 mime 实体生成新的内容 ID 或消息 ID.
 * @method static \Swift_Mime_SimpleHeaderSet getHeaders()                                            获取实体的 {@link \Swift_Mime_SimpleHeaderSet}.
 * @method static string getContentType()                                                             获取实体的 Content-type.
 * @method static \Swift_Message setContentType(string $type)                                         设置实体的 Content-type.
 * @method static string getId()                                                                      获取此实体的 CID.
 * @method static \Swift_Message setId(string $id)                                                    设置此实体的 CID.
 * @method static string getDescription()                                                             获取此实体的说明.
 * @method static \Swift_Message setDescription(string $description)                                  设置此实体的说明.
 * @method static int getMaxLineLength()                                                              获取此正文行的最长字符长度.
 * @method static \Swift_Message setMaxLineLength(int $length)                                        设置此正文行的最长字符长度.
 * @method static array getChildren()                                                                 获取此实体的所有子级.
 * @method static \Swift_Message setChildren(array $children, ?int $compoundLevel = null)             设置此实体的所有子级.
 * @method static string getBody()                                                                    获取此实体的正文为字符串.
 * @method static \Swift_Mime_ContentEncoder getEncoder()                                             获取用于此实体主体的编码器.
 * @method static \Swift_Message setEncoder(\Swift_Mime_ContentEncoder $encoder)                      设置用于此实体主体的编码器.
 * @method static string getBoundary()                                                                获取用于分隔此实体中的子级的边界.
 * @method static \Swift_Message setBoundary($boundary)                                               设置用于分隔此实体中的子级的边界.
 * @method static void encoderChanged(\Swift_Mime_ContentEncoder $encoder)                            收到有关此实体或者父实体的编码器已更改的通知.
 * @method static \Swift_Message setBody($body, ?string $contentType = null, ?string $charset = null) 设置此实体的主体为字符串或者 {@link \Swift_OutputByteStream}.
 * @method static string getCharset()                                                                 获取实体的字符集.
 * @method static \Swift_Message setCharset(string $charset)                                          设置实体的字符集.
 * @method static string getFormat()                                                                  获取此实体的格式 (例如 flowed 或 fixed).
 * @method static \Swift_Message setFormat(string $format)                                            设置此实体的格式 (例如 flowed 或 fixed).
 * @method static bool getDelSp()                                                                     测试是否正在使用 delsp.
 * @method static \Swift_Message setDelSp(bool $delsp = true)                                         打开或关闭 Delsp.
 * @method static void charsetChanged(string $charset)                                                收到有关此文档或者父文档的字符集已更改的通知.
 * @method static int getNestingLevel()                                                               始终返回 {@link \Swift_Mime_SimpleMimeEntity::LEVEL_TOP}.
 * @method static \Swift_Message setSubject(string $subject)                                          设置此邮件的主题.
 * @method static string getSubject()                                                                 获取此邮件的主题.
 * @method static \Swift_Message setDate(\DateTimeInterface $dateTime)                                设置创建此邮件的日期
 * @method static \DateTimeInterface getDate()                                                        获取创建此邮件的日期
 * @method static \Swift_Message setReturnPath(string $address)                                       设置此消息的退回路地址（邮件退回地址）.
 * @method static string getReturnPath()                                                              获取此消息的退回路地址（邮件退回地址）.
 * @method static \Swift_Message setSender(string $address, ?string $name = null)                     设置此邮件的发件人.
 * @method static string getSender()                                                                  获取此邮件的发件人.
 * @method static \Swift_Message addFrom(string $address, ?string $name = null)                       在此邮件中添加发件人地址.
 * @method static \Swift_Message setFrom($addresses, ?string $name = null)                            设置此邮件的发件人地址.
 * @method static getFrom()                                                                           获取此邮件的发件人地址.
 * @method static \Swift_Message addReplyTo(string $address, ?string $name = null)                    添加答复邮件地址.
 * @method static \Swift_Message setReplyTo($addresses, ?string $name = null)                         设置答复邮件地址.
 * @method static string getReplyTo()                                                                 获取答复邮件地址.
 * @method static \Swift_Message addTo(string $address, ?string $name = null)                         添加发送邮件地址.
 * @method static \Swift_Message setTo($addresses, ?string $name = null)                              设置发送邮件地址.
 * @method static array getTo()                                                                       获取发送邮件地址
 * @method static \Swift_Message addCc(string $address, ?string $name = null)                         添加抄送邮件地址.
 * @method static \Swift_Message setCc($addresses, ?string $name = null)                              设置抄送邮件地址.
 * @method static array getCc()                                                                       获取抄送邮件地址.
 * @method static \Swift_Message addBcc(string $address, ?string $name = null)                        添加密送邮件地址.
 * @method static \Swift_Message setBcc($addresses, ?string $name = null)                             设置密送邮件地址.
 * @method static array getBcc()                                                                      获取密送邮件地址.
 * @method static \Swift_Message setPriority(int $priority)                                           设置此邮件的优先级.
 * @method static int getPriority()                                                                   获取此邮件的优先级.
 * @method static \Swift_Message setReadReceiptTo(array $addresses)                                   设置邮件回执地址.
 * @method static string getReadReceiptTo()                                                           获取邮件回执地址.
 * @method static \Swift_Message attach(\Swift_Mime_SimpleMimeEntity $entity)                         添加 {@link \Swift_Mime_SimpleMimeEntity} 实体例如附件或者 Mime 部分.
 * @method static \Swift_Message detach(\Swift_Mime_SimpleMimeEntity $entity)                         删除添加的实体.
 * @method static string embed(\Swift_Mime_SimpleMimeEntity $entity)                                  添加 {@link \Swift_Mime_SimpleMimeEntity}，然后返回它的 CID 源. 在消息中嵌入图像或其他数据时，应使用此方法.
 * @method static \Swift_Message addPart($body, ?string $contentType = null, ?string $charset = null) 在此邮件中添加一个 MIME 部件.
 * @method static \Swift_Message attachSigner(\Swift_Signer $signer)                                  从消息中删除签名处理程序.
 * @method static \Swift_Message detachSigner(\Swift_Signer $signer)                                  添加签名处理程序到消息中.
 * @method static string toString()                                                                   将此消息作为完整字符串获取.
 * @method static void toByteStream(\Swift_InputByteStream $is)                                       将此消息写入 {@link \Swift_InputByteStream}.
 */
abstract class Mail implements IMail
{
    /**
     * swift mailer.
     *
     * @var \Swift_Mailer
     */
    protected $swiftMailer;

    /**
     * 视图.
     *
     * @var \Leevel\Router\IView
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
     * @var \Swift_Message
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
        $transport = $this->swiftMailer->getTransport();

        if (method_exists($transport, $method)) {
            return $transport->{$method}(...$args);
        }

        return $this->makeMessage()->{$method}(...$args);
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
