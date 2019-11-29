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

use Leevel\Manager\Manager as Managers;

/**
 * Mail 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.26
 *
 * @version 1.0
 *
 * @method static \Leevel\Mail\IMail setOption(string $name, $value)                                     设置配置.
 * @method static \Leevel\Mail\IMail globalFrom(string $address, ?string $name = null)                   设置邮件发送来源.
 * @method static \Leevel\Mail\IMail globalTo(string $address, ?string $name = null)                     设置邮件发送地址
 * @method static \Leevel\Mail\IMail view(string $file, array $data = [])                                视图 html 邮件内容.
 * @method static \Leevel\Mail\IMail html(string $content)                                               html 邮件内容.
 * @method static \Leevel\Mail\IMail plain(string $content)                                              纯文本邮件内容.
 * @method static \Leevel\Mail\IMail viewPlain(string $file, array $data = [])                           视图纯文本邮件内容.
 * @method static \Leevel\Mail\IMail message(\Closure $callbacks)                                        消息回调处理.
 * @method static \Leevel\Mail\IMail attachMail(string $file, ?\Closure $callbacks = null)               添加附件.
 * @method static \Leevel\Mail\IMail attachData(string $data, string $name, ?\Closure $callbacks = null) 添加内存内容附件 file_get_content(path).
 * @method static string attachView(string $file)                                                        图片嵌入邮件.
 * @method static string attachDataView(string $data, string $name, ?string $contentType = null)         内存内容图片嵌入邮件.
 * @method static string attachChinese(string $file)                                                     格式化中文附件名字.
 * @method static int flush(?\Closure $callbacks = null, bool $htmlPriority = true)                      发送邮件.
 * @method static array failedRecipients()                                                               错误消息.
 * @method static bool isStarted()                                                                       传输机制是否已经启动.
 * @method static void start()                                                                           启动传输机制.
 * @method static void stop()                                                                            停止传输机制.
 * @method static bool ping()                                                                            检查此传输机制是否处于活动状态.
 * @method static int send(\Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null)         发送消息.
 * @method static void registerPlugin(\Swift_Events_EventListener $plugin)                               注册一个插件.
 * @method static string generateId()                                                                    为此 mime 实体生成新的内容 ID 或消息 ID.
 * @method static \Swift_Mime_SimpleHeaderSet getHeaders()                                               获取实体的 {@link \Swift_Mime_SimpleHeaderSet}.
 * @method static string getContentType()                                                                获取实体的 Content-type.
 * @method static \Swift_Message setContentType(string $type)                                            设置实体的 Content-type.
 * @method static string getId()                                                                         获取此实体的 CID.
 * @method static \Swift_Message setId(string $id)                                                       设置此实体的 CID.
 * @method static string getDescription()                                                                获取此实体的说明.
 * @method static \Swift_Message setDescription(string $description)                                     设置此实体的说明.
 * @method static int getMaxLineLength()                                                                 获取此正文行的最长字符长度.
 * @method static \Swift_Message setMaxLineLength(int $length)                                           设置此正文行的最长字符长度.
 * @method static array getChildren()                                                                    获取此实体的所有子级.
 * @method static \Swift_Message setChildren(array $children, ?int $compoundLevel = null)                设置此实体的所有子级.
 * @method static string getBody()                                                                       获取此实体的正文为字符串.
 * @method static \Swift_Mime_ContentEncoder getEncoder()                                                获取用于此实体主体的编码器.
 * @method static \Swift_Message setEncoder(\Swift_Mime_ContentEncoder $encoder)                         设置用于此实体主体的编码器.
 * @method static string getBoundary()                                                                   获取用于分隔此实体中的子级的边界.
 * @method static \Swift_Message setBoundary($boundary)                                                  设置用于分隔此实体中的子级的边界.
 * @method static void encoderChanged(\Swift_Mime_ContentEncoder $encoder)                               收到有关此实体或者父实体的编码器已更改的通知.
 * @method static \Swift_Message setBody($body, ?string $contentType = null, ?string $charset = null)    设置此实体的主体为字符串或者 {@link \Swift_OutputByteStream}.
 * @method static string getCharset()                                                                    获取实体的字符集.
 * @method static \Swift_Message setCharset(string $charset)                                             设置实体的字符集.
 * @method static string getFormat()                                                                     获取此实体的格式 (例如 flowed 或 fixed).
 * @method static \Swift_Message setFormat(string $format)                                               设置此实体的格式 (例如 flowed 或 fixed).
 * @method static bool getDelSp()                                                                        测试是否正在使用 delsp.
 * @method static \Swift_Message setDelSp(bool $delsp = true)                                            打开或关闭 Delsp.
 * @method static void charsetChanged(string $charset)                                                   收到有关此文档或者父文档的字符集已更改的通知.
 * @method static int getNestingLevel()                                                                  始终返回 {@link \Swift_Mime_SimpleMimeEntity::LEVEL_TOP}.
 * @method static \Swift_Message setSubject(string $subject)                                             设置此邮件的主题.
 * @method static string getSubject()                                                                    获取此邮件的主题.
 * @method static \Swift_Message setDate(\DateTimeInterface $dateTime)                                   设置创建此邮件的日期
 * @method static \DateTimeInterface getDate()                                                           获取创建此邮件的日期
 * @method static \Swift_Message setReturnPath(string $address)                                          设置此消息的退回路地址（邮件退回地址）.
 * @method static string getReturnPath()                                                                 获取此消息的退回路地址（邮件退回地址）.
 * @method static \Swift_Message setSender(string $address, ?string $name = null)                        设置此邮件的发件人.
 * @method static string getSender()                                                                     获取此邮件的发件人.
 * @method static \Swift_Message addFrom(string $address, ?string $name = null)                          在此邮件中添加发件人地址.
 * @method static \Swift_Message setFrom($addresses, ?string $name = null)                               设置此邮件的发件人地址.
 * @method static getFrom()                                                                              获取此邮件的发件人地址.
 * @method static \Swift_Message addReplyTo(string $address, ?string $name = null)                       添加答复邮件地址.
 * @method static \Swift_Message setReplyTo($addresses, ?string $name = null)                            设置答复邮件地址.
 * @method static string getReplyTo()                                                                    获取答复邮件地址.
 * @method static \Swift_Message addTo(string $address, ?string $name = null)                            添加发送邮件地址.
 * @method static \Swift_Message setTo($addresses, ?string $name = null)                                 设置发送邮件地址.
 * @method static array getTo()                                                                          获取发送邮件地址
 * @method static \Swift_Message addCc(string $address, ?string $name = null)                            添加抄送邮件地址.
 * @method static \Swift_Message setCc($addresses, ?string $name = null)                                 设置抄送邮件地址.
 * @method static array getCc()                                                                          获取抄送邮件地址.
 * @method static \Swift_Message addBcc(string $address, ?string $name = null)                           添加密送邮件地址.
 * @method static \Swift_Message setBcc($addresses, ?string $name = null)                                设置密送邮件地址.
 * @method static array getBcc()                                                                         获取密送邮件地址.
 * @method static \Swift_Message setPriority(int $priority)                                              设置此邮件的优先级.
 * @method static int getPriority()                                                                      获取此邮件的优先级.
 * @method static \Swift_Message setReadReceiptTo(array $addresses)                                      设置邮件回执地址.
 * @method static string getReadReceiptTo()                                                              获取邮件回执地址.
 * @method static \Swift_Message attach(\Swift_Mime_SimpleMimeEntity $entity)                            添加 {@link \Swift_Mime_SimpleMimeEntity} 实体例如附件或者 Mime 部分.
 * @method static \Swift_Message detach(\Swift_Mime_SimpleMimeEntity $entity)                            删除添加的实体.
 * @method static string embed(\Swift_Mime_SimpleMimeEntity $entity)                                     添加 {@link \Swift_Mime_SimpleMimeEntity}，然后返回它的 CID 源. 在消息中嵌入图像或其他数据时，应使用此方法.
 * @method static \Swift_Message addPart($body, ?string $contentType = null, ?string $charset = null)    在此邮件中添加一个 MIME 部件.
 * @method static \Swift_Message attachSigner(\Swift_Signer $signer)                                     从消息中删除签名处理程序.
 * @method static \Swift_Message detachSigner(\Swift_Signer $signer)                                     添加签名处理程序到消息中.
 * @method static string toString()                                                                      将此消息作为完整字符串获取.
 * @method static void toByteStream(\Swift_InputByteStream $is)                                          将此消息写入 {@link \Swift_InputByteStream}.
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'mail';
    }

    /**
     * 创建 test 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Mail\Test
     */
    protected function makeConnectTest(array $options = []): Test
    {
        return new Test(
            $this->container['view'],
            $this->container['event'],
            $this->normalizeConnectOption('test', $options)
        );
    }

    /**
     * 创建 smtp 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Mail\Smtp
     * @codeCoverageIgnore
     */
    protected function makeConnectSmtp(array $options = []): Smtp
    {
        return new Smtp(
            $this->container['view'],
            $this->container['event'],
            $this->normalizeConnectOption('smtp', $options)
        );
    }

    /**
     * 创建 sendmail 连接.
     *
     * @param array $options
     *
     * @return \Leevel\Mail\Sendmail
     * @codeCoverageIgnore
     */
    protected function makeConnectSendmail(array $options = []): Sendmail
    {
        return new Sendmail(
            $this->container['view'],
            $this->container['event'],
            $this->normalizeConnectOption('sendmail', $options)
        );
    }
}
