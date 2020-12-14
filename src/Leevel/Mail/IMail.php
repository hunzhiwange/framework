<?php

declare(strict_types=1);

namespace Leevel\Mail;

use Closure;

/**
 * 邮件接口.
 *
 * @see \Swift_Transport 接口参考，加入强类型
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
interface IMail
{
    /**
     * 邮件事件.
    */
    const MAIL_EVENT = 'mail.mail';

    /**
     * 设置邮件发送来源.
     */
    public function setGlobalFrom(string $address, ?string $name = null): self;

    /**
     * 设置邮件发送地址.
     */
    public function setGlobalTo(string $address, ?string $name = null): self;

    /**
     * 视图 HTML 邮件内容.
     */
    public function view(string $file, array $data = []): self;

    /**
     * HTML 邮件内容.
     */
    public function html(string $content): self;

    /**
     * 纯文本邮件内容.
     */
    public function plain(string $content): self;

    /**
     * 视图纯文本邮件内容.
     */
    public function viewPlain(string $file, array $data = []): self;

    /**
     * 消息回调处理.
     */
    public function message(Closure $callbacks): self;

    /**
     * 添加附件.
     */
    public function attachMail(string $file, ?Closure $callbacks = null): self;

    /**
     * 添加内存内容附件.
     *
     * - 本质上执行的是 bfile_get_content(path).
     */
    public function attachData(string $data, string $name, ?Closure $callbacks = null): self;

    /**
     * 图片嵌入邮件.
     */
    public function attachView(string $file): string;

    /**
     * 内存内容图片嵌入邮件.
     */
    public function attachDataView(string $data, string $name, ?string $contentType = null): string;

    /**
     * 格式化中文附件名字.
     */
    public function attachChinese(string $file): string;

    /**
     * 发送邮件.
     */
    public function flush(?Closure $callbacks = null, bool $htmlPriority = true): int;

    /**
     * 错误消息.
     */
    public function failedRecipients(): array;
}
