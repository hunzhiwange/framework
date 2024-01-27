<?php

declare(strict_types=1);

namespace Leevel\Server\Console\Base;

use Leevel\Di\Container;
use Leevel\Server\IServer;

trait Base
{
    protected function getServerConfig(): array
    {
        return $this->createServer()->getConfig();
    }

    protected function showBaseInfo(): void
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion($this->name));
    }

    /**
     * 创建 server.
     */
    protected function createServer(): IServer
    {
        return Container::singletons()->make('servers')->connect($this->connect);
    }

    /**
     * 返回 Version.
     */
    protected function getVersion(string $type): string
    {
        return PHP_EOL.'                     '.strtoupper($type).PHP_EOL;
    }

    /**
     * 返回 Logo.
     */
    protected function getLogo(): string
    {
        return <<<'queryphp'
            _____________                           _______________
             ______/     \__  _____  ____  ______  / /_  _________
              ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
               __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
                 \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
                    \_\                /_/_/         /_/
            queryphp;
    }
}
