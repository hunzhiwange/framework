<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\File as CacheFile;
use Leevel\Session\File;
use Leevel\Session\ISession;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class FileTest extends TestCase
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

        static::assertFalse($session->isStart());
        static::assertSame('', $session->getId());
        static::assertSame('UID', $session->getName());

        $session->start();
        static::assertTrue($session->isStart());

        $session->set('hello', 'world');
        static::assertSame(['hello' => 'world'], $session->all());
        static::assertTrue($session->has('hello'));
        static::assertSame('world', $session->get('hello'));

        $session->delete('hello');
        static::assertSame([], $session->all());
        static::assertFalse($session->has('hello'));
        static::assertNull($session->get('hello'));

        $session->start();
        static::assertTrue($session->isStart());
    }

    protected function createFileSessionHandler(): File
    {
        return new File(new CacheFile([
            'path' => __DIR__.'/cacheFile',
        ]));
    }
}
