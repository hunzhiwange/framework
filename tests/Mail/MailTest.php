<?php

declare(strict_types=1);

namespace Tests\Mail;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App;
use Leevel\Mail\Mail;
use Leevel\Mail\Smtp;
use Leevel\Option\Option;
use Leevel\View\Manager;
use Swift_Attachment;
use Swift_Message;
use Swift_Mime_SimpleMessage;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Mail",
 *     path="component/mail",
 *     zh-CN:description="
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
 *     private \Leevel\Mail\Manager $mail;
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
     *     zh-CN:title="plain 纯文本邮件内容",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="html HTML 邮件内容",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="html HTML 邮件内容支持多次添加",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="view 视图 HTML 邮件内容",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **mail1.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Mail/assert/mail1.php')]}
     * ```
     * ",
     *     zh-CN:note="",
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
     *     zh-CN:title="view 视图 HTML 邮件内容支持多次添加",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="viewPlain 视图纯文本邮件内容",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="viewPlain 视图纯文本邮件内容支持多次添加",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="attachMail 添加附件",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="attachMail 添加附件支持设置附件名字",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="attachData 添加内存内容附件",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="view 视图 HTML 邮件内容支持附件",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **mail2.php**
     *
     * ``` php
     * {[file_get_contents('vendor/hunzhiwange/framework/tests/Mail/assert/mail2.php')]}
     * ```
     * ",
     *     zh-CN:note="",
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
     *     zh-CN:title="plain 纯文本邮件内容",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="attachData 添加内存内容附件支持附件中文名字",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="HTML 邮件优先级默认高于纯文本邮件",
     *     zh-CN:description="
     * HTML 邮件内容与纯文本邮件内容同时存在，系统优先采用前者。
     *
     * **flush 函数原型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Mail\Mail::class, 'flush', 'define')]}
     * ```
     * ",
     *     zh-CN:note="",
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
     *     zh-CN:title="可以设置纯文本邮件优先级高于 HTML 邮件",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="message 消息回调处理",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="flush 消息回调处理",
     *     zh-CN:description="",
     *     zh-CN:note="",
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

    public function testCallSwiftTransport(): void
    {
        $mail = $this->makeMail();
        $this->assertNull($mail->start());
        $this->assertTrue($mail->isStarted());
    }

    public function testCallSwiftMessage(): void
    {
        $mail = $this->makeMail();
        $mail->setSubject('hello world');
        $this->assertSame('hello world', $mail->getSubject());
    }

    protected function makeMail(): Mail
    {
        $mail = $this->makeConnect();
        $mail->setGlobalFrom('635750556@qq.com', 'xiaoniu');
        $mail->setGlobalTo('log1990@126.com', 'niuzai');

        return $mail;
    }

    protected function makeConnect(): MailSmtp
    {
        return new MailSmtp($this->createViewManager()->connect('phpui'));
    }

    protected function createViewManager(string $connect = 'phpui'): Manager
    {
        $app = new ExtendAppForMail($container = new Container(), '');
        $container->instance('app', $app);

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $this->assertSame(__DIR__.'/assert', $app->themesPath());
        $this->assertSame(__DIR__.'/cache_theme', $app->storagePath('theme'));

        $option = new Option([
            'view' => [
                'default'               => $connect,
                'action_fail'           => 'public/fail',
                'action_success'        => 'public/success',
                'connect'               => [
                    'html' => [
                        'driver'         => 'html',
                        'suffix'         => '.html',
                    ],
                    'phpui' => [
                        'driver' => 'phpui',
                        'suffix' => '.php',
                    ],
                ],
            ],
        ]);
        $container->singleton('option', $option);

        $request = new ExtendRequestForMail();
        $container->singleton('request', $request);

        return $manager;
    }
}

class MailSmtp extends Smtp
{
    public function send(Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null): int
    {
        return 1;
    }
}

class ExtendAppForMail extends App
{
    public function development(): bool
    {
        return true;
    }

    public function themesPath(string $path = ''): string
    {
        return __DIR__.'/assert';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/cache_'.$path;
    }
}

class ExtendRequestForMail
{
}
