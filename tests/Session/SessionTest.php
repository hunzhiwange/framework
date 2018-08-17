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

namespace Tests\Session;

use Leevel\Cache\Cache;
use Leevel\Session\File;
use Leevel\Session\ISession;
use Leevel\Session\Session;
use SessionHandlerInterface;
use Tests\TestCase;

/**
 * session test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.16
 *
 * @version 1.0
 */
class SessionTest extends TestCase
{
    protected function tearDown()
    {
        $dirPath = __DIR__.'/cache';

        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    public function testBaseUse()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertInstanceof(ISession::class, $session);

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertNull($session->getName());

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

        $this->assertInstanceof(SessionHandlerInterface::class, $session->getConnect());
    }

    public function testSave()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertNull($session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->save();
        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();
        $dirPath = __DIR__.'/cache';
        $filePath = $dirPath.'/__'.$sessionId.'.php';

        $this->assertFileExists($filePath);

        $session->destroy();
        $this->assertFileNotExists($filePath);
    }

    public function testSaveAndStart()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertNull($session->getName());

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertSame([], $session->all());

        $session->set('foo', 'bar');
        $session->set('hello', 'world');
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $session->all());

        $session->save();
        $this->assertFalse($session->isStart());

        $session->clear();
        $this->assertSame([], $session->all());

        $session->set('other', 'value');
        $this->assertSame(['other' => 'value'], $session->all());

        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();
        $session->start(null, $sessionId);

        $this->assertTrue($session->isStart());
        $this->assertSame(['other' => 'value', 'foo' => 'bar', 'hello' => 'world', 'flash.old.key' => []], $session->all());

        $session->save();
        $this->assertFalse($session->isStart());

        $sessionId = $session->getId();
        $dirPath = __DIR__.'/cache';
        $filePath = $dirPath.'/__'.$sessionId.'.php';

        $this->assertFileExists($filePath);

        $session->destroy();
        $this->assertFileNotExists($filePath);
    }

    public function testSaveButNotStart()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Session is not start yet.');

        $session = new Session($this->createFileSessionHandler());

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertNull($session->getName());

        $session->save();
    }

    public function testGetConnect()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertInstanceof(SessionHandlerInterface::class, $connect = $session->getConnect());

        $this->assertInstanceof(Cache::class, $connect->getCache());

        $this->assertTrue($connect->open('', 'foo'));
        $this->assertTrue($connect->close());
        $this->assertTrue($connect->gc(0));
    }

    public function testPut()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->put('hello', 'world');
        $this->assertSame(['hello' => 'world'], $session->all());

        $session->put(['foo' => 'bar']);
        $this->assertSame(['hello' => 'world', 'foo' => 'bar'], $session->all());

        $session->put(['foo' => 'bar']);
        $this->assertSame(['hello' => 'world', 'foo' => 'bar'], $session->all());
    }

    public function testPush()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->push('hello', 'world');
        $this->assertSame(['hello' => ['world']], $session->all());

        $session->push('hello', 'bar');
        $this->assertSame(['hello' => ['world', 'bar']], $session->all());

        $session->push('hello', 'bar');
        $this->assertSame(['hello' => ['world', 'bar', 'bar']], $session->all());
    }

    public function testMerge()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->merge('hello', ['world']);
        $this->assertSame(['hello' => ['world']], $session->all());

        $session->merge('hello', ['bar']);
        $this->assertSame(['hello' => ['world', 'bar']], $session->all());

        $session->merge('hello', ['bar']);
        $this->assertSame(['hello' => ['world', 'bar', 'bar']], $session->all());

        $session->merge('hello', ['he' => 'he']);
        $this->assertSame(['hello' => ['world', 'bar', 'bar', 'he' => 'he']], $session->all());
    }

    public function testPop()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->set('hello', ['foo', 'bar', 'world', 'sub' => 'me']);
        $this->assertSame(['hello' => ['foo', 'bar', 'world', 'sub' => 'me']], $session->all());

        $session->pop('hello', ['bar']);
        $this->assertSame(['hello' => ['foo', 2 => 'world', 'sub' => 'me']], $session->all());

        $session->pop('hello', ['me']);
        $this->assertSame(['hello' => ['foo', 2 => 'world']], $session->all());

        $session->pop('hello', ['foo', 'world']);
        $this->assertSame(['hello' => []], $session->all());
    }

    public function testArr()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->arr('hello', ['sub' => 'me']);
        $this->assertSame(['hello' => ['sub' => 'me']], $session->all());

        $session->arr('hello', 'foo', 'bar');
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar']], $session->all());

        $session->arr('hello', 'foo', 'bar');
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar']], $session->all());
    }

    public function testArrDelete()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->set('hello', ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']], $session->all());

        $session->arrDelete('hello', ['sub', 'foo']);
        $this->assertSame(['hello' => ['hello' => 'world']], $session->all());

        $session->arrDelete('hello', 'foo');
        $this->assertSame(['hello' => ['hello' => 'world']], $session->all());

        $session->arrDelete('hello', 'hello');
        $this->assertSame(['hello' => []], $session->all());
    }

    public function testGetPart()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->set('hello', ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world', 'sub2' => ['foo' => ['foo' => 'bar']]]);
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world', 'sub2' => ['foo' => ['foo' => 'bar']]]], $session->all());

        $this->assertSame('me', $session->getPart('hello\\sub'));
        $this->assertSame(['foo' => 'bar'], $session->getPart('hello\\sub2.foo'));
        $this->assertNull($session->getPart('hello\\sub2.foo.notFound'));
        $this->assertNull($session->getPart('hello\\notFound'));
        $this->assertSame(123, $session->getPart('hello\\notFound', 123));
    }

    public function testGetPart2()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->set('hello', 'bar');

        $this->assertNull($session->getPart('hello\\sub'));
    }

    public function testClear()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->set('hello', ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']);
        $this->assertSame(['hello' => ['sub' => 'me', 'foo' => 'bar', 'hello' => 'world']], $session->all());

        $session->clear();
        $this->assertSame([], $session->all());
    }

    public function testFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->flash('hello', 'world');

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.new.key' => 
  array (
    0 => 'hello',
  ),
  'flash.old.key' => 
  array (
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->flash('foo', ['bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 
  array (
    0 => 'bar',
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testFlashs()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testNowFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->nowFlash('hello', 'world');

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
    0 => 'hello',
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testNowFlashs()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testRebuildFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->rebuildFlash();

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 'bar',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testKeepFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->keepFlash('hello', 'foo');

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 'bar',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testKeepFlash2()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->keepFlash(['hello', 'foo']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 'bar',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testGetFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);

        $this->assertSame('world', $session->getFlash('hello'));
        $this->assertSame('bar', $session->getFlash('foo'));

        $session->flash('test', ['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $session->getFlash('test'));
        $this->assertSame('foo', $session->getFlash('test\\0'));
        $this->assertNull($session->getFlash('notFound'));

        $session->flash('bar', ['sub' => ['foo' => 'bar']]);
        $this->assertSame(['foo', 'bar'], $session->getFlash('test'));
        $this->assertNull($session->getFlash('test\\notFound'));
    }

    public function testDeleteFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->deleteFlash(['hello', 'foo']);

        $flash = <<<'eot'
array (
  'flash.new.key' => 
  array (
  ),
  'flash.old.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testDeleteFlash2()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->deleteFlash('hello');

        $flash = <<<'eot'
array (
  'flash.new.key' => 
  array (
    1 => 'foo',
  ),
  'flash.old.key' => 
  array (
    0 => 'hello',
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testClearFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->flashs(['hello' => 'world', 'foo' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.new.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.old.key' => 
  array (
  ),
  'flash.data.foo' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->clearFlash();

        $flash = <<<'eot'
array (
  'flash.new.key' => 
  array (
  ),
  'flash.old.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testUnregisterFlash()
    {
        $session = new Session($this->createFileSessionHandler());

        $session->nowFlashs(['hello' => 'world', 'foo' => 'bar']);
        $session->flashs(['hello2' => 'world', 'foo2' => 'bar']);

        $flash = <<<'eot'
array (
  'flash.data.hello' => 'world',
  'flash.old.key' => 
  array (
    0 => 'hello',
    1 => 'foo',
  ),
  'flash.data.foo' => 'bar',
  'flash.data.hello2' => 'world',
  'flash.new.key' => 
  array (
    0 => 'hello2',
    1 => 'foo2',
  ),
  'flash.data.foo2' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );

        $session->unregisterFlash();

        $flash = <<<'eot'
array (
  'flash.old.key' => 
  array (
    0 => 'hello2',
    1 => 'foo2',
  ),
  'flash.data.hello2' => 'world',
  'flash.data.foo2' => 'bar',
)
eot;

        $this->assertSame(
            $flash,
            $this->varExport(
                $session->all()
            )
        );
    }

    public function testPrevUrl()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertNull($session->prevUrl());

        $session->setPrevUrl('foo');

        $this->assertSame('foo', $session->prevUrl());
    }

    public function testDestroy()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertNull($session->getName());

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertNotNull($session->getId());
        $this->assertNotNull($session->getName());

        $session->destroy();
        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertNull($session->getName());
    }

    public function testRegenerateId()
    {
        $session = new Session($this->createFileSessionHandler());

        $this->assertFalse($session->isStart());
        $this->assertNull($session->getId());
        $this->assertNull($session->getName());

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertNotNull($sessionId = $session->getId());
        $this->assertNotNull($session->getName());

        $session->regenerateId();
        $this->assertFalse($sessionId === $session->getId());
    }

    protected function createFileSessionHandler()
    {
        return new File([
            'path' => __DIR__.'/cache',
        ]);
    }
}
