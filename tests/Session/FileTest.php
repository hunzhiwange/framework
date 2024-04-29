<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\File as CacheFile;
use Leevel\Session\File;
use Leevel\Session\ISession;
use Tests\TestCase;

final class FileTest extends TestCase
{
    #[\Override]
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

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());

        $session->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $session->all());
        self::assertTrue($session->has('hello'));
        self::assertSame('world', $session->get('hello'));

        $session->delete('hello');
        self::assertSame([], $session->all());
        self::assertFalse($session->has('hello'));
        self::assertNull($session->get('hello'));

        $session->start();
        self::assertTrue($session->isStart());
    }

    protected function createFileSessionHandler(): File
    {
        return new File(new CacheFile([
            'path' => __DIR__.'/cacheFile',
        ]));
    }
}
