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

use Leevel\Console\Command;

/**
 * call other command.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.24
 *
 * @version 1.0
 */
class CallOtherCommand extends Command
{
    protected $name = 'call:other';

    protected $description = 'call other command for test.';

    public function handle()
    {
        $this->info('call other command test.');

        $this->info('argument is '.json_encode($this->argument()));

        $this->info('option is '.json_encode($this->option()));

        $this->table([
            'Item',
            'Value',
        ], [
            ['hello', 'world'],
            ['foo', 'bar'],
        ]);

        $this->info($this->time('test time'));

        $this->question('a question');

        $this->error('a error message');

        $this->error('a error message');

        $this->call('load1:test1');
    }
}
