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

namespace Tests\Log;

use Leevel\Log\ILog;
use Leevel\Log\Syslog;
use Monolog\Logger;
use Tests\TestCase;

class SyslogTest extends TestCase
{
    public function testBaseUse(): void
    {
        $syslog = new Syslog();

        $data = $this->getLogData();

        $this->assertNull($syslog->flush($data));

        $this->assertInstanceof(Logger::class, $syslog->getMonolog());
    }

    public function testNormalizeLevelWithDefaultDebug(): void
    {
        $syslog = new Syslog();

        $data = $this->getLogData();
        $data[0][0] = 'notFound';

        $this->assertNull($syslog->flush($data));
    }

    public function testNormalizeMonologLevelWithDefaultDebug(): void
    {
        $syslog = new Syslog([
            'level' => 'notFound',
        ]);

        $data = $this->getLogData();

        $this->assertNull($syslog->flush($data));
    }

    protected function getLogData(): array
    {
        return [
            [
                ILog::INFO,
                'hello',
                [
                    'hello',
                    'world',
                ],
            ],
        ];
    }
}
