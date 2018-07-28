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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Mail;

use Leevel\Mail\Mail;
use Leevel\Mail\Smtp;
use Leevel\Mvc\View;
use Leevel\View\Phpui;
use Leevel\View\View as Views;
use Swift_Attachment;
use Swift_Message;
use Swift_Mime_SimpleMessage;
use Tests\TestCase;

/**
 * mail test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.28
 *
 * @version 1.0
 */
class MailTest extends TestCase
{
    public function testBaseUse()
    {
        $mail = $this->makeMail();

        $mail->plain('hello');

        $result = $mail->send();

        $this->assertSame(1, $result);

        $this->assertSame([], $mail->failedRecipients());
    }

    public function testHtml()
    {
        $mail = $this->makeMail();

        $mail->html('<b style="color:red;">hello</b>');

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testHtml2()
    {
        $mail = $this->makeMail();

        $mail->html('<b style="color:red;">hello</b>');
        $mail->html('<b style="color:blue;">world</b>');

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testView()
    {
        $mail = $this->makeMail();

        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testView2()
    {
        $mail = $this->makeMail();

        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $mail->view(__DIR__.'/assert/mail1.php', ['foo' => 'hello']);

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testViewPlain()
    {
        $mail = $this->makeMail();

        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testViewPlain2()
    {
        $mail = $this->makeMail();

        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'bar']);
        $mail->viewPlain(__DIR__.'/assert/mail1.php', ['foo' => 'hello']);

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testAttach()
    {
        $mail = $this->makeMail();

        $mail->html('hello attach');

        $mail->attach(__DIR__.'/assert/logo.png');

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testAttach2()
    {
        $mail = $this->makeMail();

        $mail->html('hello attach');

        $mail->attach(__DIR__.'/assert/logo.png', function (Swift_Attachment $attachment) {
            $attachment->setFilename('logo2.jpg');
        });

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testAttachData()
    {
        $mail = $this->makeMail();

        $mail->html('hello attach');

        $mail->attachData(file_get_contents(__DIR__.'/assert/logo.png'), 'hello.png');

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testAttachView()
    {
        $mail = $this->makeMail();

        $mail->view(__DIR__.'/assert/mail2.php', ['path' => __DIR__.'/assert/logo.png']);

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testAttachDataView()
    {
        $mail = $this->makeMail();

        $mail->view(__DIR__.'/assert/mail3.php', ['data' => file_get_contents(__DIR__.'/assert/logo.png')]);

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testAttachChinese()
    {
        $mail = $this->makeMail();

        $mail->html('hello attach');

        $mail->attachData(
            file_get_contents(__DIR__.'/assert/logo.png'),
            $mail->attachChinese('魂之挽歌.png')
        );

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testSendHtmlAndPlain()
    {
        $mail = $this->makeMail();

        $mail->plain('hello');

        $mail->html('<b style="color:red;">hello</b>');

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testSendHtmlAndPlain2()
    {
        $mail = $this->makeMail();

        $mail->plain('hello');

        $mail->html('<b style="color:red;">hello</b>');

        $result = $mail->send(null, false);

        $this->assertSame(1, $result);
    }

    public function testMessage()
    {
        $mail = $this->makeMail();

        $mail->plain('hello');

        $mail->message(function (Swift_Message $message) {
            $message->setSubject('the subject');
        });

        $result = $mail->send();

        $this->assertSame(1, $result);
    }

    public function testMessage2()
    {
        $mail = $this->makeMail();

        $mail->plain('hello');

        $result = $mail->send(function (Swift_Message $message) {
            $message->setSubject('the subject');
        });

        $this->assertSame(1, $result);
    }

    protected function makeMail(): Mail
    {
        $mail = new Mail($this->makeConnect(), $this->makeView(), null);

        $mail->globalFrom('635750556@qq.com', 'xiaoniu');
        $mail->globalTo('log1990@126.com', 'niuzai');

        return $mail;
    }

    protected function makeView()
    {
        return new View(
            new Views(new Phpui([
                'theme_path' => __DIR__,
            ]))
        );
    }

    protected function makeConnect()
    {
        return new MailSmtp([
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
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        return 1;
    }
}
