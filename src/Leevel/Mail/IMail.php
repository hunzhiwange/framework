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
use DateTimeInterface;
use Swift_Events_EventListener;
use Swift_InputByteStream;
use Swift_Message;
use Swift_Mime_ContentEncoder;
use Swift_Mime_SimpleHeaderSet;
use Swift_Mime_SimpleMessage;
use Swift_Mime_SimpleMimeEntity;
use Swift_Signer;

/**
 * IMail 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 *
 * @see \Swift_Transport 接口参考，加入强类型
 */
interface IMail
{
    /**
     * 邮件事件.
     *
     * @var string
     */
    const MAIL_EVENT = 'mail.mail';

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Mail\IMail
     */
    public function setOption(string $name, $value): self;

    /**
     * 设置邮件发送来源.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Leevel\Mail\IMail
     */
    public function globalFrom(string $address, ?string $name = null): self;

    /**
     * 设置邮件发送地址
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Leevel\Mail\IMail
     */
    public function globalTo(string $address, ?string $name = null): self;

    /**
     * 视图 html 邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return \Leevel\Mail\IMail
     */
    public function view(string $file, array $data = []): self;

    /**
     * html 邮件内容.
     *
     * @param string $content
     *
     * @return \Leevel\Mail\IMail
     */
    public function html(string $content): self;

    /**
     * 纯文本邮件内容.
     *
     * @param string $content
     *
     * @return \Leevel\Mail\IMail
     */
    public function plain(string $content): self;

    /**
     * 视图纯文本邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return \Leevel\Mail\IMail
     */
    public function viewPlain(string $file, array $data = []): self;

    /**
     * 消息回调处理.
     *
     * @param \Closure $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    public function message(Closure $callbacks): self;

    /**
     * 添加附件.
     *
     * @param string        $file
     * @param null|\Closure $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    public function attachMail(string $file, ?Closure $callbacks = null): self;

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
    public function attachData(string $data, string $name, ?Closure $callbacks = null): self;

    /**
     * 图片嵌入邮件.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachView(string $file): string;

    /**
     * 内存内容图片嵌入邮件.
     *
     * @param string      $data
     * @param string      $name
     * @param null|string $contentType
     *
     * @return string
     */
    public function attachDataView(string $data, string $name, ?string $contentType = null): string;

    /**
     * 格式化中文附件名字.
     *
     * @param string $file
     *
     * @return string
     */
    public function attachChinese(string $file): string;

    /**
     * 发送邮件.
     *
     * @param null|\Closure $callbacks
     * @param bool          $htmlPriority
     *
     * @return int
     */
    public function flush(?Closure $callbacks = null, bool $htmlPriority = true): int;

    /**
     * 错误消息.
     *
     * @return array
     */
    public function failedRecipients(): array;

    /**
     * 传输机制是否已经启动.
     *
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * 启动传输机制.
     */
    public function start(): void;

    /**
     * 停止传输机制.
     */
    public function stop(): void;

    /**
     * 检查此传输机制是否处于活动状态.
     *
     * @return bool
     */
    public function ping(): bool;

    /**
     * 发送消息.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @param null|array                $failedRecipients
     *
     * @return int
     */
    public function send(Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null): int;

    /**
     * 注册一个插件.
     *
     * @param \Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin): void;

    /**
     * 为此 mime 实体生成新的内容 ID 或消息 ID.
     *
     * @return string
     */
    public function generateId(): string;

    /**
     * 获取实体的 {@link \Swift_Mime_SimpleHeaderSet}.
     *
     * @return \Swift_Mime_SimpleHeaderSet
     */
    public function getHeaders(): Swift_Mime_SimpleHeaderSet;

    /**
     * 获取实体的 Content-type.
     *
     * @return string
     */
    public function getContentType(): string;

    /**
     * 设置实体的 Content-type.
     *
     * @param string $type
     *
     * @return \Swift_Messages
     */
    public function setContentType(string $type): Swift_Message;

    /**
     * 获取此实体的 CID.
     *
     * 只有在存在 Content-ID 内容头的情况下，CID 才会出现在头中.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * 设置此实体的 CID.
     *
     * @param string $id
     *
     * @return \Swift_Messages
     */
    public function setId(string $id): Swift_Message;

    /**
     * 获取此实体的说明.
     *
     * 此值来自 Content-Description 头，如果设置此字段.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * 设置此实体的说明.
     *
     * 此方法在 Content-Description 中设置一个值.
     *
     * @param string $description
     *
     * @return \Swift_Messages
     */
    public function setDescription(string $description): Swift_Message;

    /**
     * 获取此正文行的最长字符长度.
     *
     * @return int
     */
    public function getMaxLineLength(): int;

    /**
     * 设置此正文行的最长字符长度.
     *
     * 虽然系统并未强制执行，最好不要超过 1000 个字符.
     *
     * @param int $length
     *
     * @return \Swift_Messages
     */
    public function setMaxLineLength(int $length): Swift_Message;

    /**
     * 获取此实体的所有子级.
     *
     * @return \Swift_Mime_SimpleMimeEntity[]
     */
    public function getChildren(): array;

    /**
     * 设置此实体的所有子级.
     *
     * @param \Swift_Mime_SimpleMimeEntity[] $children
     * @param null|int                       $compoundLevel
     *
     * @return \Swift_Messages
     */
    public function setChildren(array $children, ?int $compoundLevel = null): Swift_Message;

    /**
     * 获取此实体的正文为字符串.
     *
     * @return string
     */
    public function getBody(): string;

    /**
     * 获取用于此实体主体的编码器.
     *
     * @return \Swift_Mime_ContentEncoder
     */
    public function getEncoder(): Swift_Mime_ContentEncoder;

    /**
     * 设置用于此实体主体的编码器.
     *
     * @param \Swift_Mime_ContentEncoder $encoder
     *
     * @return \Swift_Messages
     */
    public function setEncoder(Swift_Mime_ContentEncoder $encoder): Swift_Message;

    /**
     * 获取用于分隔此实体中的子级的边界.
     *
     * @return string
     */
    public function getBoundary(): string;

    /**
     * 设置用于分隔此实体中的子级的边界.
     *
     * @param string $boundary
     *
     * @throws \Swift_RfcComplianceException
     *
     * @return \Swift_Messages
     */
    public function setBoundary($boundary): Swift_Message;

    /**
     * 收到有关此实体或者父实体的编码器已更改的通知.
     *
     * @param \Swift_Mime_ContentEncoder $encoder
     */
    public function encoderChanged(Swift_Mime_ContentEncoder $encoder): void;

    /**
     * 设置此实体的主体为字符串或者 {@link \Swift_OutputByteStream}.
     *
     * @param string|\Swift_OutputByteStream $body
     * @param null|string                    $contentType
     * @param null|string                    $charset
     *
     * @return \Swift_Messages
     */
    public function setBody($body, ?string $contentType = null, ?string $charset = null): Swift_Message;

    /**
     * 获取实体的字符集.
     *
     * @return string
     */
    public function getCharset(): string;

    /**
     * 设置实体的字符集.
     *
     * @param string $charset
     *
     * @return \Swift_Message
     */
    public function setCharset(string $charset): Swift_Message;

    /**
     * 获取此实体的格式 (例如 flowed 或 fixed).
     *
     * @return string
     */
    public function getFormat(): string;

    /**
     * 设置此实体的格式 (例如 flowed 或 fixed).
     *
     * @param string $format
     *
     * @return \Swift_Message
     */
    public function setFormat(string $format): Swift_Message;

    /**
     * 测试是否正在使用 delsp.
     *
     * @return bool
     */
    public function getDelSp(): bool;

    /**
     * 打开或关闭 Delsp.
     *
     * @param bool $delsp
     *
     * @return \Swift_Message
     */
    public function setDelSp(bool $delsp = true): Swift_Message;

    /**
     * 收到有关此文档或者父文档的字符集已更改的通知.
     *
     * @param string $charset
     */
    public function charsetChanged(string $charset): void;

    /**
     * 始终返回 {@link \Swift_Mime_SimpleMimeEntity::LEVEL_TOP}.
     *
     * @return int
     */
    public function getNestingLevel(): int;

    /**
     * 设置此邮件的主题.
     *
     * @param string $subject
     *
     * @return \Swift_Message
     */
    public function setSubject(string $subject): Swift_Message;

    /**
     * 获取此邮件的主题.
     *
     * @return string
     */
    public function getSubject(): string;

    /**
     * 设置创建此邮件的日期
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return \Swift_Message
     */
    public function setDate(DateTimeInterface $dateTime): Swift_Message;

    /**
     * 获取创建此邮件的日期
     *
     * @return \DateTimeInterface
     */
    public function getDate(): DateTimeInterface;

    /**
     * 设置此消息的退回路地址（邮件退回地址）.
     *
     * @param string $address
     *
     * @return \Swift_Message
     */
    public function setReturnPath(string $address): Swift_Message;

    /**
     * 获取此消息的退回路地址（邮件退回地址）.
     *
     * @return string
     */
    public function getReturnPath(): string;

    /**
     * 设置此邮件的发件人.
     *
     * 这不会覆盖 From 字段，但具有更高的重要性.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Swift_Message
     */
    public function setSender(string $address, ?string $name = null): Swift_Message;

    /**
     * 获取此邮件的发件人.
     *
     * @return string
     */
    public function getSender(): string;

    /**
     * 在此邮件中添加发件人地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Swift_Message
     */
    public function addFrom(string $address, ?string $name = null): Swift_Message;

    /**
     * 设置此邮件的发件人地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param null|string  $name
     *
     * @example
     * $addresses = ['receiver@domain.org', 'other@domain.org' => 'A name']
     *
     * @return \Swift_Message
     */
    public function setFrom($addresses, ?string $name = null): Swift_Message;

    /**
     * 获取此邮件的发件人地址.
     *
     * @return mixed
     */
    public function getFrom();

    /**
     * 添加答复邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Swift_Message
     */
    public function addReplyTo(string $address, ?string $name = null): Swift_Message;

    /**
     * 设置答复邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param null|string  $name
     *
     * @example
     * $addresses = ['receiver@domain.org', 'other@domain.org' => 'A name']
     *
     * @return \Swift_Message
     */
    public function setReplyTo($addresses, ?string $name = null): Swift_Message;

    /**
     * 获取答复邮件地址.
     *
     * @return string
     */
    public function getReplyTo(): string;

    /**
     * 添加发送邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Swift_Message
     */
    public function addTo(string $address, ?string $name = null): Swift_Message;

    /**
     * 设置发送邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param null|string  $name
     *
     * @example
     * $addresses = ['receiver@domain.org', 'other@domain.org' => 'A name']
     *
     * @return \Swift_Message
     */
    public function setTo($addresses, ?string $name = null): Swift_Message;

    /**
     * 获取发送邮件地址
     *
     * @return array
     */
    public function getTo(): array;

    /**
     * 添加抄送邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Swift_Message
     */
    public function addCc(string $address, ?string $name = null): Swift_Message;

    /**
     * 设置抄送邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param null|string  $name
     *
     * @return \Swift_Message
     */
    public function setCc($addresses, ?string $name = null): Swift_Message;

    /**
     * 获取抄送邮件地址.
     *
     * @return array
     */
    public function getCc(): array;

    /**
     * 添加密送邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Swift_Message
     */
    public function addBcc(string $address, ?string $name = null): Swift_Message;

    /**
     * 设置密送邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param null|string  $name
     *
     * @return \Swift_Message
     */
    public function setBcc($addresses, ?string $name = null): Swift_Message;

    /**
     * 获取密送邮件地址.
     *
     * @return array
     */
    public function getBcc(): array;

    /**
     * 设置此邮件的优先级.
     *
     * 该值是一个整数，其中 1 是最高优先级，5 是最低优先级.
     *
     * @param int $priority
     *
     * @return \Swift_Message
     */
    public function setPriority(int $priority): Swift_Message;

    /**
     * 获取此邮件的优先级.
     *
     * 该值是一个整数，其中 1 是最高优先级，5 是最低优先级.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * 设置邮件回执地址.
     *
     * @param array $addresses
     *
     * @return \Swift_Message
     */
    public function setReadReceiptTo(array $addresses): Swift_Message;

    /**
     * 获取邮件回执地址.
     *
     * @return string
     */
    public function getReadReceiptTo(): string;

    /**
     * 添加 {@link \Swift_Mime_SimpleMimeEntity} 实体例如附件或者 Mime 部分.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return \Swift_Message
     */
    public function attach(Swift_Mime_SimpleMimeEntity $entity): Swift_Message;

    /**
     * 删除添加的实体.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return \Swift_Message
     */
    public function detach(Swift_Mime_SimpleMimeEntity $entity): Swift_Message;

    /**
     * 添加 {@link \Swift_Mime_SimpleMimeEntity}，然后返回它的 CID 源.
     * 在消息中嵌入图像或其他数据时，应使用此方法.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return string
     */
    public function embed(Swift_Mime_SimpleMimeEntity $entity): string;

    /**
     * 在此邮件中添加一个 MIME 部件.
     *
     * @param string|\Swift_OutputByteStream $body
     * @param null|string                    $contentType
     * @param null|string                    $charset
     *
     * @return \Swift_Message
     */
    public function addPart($body, ?string $contentType = null, ?string $charset = null): Swift_Message;

    /**
     * 从消息中删除签名处理程序.
     *
     * @param \Swift_Signer $signer
     *
     * @return \Swift_Message
     */
    public function attachSigner(Swift_Signer $signer): Swift_Message;

    /**
     * 添加签名处理程序到消息中.
     *
     * @param \Swift_Signer $signer
     *
     * @return \Swift_Message
     */
    public function detachSigner(Swift_Signer $signer): Swift_Message;

    /**
     * 将此消息作为完整字符串获取.
     *
     * @return string
     */
    public function toString(): string;

    /**
     * 将此消息写入 {@link \Swift_InputByteStream}.
     *
     * @param \Swift_InputByteStream $is
     */
    public function toByteStream(Swift_InputByteStream $is): void;
}
