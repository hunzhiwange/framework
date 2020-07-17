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

namespace Leevel\Protocol\Console\Base;

use InvalidArgumentException;
use Leevel\Console\Command;
use Leevel\Console\Option;
use Leevel\Kernel\Proxy\App;
use Leevel\Protocol\IServer;

/**
 * Swoole HTTP 服务启动.
 *
 * @codeCoverageIgnore
 */
abstract class Server extends Command
{
    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->info($this->getLogo());
        $this->warn($this->getVersion());

        $server = $this->createServer();
        if (true === $this->getOption('daemonize')) {
            $server->setDaemonize();
        }

        $this->checkPort((string) $server->option['host'], (int) $server->option['port']);
        $this->checkService((string) $server->option['pid_path']);
        $server->createServer();
        $this->start($server->getServer()->setting);
        $server->startServer();

        return 0;
    }

    /**
     * 创建 server.
     */
    abstract protected function createServer(): IServer;

    /**
     * 返回 Version.
     */
    abstract protected function getVersion(): string;

    /**
     * 显示 Swoole 服务启动项.
     */
    protected function start(array $option): void
    {
        $show = [];
        $show[] = ['QueryPHP', App::version()];
        $show[] = ['Swoole', phpversion('swoole')];
        $basePath = App::path();
        foreach ($option as $key => $val) {
            if (is_array($val)) {
                $val = json_encode($val, JSON_PRETTY_PRINT);
            } else {
                if (in_array($key, ['pid_path', 'document_root'], true)) {
                    $val = str_replace($basePath, '@path', $val);
                }
            }
            $show[] = [$key, $val];
        }

        $this->table(['Item', 'Value'], $show);
    }

    /**
     * 验证端口是否被占用.
     */
    protected function checkPort(string $host, int $port): void
    {
        $bind = $this->portBind($port);
        if ($bind) {
            foreach ($bind as $k => $val) {
                if ('*' === $val['ip'] || $val['ip'] === $host) {
                    $e = sprintf(
                        'The port has been used %s:%s,the port process ID is %s',
                        $val['ip'],
                        $val['port'],
                        $k
                    );

                    throw new InvalidArgumentException($e);
                }
            }
        }
    }

    /**
     * 获取端口占用情况.
     */
    protected function portBind(int $port): array
    {
        $result = [];
        $cmd = "lsof -i :{$port}|awk '$1 != \"COMMAND\"  {print $1, $2, $9}'";
        exec($cmd, $out);
        if (!empty($out)) {
            foreach ($out as $val) {
                $tmp = explode(' ', $val);
                list($ip, $p) = explode(':', $tmp[2]);
                $result[$tmp[1]] = [
                    'cmd'  => $tmp[0],
                    'ip'   => $ip,
                    'port' => $p,
                ];
            }
        }

        return $result;
    }

    /**
     * 验证服务是否被占用.
     *
     * @throws \InvalidArgumentException
     */
    protected function checkService(string $pidPath): void
    {
        if (!is_file($pidPath)) {
            return;
        }

        $pid = (int) explode(PHP_EOL, file_get_contents($pidPath))[0];
        $cmd = "ps ax | awk '{ print $1 }' | grep -e \"^{$pid}$\"";
        exec($cmd, $out);
        if (!empty($out)) {
            $e = sprintf(
                'Swoole pid file %s is already exists,pid is %d',
                $pidPath,
                $pid,
            );

            throw new InvalidArgumentException($e);
        }

        unlink($pidPath);
    }

    /**
     * 返回 QueryPHP Logo.
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

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [
            [
                'daemonize',
                'd',
                Option::VALUE_NONE,
                'Daemon process',
            ],
        ];
    }
}
