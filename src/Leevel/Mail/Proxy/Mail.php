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

namespace Leevel\Mail\Proxy;

use Closure;
use DateTimeInterface;
use Leevel\Di\Container;
use Leevel\Mail\IMail as IBaseMail;
use Leevel\Mail\Manager;
use Swift_Events_EventListener;
use Swift_InputByteStream;
use Swift_Message;
use Swift_Mime_ContentEncoder;
use Swift_Mime_SimpleHeaderSet;
use Swift_Mime_SimpleMessage;
use Swift_Mime_SimpleMimeEntity;
use Swift_Signer;

/**
 * 代理 mail.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Mail implements IMail
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 设置配置.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Leevel\Mail\IMail
     */
    public static function setOption(string $name, $value): IBaseMail
    {
        return self::proxy()->setOption($name, $value);
    }

    /**
     * 设置邮件发送来源.
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Leevel\Mail\IMail
     */
    public static function globalFrom(string $address, ?string $name = null): IBaseMail
    {
        return self::proxy()->globalFrom($address, $name);
    }

    /**
     * 设置邮件发送地址
     *
     * @param string      $address
     * @param null|string $name
     *
     * @return \Leevel\Mail\IMail
     */
    public static function globalTo(string $address, ?string $name = null): IBaseMail
    {
        return self::proxy()->globalTo($address, $name);
    }

    /**
     * 视图 html 邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return \Leevel\Mail\IMail
     */
    public static function view(string $file, array $data = []): IBaseMail
    {
        return self::proxy()->view($file, $data);
    }

    /**
     * html 邮件内容.
     *
     * @param string $content
     *
     * @return \Leevel\Mail\IMail
     */
    public static function html(string $content): IBaseMail
    {
        return self::proxy()->html($content);
    }

    /**
     * 纯文本邮件内容.
     *
     * @param string $content
     *
     * @return \Leevel\Mail\IMail
     */
    public static function plain(string $content): IBaseMail
    {
        return self::proxy()->plain($content);
    }

    /**
     * 视图纯文本邮件内容.
     *
     * @param string $file
     * @param array  $data
     *
     * @return \Leevel\Mail\IMail
     */
    public static function viewPlain(string $file, array $data = []): IBaseMail
    {
        return self::proxy()->viewPlain($file, $data);
    }

    /**
     * 消息回调处理.
     *
     * @param \Closure $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    public static function message(Closure $callbacks): IBaseMail
    {
        return self::proxy()->message($callbacks);
    }

    /**
     * 添加附件.
     *
     * @param string        $file
     * @param null|\Closure $callbacks
     *
     * @return \Leevel\Mail\IMail
     */
    public static function attachMail(string $file, ?Closure $callbacks = null): IBaseMail
    {
        return self::proxy()->attachMail($file, $callbacks);
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
    public static function attachData(string $data, string $name, ?Closure $callbacks = null): IBaseMail
    {
        return self::proxy()->attachData($data, $name, $callbacks);
    }

    /**
     * 图片嵌入邮件.
     *
     * @param string $file
     *
     * @return string
     */
    public static function attachView(string $file): string
    {
        return self::proxy()->attachView($file);
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
    public static function attachDataView(string $data, string $name, ?string $contentType = null): string
    {
        return self::proxy()->attachDataView($data, $name, $contentType);
    }

    /**
     * 格式化中文附件名字.
     *
     * @param string $file
     *
     * @return string
     */
    public static function attachChinese(string $file): string
    {
        return self::proxy()->attachChinese($file);
    }

    /**
     * 发送邮件.
     *
     * @param null|\Closure $callbacks
     * @param bool          $htmlPriority
     *
     * @return int
     */
    public static function flush(?Closure $callbacks = null, bool $htmlPriority = true): int
    {
        return self::proxy()->flush($callbacks, $htmlPriority);
    }

    /**
     * 错误消息.
     *
     * @return array
     */
    public static function failedRecipients(): array
    {
        return self::proxy()->failedRecipients();
    }

    /**
     * 传输机制是否已经启动.
     *
     * @return bool
     */
    public static function isStarted(): bool
    {
        return self::proxy()->isStarted();
    }

    /**
     * 启动传输机制.
     */
    public static function start(): void
    {
        self::proxy()->start();
    }

    /**
     * 停止传输机制.
     */
    public static function stop(): void
    {
        self::proxy()->stop();
    }

    /**
     * 检查此传输机制是否处于活动状态.
     *
     * @return bool
     */
    public static function ping(): bool
    {
        return self::proxy()->ping();
    }

    /**
     * 发送消息.
     *
     * @param \Swift_Mime_SimpleMessage $message
     * @param null|array                $failedRecipients
     *
     * @return int
     */
    public static function send(Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null): int
    {
        return self::proxy()->send($message, $failedRecipients);
    }

    /**
     * 注册一个插件.
     *
     * @param \Swift_Events_EventListener $plugin
     */
    public static function registerPlugin(Swift_Events_EventListener $plugin): void
    {
        self::proxy()->registerPlugin($plugin);
    }

    /**
     * 为此 mime 实体生成新的内容 ID 或消息 ID.
     *
     * @return string
     */
    public static function generateId(): string
    {
        return self::proxy()->generateId();
    }

    /**
     * 获取实体的 {@link \Swift_Mime_SimpleHeaderSet}.
     *
     * @return \Swift_Mime_SimpleHeaderSet
     */
    public static function getHeaders(): Swift_Mime_SimpleHeaderSet
    {
        return self::proxy()->getHeaders();
    }

    /**
     * 获取实体的 Content-type.
     *
     * @return string
     */
    public static function getContentType(): string
    {
        return self::proxy()->getContentType();
    }

    /**
     * 设置实体的 Content-type.
     *
     * @param string $type
     *
     * @return \Swift_Messages
     */
    public static function setContentType(string $type): Swift_Message
    {
        return self::proxy()->setContentType($type);
    }

    /**
     * 获取此实体的 CID.
     *
     * 只有在存在 Content-ID 内容头的情况下，CID 才会出现在头中.
     *
     * @return string
     */
    public static function getId(): string
    {
        return self::proxy()->getId();
    }

    /**
     * 设置此实体的 CID.
     *
     * @param string $id
     *
     * @return \Swift_Messages
     */
    public static function setId(string $id): Swift_Message
    {
        return self::proxy()->setId($id);
    }

    /**
     * 获取此实体的说明.
     *
     * 此值来自 Content-Description 头，如果设置此字段.
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return self::proxy()->getDescription();
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
    public static function setDescription(string $description): Swift_Message
    {
        return self::proxy()->setDescription($description);
    }

    /**
     * 获取此正文行的最长字符长度.
     *
     * @return int
     */
    public static function getMaxLineLength(): int
    {
        return self::proxy()->getMaxLineLength();
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
    public static function setMaxLineLength(int $length): Swift_Message
    {
        return self::proxy()->setMaxLineLength($length);
    }

    /**
     * 获取此实体的所有子级.
     *
     * @return \Swift_Mime_SimpleMimeEntity[]
     */
    public static function getChildren(): array
    {
        return self::proxy()->getChildren();
    }

    /**
     * 设置此实体的所有子级.
     *
     * @param \Swift_Mime_SimpleMimeEntity[] $children
     * @param null|int                       $compoundLevel
     *
     * @return \Swift_Messages
     */
    public static function setChildren(array $children, ?int $compoundLevel = null): Swift_Message
    {
        return self::proxy()->setChildren($children, $compoundLevel);
    }

    /**
     * 获取此实体的正文为字符串.
     *
     * @return string
     */
    public static function getBody(): string
    {
        return self::proxy()->getBody();
    }

    /**
     * 获取用于此实体主体的编码器.
     *
     * @return \Swift_Mime_ContentEncoder
     */
    public static function getEncoder(): Swift_Mime_ContentEncoder
    {
        return self::proxy()->getEncoder();
    }

    /**
     * 设置用于此实体主体的编码器.
     *
     * @param \Swift_Mime_ContentEncoder $encoder
     *
     * @return \Swift_Messages
     */
    public static function setEncoder(Swift_Mime_ContentEncoder $encoder): Swift_Message
    {
        return self::proxy()->setEncoder($encoder);
    }

    /**
     * 获取用于分隔此实体中的子级的边界.
     *
     * @return string
     */
    public static function getBoundary(): string
    {
        return self::proxy()->getBoundary();
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
    public static function setBoundary($boundary): Swift_Message
    {
        return self::proxy()->setBoundary($boundary);
    }

    /**
     * 收到有关此实体或者父实体的编码器已更改的通知.
     *
     * @param \Swift_Mime_ContentEncoder $encoder
     */
    public static function encoderChanged(Swift_Mime_ContentEncoder $encoder): void
    {
        self::proxy()->encoderChanged($encoder);
    }

    /**
     * 设置此实体的主体为字符串或者 {@link \Swift_OutputByteStream}.
     *
     * @param string|\Swift_OutputByteStream $body
     * @param null|string                    $contentType
     * @param null|string                    $charset
     *
     * @return \Swift_Messages
     */
    public static function setBody($body, ?string $contentType = null, ?string $charset = null): Swift_Message
    {
        return self::proxy()->setBody($body, $contentType, $charset);
    }

    /**
     * 获取实体的字符集.
     *
     * @return string
     */
    public static function getCharset(): string
    {
        return self::proxy()->getCharset();
    }

    /**
     * 设置实体的字符集.
     *
     * @param string $charset
     *
     * @return \Swift_Message
     */
    public static function setCharset(string $charset): Swift_Message
    {
        return self::proxy()->setCharset($charset);
    }

    /**
     * 获取此实体的格式 (例如 flowed 或 fixed).
     *
     * @return string
     */
    public static function getFormat(): string
    {
        return self::proxy()->getFormat();
    }

    /**
     * 设置此实体的格式 (例如 flowed 或 fixed).
     *
     * @param string $format
     *
     * @return \Swift_Message
     */
    public static function setFormat(string $format): Swift_Message
    {
        return self::proxy()->setFormat($format);
    }

    /**
     * 测试是否正在使用 delsp.
     *
     * @return bool
     */
    public static function getDelSp(): bool
    {
        return self::proxy()->getDelSp();
    }

    /**
     * 打开或关闭 Delsp.
     *
     * @param bool $delsp
     *
     * @return \Swift_Message
     */
    public static function setDelSp(bool $delsp = true): Swift_Message
    {
        return self::proxy()->setDelSp($delsp);
    }

    /**
     * 收到有关此文档或者父文档的字符集已更改的通知.
     *
     * @param string $charset
     */
    public static function charsetChanged(string $charset): void
    {
        self::proxy()->charsetChanged($charset);
    }

    /**
     * 始终返回 {@link \Swift_Mime_SimpleMimeEntity::LEVEL_TOP}.
     *
     * @return int
     */
    public static function getNestingLevel(): int
    {
        return self::proxy()->getNestingLevel();
    }

    /**
     * 设置此邮件的主题.
     *
     * @param string $subject
     *
     * @return \Swift_Message
     */
    public static function setSubject(string $subject): Swift_Message
    {
        return self::proxy()->setSubject($subject);
    }

    /**
     * 获取此邮件的主题.
     *
     * @return string
     */
    public static function getSubject(): string
    {
        return self::proxy()->getSubject();
    }

    /**
     * 设置创建此邮件的日期
     *
     * @param \DateTimeInterface $dateTime
     *
     * @return \Swift_Message
     */
    public static function setDate(DateTimeInterface $dateTime): Swift_Message
    {
        return self::proxy()->setDate($dateTime);
    }

    /**
     * 获取创建此邮件的日期
     *
     * @return \DateTimeInterface
     */
    public static function getDate(): DateTimeInterface
    {
        return self::proxy()->getDate();
    }

    /**
     * 设置此消息的退回路地址（邮件退回地址）.
     *
     * @param string $address
     *
     * @return \Swift_Message
     */
    public static function setReturnPath(string $address): Swift_Message
    {
        return self::proxy()->setReturnPath($address);
    }

    /**
     * 获取此消息的退回路地址（邮件退回地址）.
     *
     * @return string
     */
    public static function getReturnPath(): string
    {
        return self::proxy()->getReturnPath();
    }

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
    public static function setSender(string $address, ?string $name = null): Swift_Message
    {
        return self::proxy()->setSender($address, $name);
    }

    /**
     * 获取此邮件的发件人.
     *
     * @return string
     */
    public static function getSender(): string
    {
        return self::proxy()->getSender();
    }

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
    public static function addFrom(string $address, ?string $name = null): Swift_Message
    {
        return self::proxy()->addFrom($address, $name);
    }

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
    public static function setFrom($addresses, ?string $name = null): Swift_Message
    {
        return self::proxy()->setFrom($addresses, $name);
    }

    /**
     * 获取此邮件的发件人地址.
     *
     * @return mixed
     */
    public static function getFrom()
    {
        return self::proxy()->getFrom();
    }

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
    public static function addReplyTo(string $address, ?string $name = null): Swift_Message
    {
        return self::proxy()->addReplyTo($address, $name);
    }

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
    public static function setReplyTo($addresses, ?string $name = null): Swift_Message
    {
        return self::proxy()->setReplyTo($addresses, $name);
    }

    /**
     * 获取答复邮件地址.
     *
     * @return string
     */
    public static function getReplyTo(): string
    {
        return self::proxy()->getReplyTo();
    }

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
    public static function addTo(string $address, ?string $name = null): Swift_Message
    {
        return self::proxy()->addTo($address, $name);
    }

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
    public static function setTo($addresses, ?string $name = null): Swift_Message
    {
        return self::proxy()->setTo($addresses, $name);
    }

    /**
     * 获取发送邮件地址
     *
     * @return array
     */
    public static function getTo(): array
    {
        return self::proxy()->getTo();
    }

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
    public static function addCc(string $address, ?string $name = null): Swift_Message
    {
        return self::proxy()->addCc($address, $name);
    }

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
    public static function setCc($addresses, ?string $name = null): Swift_Message
    {
        return self::proxy()->setCc($addresses, $name);
    }

    /**
     * 获取抄送邮件地址.
     *
     * @return array
     */
    public static function getCc(): array
    {
        return self::proxy()->getCc();
    }

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
    public static function addBcc(string $address, ?string $name = null): Swift_Message
    {
        return self::proxy()->addBcc($address, $name);
    }

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
    public static function setBcc($addresses, ?string $name = null): Swift_Message
    {
        return self::proxy()->setBcc($addresses, $name);
    }

    /**
     * 获取密送邮件地址.
     *
     * @return array
     */
    public static function getBcc(): array
    {
        return self::proxy()->getBcc();
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
    public static function setPriority(int $priority): Swift_Message
    {
        return self::proxy()->setPriority($priority);
    }

    /**
     * 获取此邮件的优先级.
     *
     * 该值是一个整数，其中 1 是最高优先级，5 是最低优先级.
     *
     * @return int
     */
    public static function getPriority(): int
    {
        return self::proxy()->getPriority();
    }

    /**
     * 设置邮件回执地址.
     *
     * @param array $addresses
     *
     * @return \Swift_Message
     */
    public static function setReadReceiptTo(array $addresses): Swift_Message
    {
        return self::proxy()->setReadReceiptTo($addresses);
    }

    /**
     * 获取邮件回执地址.
     *
     * @return string
     */
    public static function getReadReceiptTo(): string
    {
        return self::proxy()->getReadReceiptTo();
    }

    /**
     * 添加 {@link \Swift_Mime_SimpleMimeEntity} 实体例如附件或者 Mime 部分.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return \Swift_Message
     */
    public static function attach(Swift_Mime_SimpleMimeEntity $entity): Swift_Message
    {
        return self::proxy()->attach($entity);
    }

    /**
     * 删除添加的实体.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return \Swift_Message
     */
    public static function detach(Swift_Mime_SimpleMimeEntity $entity): Swift_Message
    {
        return self::proxy()->detach($entity);
    }

    /**
     * 添加 {@link \Swift_Mime_SimpleMimeEntity}，然后返回它的 CID 源.
     * 在消息中嵌入图像或其他数据时，应使用此方法.
     *
     * @param \Swift_Mime_SimpleMimeEntity $entity
     *
     * @return string
     */
    public static function embed(Swift_Mime_SimpleMimeEntity $entity): string
    {
        return self::proxy()->embed($entity);
    }

    /**
     * 在此邮件中添加一个 MIME 部件.
     *
     * @param string|\Swift_OutputByteStream $body
     * @param null|string                    $contentType
     * @param null|string                    $charset
     *
     * @return \Swift_Message
     */
    public static function addPart($body, ?string $contentType = null, ?string $charset = null): Swift_Message
    {
        return self::proxy()->addPart($body, $contentType, $charset);
    }

    /**
     * 从消息中删除签名处理程序.
     *
     * @param \Swift_Signer $signer
     *
     * @return \Swift_Message
     */
    public static function attachSigner(Swift_Signer $signer): Swift_Message
    {
        return self::proxy()->attachSigner($signer);
    }

    /**
     * 添加签名处理程序到消息中.
     *
     * @param \Swift_Signer $signer
     *
     * @return \Swift_Message
     */
    public static function detachSigner(Swift_Signer $signer): Swift_Message
    {
        return self::proxy()->detachSigner($signer);
    }

    /**
     * 将此消息作为完整字符串获取.
     *
     * @return string
     */
    public static function toString(): string
    {
        return self::proxy()->toString();
    }

    /**
     * 将此消息写入 {@link \Swift_InputByteStream}.
     *
     * @param \Swift_InputByteStream $is
     */
    public static function toByteStream(Swift_InputByteStream $is): void
    {
        self::proxy()->toByteStream($is);
    }

    /**
     * 代理服务
     *
     * @return \Leevel\Mail\Manager
     */
    public static function proxy(): Manager
    {
        return Container::singletons()->make('mails');
    }
}
