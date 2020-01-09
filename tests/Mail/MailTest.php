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

class MailTest extends TestCase
{
    public function testBaseUse(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $result = $mail->flush();

        $this->assertSame(1, $result);
        $this->assertSame([], $mail->failedRecipients());
    }

    public function testHtml(): void
    {
        $mail = $this->makeMail();
        $mail->html('<b style="color:red;">hello</b>');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testHtml2(): void
    {
        $mail = $this->makeMail();
        $mail->html('<b style="color:red;">hello</b>');
        $mail->html('<b style="color:blue;">world</b>');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testView(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testView2(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'hello']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testViewPlain(): void
    {
        $mail = $this->makeMail();
        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testViewPlain2(): void
    {
        $mail = $this->makeMail();
        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'hello']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testAttach(): void
    {
        $mail = $this->makeMail();
        $mail->html('hello attach');
        $mail->attachMail(__DIR__.'/assert/logo.png');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testAttach2(): void
    {
        $mail = $this->makeMail();
        $mail->html('hello attach');
        $mail->attachMail(__DIR__.'/assert/logo.png', function (Swift_Attachment $attachment) {
            $attachment->setFilename('logo2.jpg');
        });
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testAttachData(): void
    {
        $mail = $this->makeMail();
        $mail->html('hello attach');
        $mail->attachData(file_get_contents(__DIR__.'/assert/logo.png'), 'hello.png');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testAttachView(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail2.php', ['path' => __DIR__.'/assert/logo.png']);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testAttachDataView(): void
    {
        $mail = $this->makeMail();
        $mail->view(__DIR__.'/assert/mail3.php', ['data' => file_get_contents(__DIR__.'/assert/logo.png')]);
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

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

    public function testSendHtmlAndPlain(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $mail->html('<b style="color:red;">hello</b>');
        $result = $mail->flush();

        $this->assertSame(1, $result);
    }

    public function testSendHtmlAndPlain2(): void
    {
        $mail = $this->makeMail();
        $mail->plain('hello');
        $mail->html('<b style="color:red;">hello</b>');
        $result = $mail->flush(null, false);

        $this->assertSame(1, $result);
    }

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

    public function testMessage2(): void
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

    protected function makeView(): View
    {
        return new View(
            new Phpui([
                'theme_path' => __DIR__,
            ])
        );
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
}

class MailSmtp extends Smtp
{
    public function send(Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null): int
    {
        return 1;
    }
}
