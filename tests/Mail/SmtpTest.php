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

use Leevel\Mail\Smtp;
use Swift_Message;
use Swift_Mime_SimpleMessage;
use Tests\TestCase;
use Leevel\View\Manager;

class SmtpTest extends TestCase
{
    public function testBaseUse(): void
    {
        $smtp = new MySmtp($this->makeView(), null, [
            'host'       => 'smtp.qq.com',
            'port'       => 465,
            'username'   => '635750556@qq.com',
            'password'   => 'hellopassworld', // 授权码而并非 QQ 密码
            'encryption' => 'ssl',
        ]);

        $message = (new Swift_Message('Wonderful Subject'))
            ->setFrom(['635750556@qq.com' => 'John Doe'])
            ->setTo(['log1990@126.com' => 'A name'])
            ->setBody('Here is the message itself');

        $result = $smtp->send($message);

        $this->assertSame(1, $result);
        $this->assertTrue($smtp->isStarted());
        $this->assertNull($smtp->start());
        $this->assertNull($smtp->stop());
        $this->assertTrue($smtp->ping());

        $result = $smtp->send($message);

        $this->assertSame(1, $result);
    }

    protected function makeView(): Manager
    {
        return $this->createMock(Manager::class);
    }
}

class MySmtp extends Smtp
{
    public function isStarted(): bool
    {
        return true;
    }

    public function start(): void
    {
    }

    public function stop(): void
    {
    }

    public function ping(): bool
    {
        return true;
    }

    public function send(Swift_Mime_SimpleMessage $message, ?array &$failedRecipients = null): int
    {
        return 1;
    }
}
