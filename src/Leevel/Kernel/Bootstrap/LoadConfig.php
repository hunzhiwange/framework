<?php

declare(strict_types=1);

namespace Leevel\Kernel\Bootstrap;

use Leevel\Config\Config;
use Leevel\Config\Env;
use Leevel\Config\IConfig;
use Leevel\Config\Load;
use Leevel\Kernel\IApp;

/**
 * 载入配置.
 */
class LoadConfig
{
    use Env;

    /**
     * 响应.
     */
    public function handle(IApp $app): void
    {
        $this->checkRuntimeEnv($app);

        if ($app->isCachedConfig()) {
            $data = (array) include $app->configCachedPath();
            $this->setEnvVars($data['app'][':env'], function (string $name, null|bool|string $value = null): void {
                // 保持和 Dotenv 兼容
                $_SERVER[$name] = $_ENV[$name] = $value;
            });
        } else {
            $load = new Load($app->configPath());
            $data = $load->loadData($app);
        }

        $app
            ->container()
            ->instance('config', $config = new Config($data))
        ;
        $app
            ->container()
            ->alias('config', [IConfig::class, Config::class])
        ;

        $this->initialization($config);
    }

    /**
     * 载入运行时环境变量.
     *
     * @throws \RuntimeException
     */
    protected function checkRuntimeEnv(IApp $app): void
    {
        if (!getenv('RUNTIME_ENVIRONMENT')) {
            return;
        }

        $file = '.'.getenv('RUNTIME_ENVIRONMENT');
        if (!is_file($fullFile = $app->envPath().'/'.$file)) {
            throw new \RuntimeException(sprintf('Env file `%s` was not found.', $fullFile));
        }

        $app->setEnvFile($file);
    }

    /**
     * 初始化处理.
     *
     * @codeCoverageIgnore
     */
    protected function initialization(Config $config): void
    {
        mb_internal_encoding('UTF-8');

        if (\function_exists('date_default_timezone_set')) {
            // @phpstan-ignore-next-line
            date_default_timezone_set($config->get('time_zone', 'UTC'));
        }

        if (\PHP_SAPI === 'cli') {
            return;
        }

        if (\function_exists('gz_handler') && $config->get('start_gzip')) {
            // @phpstan-ignore-next-line
            ob_start('gz_handler');
        } else {
            ob_start();
        }
    }
}
