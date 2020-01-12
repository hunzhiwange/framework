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

namespace Tests\Mail;

use Leevel\Mail\Mail;
use Leevel\Mail\Smtp;
use Leevel\Router\View;
use Leevel\View\Phpui;
use Swift_Attachment;
use Swift_Message;
use Swift_Mime_SimpleMessage;
use Tests\TestCase;

/**
 * @api(
 *     title="Mail",
 *     path="component/mail",
 *     description="
 * 邮件发送统一由邮件组件完成，通常我们使用代理 `\Leevel\Mail\Proxy\Mail` 类进行静态调用。
 *
 * QueryPHP 的邮件底层为 `swiftmailer/swiftmailer`，系统进行了简单的一层封装。
 *
 * 内置支持的邮件驱动类型包括 smtp、sendmail、test，未来可能增加其他驱动。
 *
 * ## 使用方式
 *
 * 使用容器 mails 服务
 *
 * ``` php
 * \App::make('mails')->html(string $content): \Leevel\Mail\IMail;
 * ```
 *
 * 依赖注入
 *
 * ``` php
 * class Demo
 * {
 *     private $mail;
 *
 *     public function __construct(\Leevel\Mail\Manager $mail)
 *     {
 *         $this->mail = $mail;
 *     }
 * }
 * ```
 *
 * 使用静态代理
 *
 * ``` php
 * \Leevel\Log\Proxy\Log::html(string $content): \Leevel\Mail\IMail;
 * ```
 *
 * ## mail 配置
 *
 * 系统的 mail 配置位于应用下面的 `option/mail.php` 文件。
 *
 * 可以定义多个邮件连接，并且支持切换，每一个连接支持驱动设置。
 *
 * ``` php
 * {[file_get_contents('option/mail.php')]}
 * ```
 *
 * mail 参数根据不同的连接会有所区别，通用的 mail 参数如下：
 *
 * |配置项|配置描述|
 * |:-|:-|
 * |global_from|邮件发送地址|
 * |global_to|邮件全局接收地址|
 * ",
 * )
 */
class MailTest extends TestCase
{
    /**
     * @api(
     *     title="plain 纯文本邮件内容",
     *     description="",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $result = $mail->flush();

        $this->assertSame(1, $result);
        $this->assertSame([], $mail->failedRecipients());
    }

    /**
     * @api(
     *     title="html HTML 邮件内容",
     *     description="",
     *     note="",
     * )
     */
    public function testHtml(): void
    {
        $mail = $this->makeMail();
        $mail->html('<b style="color:red;">hello</b>');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="html HTML 邮件内容支持多次添加",
     *     description="",
     *     note="",
     * )
     */
    public function testHtmlMulti(): void
    {
        $mail = $this->makeMail();
        $mail->html('<b style="color:red;">hello</b>');
        $mail->html('<b style="color:blue;">world</b>');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="view 视图 HTML 邮件内容",
     *     description="
     * **fixture 定义**
     *
     * **mail1.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Mail/assert/mail1.php')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testView(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="view 视图 HTML 邮件内容支持多次添加",
     *     description="",
     *     note="",
     * )
     */
    public function testViewMulti(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'hello']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="viewPlain 视图纯文本邮件内容",
     *     description="",
     *     note="",
     * )
     */
    public function testViewPlain(): void
    {
        $mail = $this->makeMail();
        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="viewPlain 视图纯文本邮件内容支持多次添加",
     *     description="",
     *     note="",
     * )
     */
    public function testViewPlainMulti(): void
    {
        $mail = $this->makeMail();
        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'hello']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="attachMail 添加附件",
     *     description="",
     *     note="",
     * )
     */
    public function testAttach(): void
    {
        $mail = $this->makeMail();
        $mail->html('hello attach');
        $mail->attachMail(__DIR__.'/assert/logo.png');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="attachMail 添加附件支持设置附件名字",
     *     description="",
     *     note="",
     * )
     */
    public function testAttachSupportSetFilename(): void
    {
        $mail = $this->makeMail();
        $mail->html('hello attach');
        $mail->attachMail(__DIR__.'/assert/logo.png', function (Swift_Attachment $attachment) {
            $attachment->setFilename('logo2.jpg');
        });
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="attachData 添加内存内容附件",
     *     description="",
     *     note="",
     * )
     */
    public function testAttachData(): void
    {
        $mail = $this->makeMail();
        $mail->html('hello attach');
        $mail->attachData(file_get_contents(__DIR__.'/assert/logo.png'), 'hello.png');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="view 视图 HTML 邮件内容支持附件",
     *     description="
     * **fixture 定义**
     *
     * **mail2.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Mail/assert/mail2.php')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testAttachView(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail2.php', ['path' => __DIR__.'/assert/logo.png']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="plain 纯文本邮件内容",
     *     description="",
     *     note="",
     * )
     */
    public function testAttachDataView(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail3.php', ['data' => file_get_contents(__DIR__.'/assert/logo.png')]);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="attachData 添加内存内容附件支持附件中文名字",
     *     description="",
     *     note="",
     * )
     */
    public function testAttachChinese(): void
    {
        $mail = $this->makeMail();
        $mail->html('hello attach');
        $mail->attachData(
            file_get_contents(__DIR__.'/assert/logo.png'),
            $mail->attachChinese('魂之挽歌.png')
        );
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="HTML 邮件优先级默认高于纯文本邮件",
     *     description="
     * HTML 邮件内容与纯文本邮件内容同时存在，系统优先采用前者。
     *
     * **flush 函数原型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Mail\Mail::class, 'flush', 'define')]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testSendHtmlAndPlain(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $mail->html('<b style="color:red;">hello</b>');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="可以设置纯文本邮件优先级高于 HTML 邮件",
     *     description="",
     *     note="",
     * )
     */
    public function testSendHtmlAndPlainAndPlainIsFirst(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $mail->html('<b style="color:red;">hello</b>');
        $result = $mail->flush(null, false);

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="message 消息回调处理",
     *     description="",
     *     note="",
     * )
     */
    public function testMessage(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $mail->message(function (Swift_Message $message) {
            $message->setSubject('the subject');
        });
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    /**
     * @api(
     *     title="flush 消息回调处理",
     *     description="",
     *     note="",
     * )
     */
    public function testFlushWithMessage(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $result = $mail->flush(function (Swift_Message $message) {
            $message->setSubject('the subject');
        });

        $this->assertSame(1, $result);
    }

    protected function makeMail(): Mail
    {
        $mail = $this->makeConnect();
        $mail->globalFrom('635750556@qq.com', 'xiaoniu');
        $mail->globalTo('log1990@126.com', 'niuzai');

        return $mail;
    }

    protected function makeConnect(): MailSmtp
    {
        return new MailSmtp($this->makeView(), null, [
            'host'       => 'smtp.qq.com',
            'port'       => 465,
            'username'   => '635750556@qq.com',
            'password'   => 'ebqdatmseuyjbeie', // 授权码而并非 QQ 密码
            'encryption' => 'ssl',
        ]);
    }

    protected function makeView(): View
    {
        return new View(
            new Phpui([
                'theme_path' => __DIR__,
            ])
        );
    }
}

class MailSmtp extends Smtp
{
    public function send(Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null): int
    {
        return 1;
    }
}
