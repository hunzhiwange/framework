<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\File as CacheFile;
use Leevel\Session\File;
use Leevel\Session\ISession;
use Tests\TestCase;

class FileTest extends TestCase
{
    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cacheFile';
        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    public function testBaseUse(): void
    {
        $session = $this->createFileSessionHandler();

        $this->assertInstanceof(ISession::class, $session);

        $this->assertFalse($session->isStart());
        $this->assertSame('', $session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $session->all());
        $this->assertTrue($session->has('hello'));
        $this->assertSame('world', $session->get('hello'));

        $session->delete('hello');
        $this->assertSame([], $session->all());
        $this->assertFalse($session->has('hello'));
        $this->assertNull($session->get('hello'));

        $session->start();
        $this->assertTrue($session->isStart());
    }

    protected function createFileSessionHandler(): File
    {
        return new File(new CacheFile([
            'path' => __DIR__.'/cacheFile',
        ]));
    }
}
