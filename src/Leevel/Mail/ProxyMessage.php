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

use DateTimeInterface;
use Swift_InputByteStream;
use Swift_Message;
use Swift_Mime_ContentEncoder;
use Swift_Mime_SimpleHeaderSet;
use Swift_Mime_SimpleMimeEntity;
use Swift_Signer;

/**
 * 消息代理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.12
 *
 * @version 1.0
 *
 * @see \Swift_Message
 */
trait ProxyMessage
{
    /**
     * 为此 mime 实体生成新的内容 ID 或消息 ID.
     *
     * @return string
     */
    public function generateId(): string
    {
        return $this->proxyMessage()->generateId();
    }

    /**
     * 获取实体的 {@link \Swift_Mime_SimpleHeaderSet}.
     *
     * @return \Swift_Mime_SimpleHeaderSet
     */
    public function getHeaders(): Swift_Mime_SimpleHeaderSet
    {
        return $this->proxyMessage()->getHeaders();
    }

    /**
     * 获取实体的 Content-type.
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->proxyMessage()->getContentType();
    }

    /**
     * 设置实体的 Content-type.
     *
     * @param string $type
     *
     * @return \Swift_Messages
     */
    public function setContentType(string $type): Swift_Message
    {
        return $this->proxyMessage()->setContentType($type);
    }

    /**
     * 获取此实体的 CID.
     *
     * 只有在存在 Content-ID 内容头的情况下，CID 才会出现在头中.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->proxyMessage()->getId();
    }

    /**
     * 设置此实体的 CID.
     *
     * @param string $id
     *
     * @return \Swift_Messages
     */
    public function setId(string $id): Swift_Message
    {
        return $this->proxyMessage()->setId($id);
    }

    /**
     * 获取此实体的说明.
     *
     * 此值来自 Content-Description 头，如果设置此字段.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->proxyMessage()->getDescription();
    }

    /**
     * 设置此实体的说明.
     *
     * 此方法在 Content-Description 中设置一个值.
     *
     * @param string $description
     *
     * @return \Swift_Messages
     */
    public function setDescription(string $description): Swift_Message
    {
        return $this->proxyMessage()->setDescription($description);
    }

    /**
     * 获取此正文行的最长字符长度.
     *
     * @return int
     */
    public function getMaxLineLength(): int
    {
        return $this->proxyMessage()->getMaxLineLength();
    }

    /**
     * 设置此正文行的最长字符长度.
     *
     * 虽然系统并未强制执行，最好不要超过 1000 个字符.
     *
     * @param int $length
     *
     * @return \Swift_Messages
     */
    public function setMaxLineLength(int $length): Swift_Message
    {
        return $this->proxyMessage()->setMaxLineLength($length);
    }

    /**
     * 获取此实体的所有子级.
     *
     * @return \Swift_Mime_SimpleMimeEntity[]
     */
    public function getChildren(): array
    {
        return $this->proxyMessage()->getChildren();
    }

    /**
     * 设置此实体的所有子级.
     *
     * @param \Swift_Mime_SimpleMimeEntity[] $children
     * @param int                            $compoundLevel
     *
     * @return \Swift_Messages
     */
    public function setChildren(array $children, ?int $compoundLevel = null): Swift_Message
    {
        return $this->proxyMessage()->setChildren($children, $compoundLevel);
    }

    /**
     * 获取此实体的正文为字符串.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->proxyMessage()->getBody();
    }

    /**
     * 获取用于此实体主体的编码器.
     *
     * @return \Swift_Mime_ContentEncoder
     */
    public function getEncoder(): Swift_Mime_ContentEncoder
    {
        return $this->proxyMessage()->getEncoder();
    }

    /**
     * 设置用于此实体主体的编码器.
     *
     * @param \Swift_Mime_ContentEncoder $encoder
     *
     * @return \Swift_Messages
     */
    public function setEncoder(Swift_Mime_ContentEncoder $encoder): Swift_Message
    {
        return $this->proxyMessage()->setEncoder($encoder);
    }

    /**
     * 获取用于分隔此实体中的子级的边界.
     *
     * @return string
     */
    public function getBoundary(): string
    {
        return $this->proxyMessage()->getBoundary();
    }

    /**
     * 设置用于分隔此实体中的子级的边界.
     *
     * @param string $boundary
     *
     * @throws \Swift_RfcComplianceException
     *
     * @return \Swift_Messages
     */
    public function setBoundary($boundary): Swift_Message
    {
        return $this->proxyMessage()->setBoundary($boundary);
    }

    /**
     * 收到有关此实体或者父实体的编码器已更改的通知.
     *
     * @param \Swift_Mime_ContentEncoder $encoder
     */
    public function encoderChanged(Swift_Mime_ContentEncoder $encoder): void
    {
        $this->proxyMessage()->encoderChanged($encoder);
    }

    /**
     * 设置此实体的主体为字符串或者 {@link \Swift_OutputByteStream}.
     *
     * @param string|\Swift_OutputByteStream $body
     * @param string                         $contentType
     * @param string                         $charset
     *
     * @return \Swift_Messages
     */
    public function setBody($body, ?string $contentType = null, ?string $charset = null): Swift_Message
    {
        return $this->proxyMessage()->setBody($body, $contentType, $charset);
    }

    /**
     * 获取实体的字符集.
     *
     * @return string
     */
    public function getCharset(): string
    {
        return $this->proxyMessage()->getCharset();
    }

    /**
     * 设置实体的字符集.
     *
     * @param string $charset
     *
     * @return \Swift_Message
     */
    public function setCharset(string $charset): Swift_Message
    {
        return $this->proxyMessage()->setCharset($charset);
    }

    /**
     * 获取此实体的格式 (例如 flowed 或 fixed).
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->proxyMessage()->getFormat();
    }

    /**
     * 设置此实体的格式 (例如 flowed 或 fixed).
     *
     * @param string $format
     *
     * @return \Swift_Message
     */
    public function setFormat(string $format): Swift_Message
    {
        return $this->proxyMessage()->setFormat($format);
    }

    /**
     * 测试是否正在使用 delsp.
     *
     * @return bool
     */
    public function getDelSp(): bool
    {
        return $this->proxyMessage()->getDelSp();
    }

    /**
     * 打开或关闭 Delsp.
     *
     * @param bool $delsp
     *
     * @return \Swift_Message
     */
    public function setDelSp(bool $delsp = true): Swift_Message
    {
        return $this->proxyMessage()->setDelSp($delsp);
    }

    /**
     * 收到有关此文档或者父文档的字符集已更改的通知.
     *
     * @param string $charset
     */
    public function charsetChanged(string $charset): void
    {
        $this->proxyMessage()->charsetChanged($charset);
    }

    /**
     * 始终返回 {@link \Swift_Mime_SimpleMimeEntity::LEVEL_TOP}.
     *
     * @return int
     */
    public function getNestingLevel(): int
    {
        return $this->proxyMessage()->getNestingLevel();
    }

    /**
     * 设置此邮件的主题.
     *
     * @param string $subject
     *
     * @return \Swift_Message
     */
    public function setSubject(string $subject): Swift_Message
    {
        return $this->proxyMessage()->setSubject($subject);
    }

    /**
     * 获取此邮件的主题.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->proxyMessage()->getSubject();
    }

    /**
     * 设置创建此邮件的日期
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return \Swift_Message
     */
    public function setDate(DateTimeInterface $dateTime): Swift_Message
    {
        return $this->proxyMessage()->setDate($dateTime);
    }

    /**
     * 获取创建此邮件的日期
     *
     * @return \DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->proxyMessage()->getDate();
    }

    /**
     * 设置此消息的退回路地址（邮件退回地址）.
     *
     * @param string $address
     *
     * @return \Swift_Message
     */
    public function setReturnPath(string $address): Swift_Message
    {
        return $this->proxyMessage()->setReturnPath($address);
    }

    /**
     * 获取此消息的退回路地址（邮件退回地址）.
     *
     * @return string
     */
    public function getReturnPath(): string
    {
        return $this->proxyMessage()->getReturnPath();
    }

    /**
     * 设置此邮件的发件人.
     *
     * 这不会覆盖 From 字段，但具有更高的重要性.
     *
     * @param string $address
     * @param string $name
     *
     * @return \Swift_Message
     */
    public function setSender(string $address, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->setSender($address, $name);
    }

    /**
     * 获取此邮件的发件人.
     *
     * @return string
     */
    public function getSender(): string
    {
        return $this->proxyMessage()->getSender();
    }

    /**
     * 在此邮件中添加发件人地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string $address
     * @param string $name
     *
     * @return \Swift_Message
     */
    public function addFrom(string $address, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->addFrom($address, $name);
    }

    /**
     * 设置此邮件的发件人地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param string       $name
     *
     * @example
     * $addresses = ['receiver@domain.org', 'other@domain.org' => 'A name']
     *
     * @return \Swift_Message
     */
    public function setFrom($addresses, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->setFrom($addresses, $name);
    }

    /**
     * 获取此邮件的发件人地址.
     *
     * @return mixed
     */
    public function getFrom()
    {
        return $this->proxyMessage()->getFrom();
    }

    /**
     * 添加答复邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string $address
     * @param string $name
     *
     * @return \Swift_Message
     */
    public function addReplyTo(string $address, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->addReplyTo($address, $name);
    }

    /**
     * 设置答复邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param string       $name
     *
     * @example
     * $addresses = ['receiver@domain.org', 'other@domain.org' => 'A name']
     *
     * @return \Swift_Message
     */
    public function setReplyTo($addresses, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->setReplyTo($addresses, $name);
    }

    /**
     * 获取答复邮件地址.
     *
     * @return string
     */
    public function getReplyTo(): string
    {
        return $this->proxyMessage()->getReplyTo();
    }

    /**
     * 添加发送邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string $address
     * @param string $name
     *
     * @return \Swift_Message
     */
    public function addTo(string $address, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->addTo($address, $name);
    }

    /**
     * 设置发送邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param string       $name
     *
     * @example
     * $addresses = ['receiver@domain.org', 'other@domain.org' => 'A name']
     *
     * @return \Swift_Message
     */
    public function setTo($addresses, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->setTo($addresses, $name);
    }

    /**
     * 获取发送邮件地址
     *
     * @return array
     */
    public function getTo(): array
    {
        return $this->proxyMessage()->getTo();
    }

    /**
     * 添加抄送邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string $address
     * @param string $name
     *
     * @return \Swift_Message
     */
    public function addCc(string $address, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->addCc($address, $name);
    }

    /**
     * 设置抄送邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param string       $name
     *
     * @return \Swift_Message
     */
    public function setCc($addresses, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->setCc($addresses, $name);
    }

    /**
     * 获取抄送邮件地址.
     *
     * @return array
     */
    public function getCc(): array
    {
        return $this->proxyMessage()->getCc();
    }

    /**
     * 添加密送邮件地址.
     *
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param string $address
     * @param string $name
     *
     * @return \Swift_Message
     */
    public function addBcc(string $address, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->addBcc($address, $name);
    }

    /**
     * 设置密送邮件地址.
     *
     * 如果此消息来自多个人，则可以传递地址数组.
     * 如果传递第二个参数名称，则此名称将与地址关联.
     *
     * @param array|string $addresses
     * @param string       $name
     *
     * @return \Swift_Message
     */
    public function setBcc($addresses, ?string $name = null): Swift_Message
    {
        return $this->proxyMessage()->setBcc($addresses, $name);
    }

    /**
     * 获取密送邮件地址.
     *
     * @return array
     */
    public function getBcc(): array
    {
        return $this->proxyMessage()->getBcc();
    }

    /**
     * 设置此邮件的优先级.
     *
     * 该值是一个整数，其中 1 是最高优先级，5 是最低优先级.
     *
     * @param int $priority
     *
     * @return \Swift_Message
     */
    public function setPriority(int $priority): Swift_Message
    {
        return $this->proxyMessage()->setPriority($priority);
    }

    /**
     * 获取此邮件的优先级.
     *
     * 该值是一个整数，其中 1 是最高优先级，5 是最低优先级.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->proxyMessage()->getPriority();
    }

    /**
     * 设置邮件回执地址.
     *
     * @param array $addresses
     *
     * @return \Swift_Message
     */
    public function setReadReceiptTo(array $addresses): Swift_Message
    {
        return $this->proxyMessage()->setReadReceiptTo($addresses);
    }

    /**
     * 获取邮件回执地址.
     *
     * @return string
     */
    public function getReadReceiptTo(): string
    {
        return $this->proxyMessage()->getReadReceiptTo();
    }

    /**
     * 添加 {@link \Swift_Mime_SimpleMimeEntity} 实体例如附件或者 Mime 部分.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return \Swift_Message
     */
    public function attach(Swift_Mime_SimpleMimeEntity $entity): Swift_Message
    {
        return $this->proxyMessage()->attach($entity);
    }

    /**
     * 删除添加的实体.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return \Swift_Message
     */
    public function detach(Swift_Mime_SimpleMimeEntity $entity): Swift_Message
    {
        return $this->proxyMessage()->detach($entity);
    }

    /**
     * 添加 {@link \Swift_Mime_SimpleMimeEntity}，然后返回它的 CID 源.
     * 在消息中嵌入图像或其他数据时，应使用此方法.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return string
     */
    public function embed(Swift_Mime_SimpleMimeEntity $entity): string
    {
        return $this->proxyMessage()->embed($entity);
    }

    /**
     * 在此邮件中添加一个 MIME 部件.
     *
     * @param string|\Swift_OutputByteStream $body
     * @param string                         $contentType
     * @param string                         $charset
     *
     * @return \Swift_Message
     */
    public function addPart($body, ?string $contentType = null, ?string $charset = null): Swift_Message
    {
        return $this->proxyMessage()->addPart($body, $contentType, $charset);
    }

    /**
     * 从消息中删除签名处理程序.
     *
     * @param \Swift_Signer $signer
     *
     * @return \Swift_Message
     */
    public function attachSigner(Swift_Signer $signer): Swift_Message
    {
        return $this->proxyMessage()->attachSigner($signer);
    }

    /**
     * 添加签名处理程序到消息中.
     *
     * @param \Swift_Signer $signer
     *
     * @return \Swift_Message
     */
    public function detachSigner(Swift_Signer $signer): Swift_Message
    {
        return $this->proxyMessage()->detachSigner($signer);
    }

    /**
     * 将此消息作为完整字符串获取.
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->proxyMessage()->toString();
    }

    /**
     * 将此消息写入 {@link \Swift_InputByteStream}.
     *
     * @param \Swift_InputByteStream $is
     */
    public function toByteStream(Swift_InputByteStream $is): void
    {
        $this->proxyMessage()->toByteStream($is);
    }
}
