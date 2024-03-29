<?php

declare(strict_types=1);

namespace Tests\Log;

use Leevel\Log\Syslog;
use Monolog\Logger;
use Tests\TestCase;

final class SyslogTest extends TestCase
{
    public function testBaseUse(): void
    {
        $syslog = new Syslog();
        $data = $this->getLogData();
        $syslog->info(...$data);
        $this->assertInstanceof(Logger::class, $syslog->getMonolog());
    }

    protected function getLogData(): array
    {
        return [
            'hello',
            [
                'hello',
                'world',
            ],
        ];
    }
}
