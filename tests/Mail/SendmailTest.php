<?php

declare(strict_types=1);

namespace Tests\Mail;

use Leevel\Mail\Sendmail;
use Leevel\View\Manager;
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

    protected function makeView(): Manager
    {
        return $this->createMock(Manager::class);
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
