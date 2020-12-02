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

namespace Leevel\Mail;

use Closure;
use Leevel\Event\IDispatch;
use Leevel\Router\IView;
use Swift_Attachment;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Swift_Mime_Attachment;

/**
 * 邮件抽象类.
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
 * @method static \Swift_Message setDate(\DateTimeInterface $dateTime)                                设置创建此邮件的日期.
 * @method static \DateTimeInterface getDate()                                                        获取创建此邮件的日期.
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
 * @method static string embed(\Swift_Mime_SimpleMimeEntity $entity)                                  添加 {@link \Swift_Mime_SimpleMimeEntity}，然后返回它的 CID 源.
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
     */
    protected Swift_Mailer $swiftMailer;

    /**
     * 视图.
     */
    protected IView $view;

    /**
     * 事件处理器.
     */
    protected ?IDispatch $dispatch = null;

    /**
     * 邮件错误消息.
     */
    protected array $failedRecipients = [];

    /**
     * 消息.
     */
    protected ?Swift_Message $message = null;

    /**
     * 消息配置.
     */
    protected array $messageData = [
        'html'  => [],
        'plain' => [],
    ];

    /**
     * 配置.
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
     */
    public function __construct(IView $view, ?IDispatch $dispatch = null, array $option = [])
    {
        $this->view = $view;
        $this->dispatch = $dispatch;
        $this->option = array_merge($this->option, $option);
        $this->swiftMailer();
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        $transport = $this->swiftMailer->getTransport();
        if (method_exists($transport, $method)) {
            return $transport->{$method}(...$args);
        }

        return $this->makeMessage()->{$method}(...$args);
    }

    /**
     * {@inheritdoc}
     */
    public function setGlobalFrom(string $address, ?string $name = null): IMail
    {
        $this->option['global_from'] = compact('address', 'name');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setGlobalTo(string $address, ?string $name = null): IMail
    {
        $this->option['global_to'] = compact('address', 'name');

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function html(string $content): IMail
    {
        $this->messageData['html'][] = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function plain(string $content): IMail
    {
        $this->messageData['plain'][] = $content;

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function message(Closure $callbacks): IMail
    {
        $this->callbackMessage($callbacks, $this->makeMessage());

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function attachView(string $file): string
    {
        $this->makeMessage();

        return $this->message->embed(Swift_Image::fromPath($file));
    }

    /**
     * {@inheritdoc}
     */
    public function attachDataView(string $data, string $name, ?string $contentType = null): string
    {
        $this->makeMessage();

        return $this->message->embed(
            new Swift_Image($data, $name, $contentType)
        );
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function failedRecipients(): array
    {
        return $this->failedRecipients;
    }

    /**
     * 创建 transport.
     */
    abstract protected function makeTransport(): object;

    /**
     * 事件派发.
     */
    protected function handleDispatch(Swift_Message $message): void
    {
        if ($this->dispatch) {
            $this->dispatch->handle(self::MAIL_EVENT, $message);
        }
    }

    /**
     * 试图渲染数据.
     */
    protected function getViewData(string $file, array $data): string
    {
        $this->view->clearVar();
        $this->view->setVar('mail', $this);
        $this->view->setVar($data);

        return $this->view->display($file, [], null);
    }

    /**
     * 解析邮件内容.
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
     */
    protected function sendMessage(Swift_Message $message): int
    {
        return $this->send($message, $this->failedRecipients);
    }

    /**
     * 创建消息对象.
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
     */
    protected function callbackMessage(Closure $callbacks, Swift_Message $message): mixed
    {
        return $callbacks($message, $this);
    }

    /**
     * 路径创建 Swift_Mime_Attachment.
     */
    protected function createPathAttachment(string $file): Swift_Mime_Attachment
    {
        return Swift_Attachment::fromPath($file);
    }

    /**
     * 内存内容创建 Swift_Attachment.
     */
    protected function createDataAttachment(string $data, string $name): Swift_Attachment
    {
        return new Swift_Attachment($data, $name);
    }

    /**
     * 邮件附件消息回调处理.
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
     */
    protected function swiftMailer(): Swift_Mailer
    {
        return $this->swiftMailer = new Swift_Mailer(
            $this->makeTransport()
        );
    }
}
