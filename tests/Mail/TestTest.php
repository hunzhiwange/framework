<?php

declare(strict_types=1);

namespace Tests\Mail;

use Leevel\Mail\Test;
use Leevel\View\IView;
use Swift_Message;
use Tests\TestCase;

class TestTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Test($this->makeView());

        $message = (new Swift_Message('Wonderful Subject'))
            ->setFrom(['foo@qq.com' => 'John Doe'])
            ->setTo(['bar@qq.com' => 'A name'])
            ->setBody('Here is the message itself');

        $result = $test->send($message);

        $this->assertSame(1, $result);

        $this->assertTrue($test->isStarted());
        $this->assertNull($test->start());
        $this->assertNull($test->stop());
        $this->assertTrue($test->ping());
    }

    protected function makeView(): IView
    {
        return $this->createMock(IView::class);
    }
}
