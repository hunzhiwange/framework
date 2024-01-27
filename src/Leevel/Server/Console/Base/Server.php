<?php

declare(strict_types=1);

namespace Leevel\Server\Console\Base;

use Leevel\Console\Command;
use Leevel\Kernel\Proxy\App;

/**
 * HTTP 服务启动.
 */
abstract class Server extends Command
{
    use Base;

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $this->showBaseInfo();
        $server = $this->createServer();
        $config = $server->getConfig();
        $this->checkPort((string) ($config['host'] ?? '127.0.0.1'), (int) ($config['port'] ?? 9527));
        $this->checkService((string) ($config['pid_path'] ?? ''));
        $this->init($config);
        $server->start();

        return self::SUCCESS;
    }

    /**
     * 显示服务启动项.
     */
    protected function init(array $config): void
    {
        $result = [];
        // @phpstan-ignore-next-line
        $result[] = ['QueryPHP', App::version()];
        $result[] = ['Swoole', phpversion('swoole')];
        foreach ($config as $key => $val) {
            if (\is_array($val)) {
                $val = var_export($val, true);
            }

            $result[] = [$key, $val];
        }

        $this->table(['Item', 'Value'], $result);
    }

    /**
     * 验证端口是否被占用.
     *
     * @throws \InvalidArgumentException
     */
    protected function checkPort(string $host, int $port): void
    {
        $bind = $this->portBind($port);
        if ($bind) {
            foreach ($bind as $k => $val) {
                if ('*' === $val['ip'] || $val['ip'] === $host) {
                    throw new \InvalidArgumentException(sprintf(
                        'The port has been used %s:%s,the port process ID is %s',
                        $val['ip'],
                        $val['port'],
                        $k
                    ));
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
                [$ip, $p] = explode(':', $tmp[2]);
                $result[$tmp[1]] = [
                    'cmd' => $tmp[0],
                    'ip' => $ip,
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

        $pid = (int) file_get_contents($pidPath);
        $cmd = "ps ax | awk '{ print $1 }' | grep -e \"^{$pid}$\"";
        exec($cmd, $out);
        if (!empty($out)) {
            throw new \InvalidArgumentException(sprintf(
                'Swoole pid file %s is already exists,pid is %d',
                $pidPath,
                $pid,
            ));
        }

        unlink($pidPath);
    }
}
