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
        $this->assertNull($session->getId());
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
