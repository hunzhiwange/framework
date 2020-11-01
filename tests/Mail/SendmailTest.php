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

use Leevel\Mail\Sendmail;
use Leevel\Router\View;
use Leevel\View\Phpui;
use Swift_Message;
use Swift_Mime_SimpleMessage;
use Tests\TestCase;

class SendmailTest extends TestCase
{
    public function testBaseUse(): void
    {
        $sendmail = new MySendmail($this->makeView(), null, [
            'path' => '/usr/sbin/sendmail -bs',
        ]);

        $message = (new Swift_Message('Wonderful Subject'))
            ->setFrom(['635750556@qq.com' => 'John Doe'])
            ->setTo(['log1990@126.com' => 'A name'])
            ->setBody('Here is the message itself');

        $result = $sendmail->send($message);

        $this->assertSame(1, $result);
        $this->assertTrue($sendmail->isStarted());
        $this->assertNull($sendmail->start());
        $this->assertNull($sendmail->stop());
        $this->assertTrue($sendmail->ping());

        $result = $sendmail->send($message);

        $this->assertSame(1, $result);
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

class MySendmail extends Sendmail
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
