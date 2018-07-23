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

namespace Tests\Console;

use Leevel\Console\Make;
use Tests\Console\Command\MakeFile;
use Tests\TestCase;

/**
 * make test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.23
 *
 * @version 1.0
 */
class MakeTest extends TestCase
{
    use BaseMake;

    public function testBaseUse()
    {
        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test',
        ]);

        $this->assertContains('test <test> created successfully.', $result);

        $file = __DIR__.'/Command/cache/test';

        $this->assertContains('hello make file', $content = file_get_contents($file));

        $this->assertContains('hello key1', $content);
        $this->assertContains('hello key2', $content);
        $this->assertContains('hello key3', $content);
        $this->assertContains('hello key4', $content);

        unlink($file);
        rmdir(dirname($file));
    }

    public function testFileAleadyExists()
    {
        $file = __DIR__.'/Command/cache/test2';
        $dirname = dirname($file);
        mkdir($dirname, 0777, true);
        file_put_contents($file, 'foo');

        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test2',
        ]);

        $this->assertContains('File is already exits.', $result);

        unlink($file);
        rmdir($dirname);
    }

    public function testFileIsNotWritable()
    {
        $file = __DIR__.'/Command/cache/test3';
        $dirname = dirname($file);
        mkdir($dirname, 0444, true);

        $result = $this->runCommand(new MakeFile(), [
            'command'     => 'make:test',
            'name'        => 'test3',
        ]);

        $this->assertContains('Can not write file.', $result);

        rmdir($dirname);
    }

    public function testTemplateNotFound()
    {
        $file = __DIR__.'/Command/cache/test4';
        $dirname = dirname($file);

        $result = $this->runCommand(new MakeFile(), [
            'command'         => 'make:test',
            'name'            => 'test4',
            'template'        => 'notFound',
        ]);

        $this->assertContains('Template not found.', $result);
    }
}
