<?php

declare(strict_types=1);

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
        $syslog->store($data);
        $syslog->store($data);
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
                ILog::LEVEL_INFO,
                'hello',
                [
                    'hello',
                    'world',
                ],
            ],
        ];
    }
}
