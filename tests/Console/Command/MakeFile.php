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

namespace Tests\Console\Command;

use Leevel\Console\Argument;
use Leevel\Console\Make;

/**
 * makeFile.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.23
 *
 * @version 1.0
 */
class MakeFile extends Make
{
    protected $name = 'make:test';

    protected $description = 'Create a test file.';

    public function handle()
    {
        $this->setTemplatePath(__DIR__.'/'.($this->argument('template') ?: 'template'));

        $this->setCustomReplaceKeyValue('key1', 'hello key1');

        $this->setCustomReplaceKeyValue('key2', 'hello key2');

        $this->setCustomReplaceKeyValue(['key3' => 'hello key3', 'key4' => 'hello key4']);
        $this->setSaveFilePath(__DIR__.'/cache/'.$this->argument('name'));

        $this->setMakeType('test');

        parent::handle();
    }

    protected function getArguments()
    {
        return [
            [
                'name',
                Argument::OPTIONAL,
                'This is a name.',
            ],
            [
                'template',
                Argument::OPTIONAL,
                'This is a template.',
            ],
        ];
    }
}
