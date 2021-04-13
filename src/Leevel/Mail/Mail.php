<?php

declare(strict_types=1);

namespace Leevel\Mail;

use Closure;
use Leevel\Event\IDispatch;
use Leevel\View\IView;
use Swift_Attachment;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Swift_Mime_Attachment;

/**
 * 邮件抽象类.
 *
 * @method static mixed isStarted() Test if this Transport mechanism has started. 
 * @method static mixed start() Start this Transport mechanism. 
 * @method static mixed stop() Stop this Transport mechanism. 
 * @method static mixed ping() Check if this Transport mechanism is alive. 
 * @method static mixed send(\Swift_Mime_SimpleMessage $message, &$failedRecipients = null) Send the given Message. 
 * @method static mixed registerPlugin(\Swift_Events_EventListener $plugin) Register a plugin in the Transport. 
 * @method static mixed addPart($body, $contentType = null, $charset = null) Add a MimePart to this Message. 
 * @method static mixed attachSigner(\Swift_Signer $signer) Attach a new signature handler to the message. 
 * @method static mixed detachSigner(\Swift_Signer $signer) Detach a signature handler from a message. 
 * @method static mixed clearSigners() Clear all signature handlers attached to the message. 
 * @method static mixed toString() Get this message as a complete string. 
 * @method static mixed toByteStream(\Swift_InputByteStream $is) Write this message to a {@link Swift_InputByteStream}. 
 * @method static mixed getNestingLevel() Always returns {@link LEVEL_TOP} for a message instance. 
 * @method static mixed setSubject($subject) Set the subject of this message. 
 * @method static mixed getSubject() Get the subject of this message. 
 * @method static mixed setDate(\DateTimeInterface $dateTime) Set the date at which this message was created. 
 * @method static mixed getDate() Get the date at which this message was created. 
 * @method static mixed setReturnPath($address) Set the return-path (the bounce address) of this message. 
 * @method static mixed getReturnPath() Get the return-path (bounce address) of this message. 
 * @method static mixed setSender($address, $name = null) Set the sender of this message. 
 * @method static mixed getSender() Get the sender of this message. 
 * @method static mixed addFrom($address, $name = null) Add a From: address to this message. 
 * @method static mixed setFrom($addresses, $name = null) Set the from address of this message. 
 * @method static mixed getFrom() Get the from address of this message. 
 * @method static mixed addReplyTo($address, $name = null) Add a Reply-To: address to this message. 
 * @method static mixed setReplyTo($addresses, $name = null) Set the reply-to address of this message. 
 * @method static mixed getReplyTo() Get the reply-to address of this message. 
 * @method static mixed addTo($address, $name = null) Add a To: address to this message. 
 * @method static mixed setTo($addresses, $name = null) Set the to addresses of this message. 
 * @method static mixed getTo() Get the To addresses of this message. 
 * @method static mixed addCc($address, $name = null) Add a Cc: address to this message. 
 * @method static mixed setCc($addresses, $name = null) Set the Cc addresses of this message. 
 * @method static mixed getCc() Get the Cc address of this message. 
 * @method static mixed addBcc($address, $name = null) Add a Bcc: address to this message. 
 * @method static mixed setBcc($addresses, $name = null) Set the Bcc addresses of this message. 
 * @method static mixed getBcc() Get the Bcc addresses of this message. 
 * @method static mixed setPriority($priority) Set the priority of this message. 
 * @method static mixed getPriority() Get the priority of this message. 
 * @method static mixed setReadReceiptTo($addresses) Ask for a delivery receipt from the recipient to be sent to $addresses. 
 * @method static mixed getReadReceiptTo() Get the addresses to which a read-receipt will be sent. 
 * @method static mixed attach(\Swift_Mime_SimpleMimeEntity $entity) Attach a {@link Swift_Mime_SimpleMimeEntity} such as an Attachment or MimePart. 
 * @method static mixed detach(\Swift_Mime_SimpleMimeEntity $entity) Remove an already attached entity. 
 * @method static mixed embed(\Swift_Mime_SimpleMimeEntity $entity) Attach a {@link Swift_Mime_SimpleMimeEntity} and return it's CID source. 
 * @method static mixed setBody($body, $contentType = null, $charset = null) Set the body of this entity, either as a string, or as an instance of {@link Swift_OutputByteStream}. 
 * @method static mixed getCharset() Get the character set of this entity. 
 * @method static mixed setCharset($charset) Set the character set of this entity. 
 * @method static mixed getFormat() Get the format of this entity (i.e. flowed or fixed). 
 * @method static mixed setFormat($format) Set the format of this entity (flowed or fixed). 
 * @method static mixed getDelSp() Test if delsp is being used for this entity. 
 * @method static mixed setDelSp($delsp = true) Turn delsp on or off for this entity. 
 * @method static mixed charsetChanged($charset) Receive notification that the charset has changed on this document, or a parent document. 
 * @method static mixed generateId() Generate a new Content-ID or Message-ID for this MIME entity. 
 * @method static mixed getHeaders() Get the {@link Swift_Mime_SimpleHeaderSet} for this entity. 
 * @method static mixed getContentType() Get the Content-type of this entity. 
 * @method static mixed getBodyContentType() Get the Body Content-type of this entity. 
 * @method static mixed setContentType($type) Set the Content-type of this entity. 
 * @method static mixed getId() Get the CID of this entity. 
 * @method static mixed setId($id) Set the CID of this entity. 
 * @method static mixed getDescription() Get the description of this entity. 
 * @method static mixed setDescription($description) Set the description of this entity. 
 * @method static mixed getMaxLineLength() Get the maximum line length of the body of this entity. 
 * @method static mixed setMaxLineLength($length) Set the maximum line length of lines in this body. 
 * @method static mixed getChildren() Get all children added to this entity. 
 * @method static mixed setChildren(array $children, $compoundLevel = null) Set all children of this entity. 
 * @method static mixed getBody() Get the body of this entity as a string. 
 * @method static mixed getEncoder() Get the encoder used for the body of this entity. 
 * @method static mixed setEncoder(\Swift_Mime_ContentEncoder $encoder) Set the encoder used for the body of this entity. 
 * @method static mixed getBoundary() Get the boundary used to separate children in this entity. 
 * @method static mixed setBoundary($boundary) Set the boundary used to separate children in this entity. 
 * @method static mixed encoderChanged(\Swift_Mime_ContentEncoder $encoder) Receive notification that the encoder of this entity or a parent entity has changed. 
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
     * {@inheritDoc}
     */
    public function setGlobalFrom(string $address, ?string $name = null): IMail
    {
        $this->option['global_from'] = compact('address', 'name');

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setGlobalTo(string $address, ?string $name = null): IMail
    {
        $this->option['global_to'] = compact('address', 'name');

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function html(string $content): IMail
    {
        $this->messageData['html'][] = $content;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function plain(string $content): IMail
    {
        $this->messageData['plain'][] = $content;

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function message(Closure $callbacks): IMail
    {
        $this->callbackMessage($callbacks, $this->makeMessage());

        return $this;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function attachView(string $file): string
    {
        $this->makeMessage();

        return $this->message->embed(Swift_Image::fromPath($file));
    }

    /**
     * {@inheritDoc}
     */
    public function attachDataView(string $data, string $name, ?string $contentType = null): string
    {
        $this->makeMessage();

        return $this->message->embed(
            new Swift_Image($data, $name, $contentType)
        );
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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

        return $this->view->display($file);
    }

    /**
     * 解析邮件内容.
     */
    protected function parseMailContent(bool $htmlPriority = true): void
    {
        $messageData = $this->messageData;
        $type = $htmlPriority && !empty($messageData['html']) ? 'html' : 'plain';
        if (empty($messageData[$type])) {
            return;
        }

        $findBody = false;
        foreach ($messageData[$type] as $view) {
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
                'text/'.$type
            );
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
