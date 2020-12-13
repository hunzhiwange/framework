<?php

declare(strict_types=1);

namespace Leevel\Kernel\Bootstrap;

use Leevel\Kernel\IApp;
use Leevel\Option\Env;
use Leevel\Option\IOption;
use Leevel\Option\Load;
use Leevel\Option\Option;
use RuntimeException;

/**
 * 载入配置.
 */
class LoadOption
{
    use Env;

    /**
     * 响应.
     */
    public function handle(IApp $app): void
    {
        $this->checkRuntimeEnv($app);

        if ($app->isCachedOption()) {
            $data = (array) include $app->optionCachedPath();
            $this->setEnvVars($data['app'][':env'], function(string $name, null|bool|string $value = null): void {
                // 保持和 Dotenv 兼容
                $_SERVER[$name] = $_ENV[$name] = $value;
            });
        } else {
            $load = new Load($app->optionPath());
            $data = $load->loadData($app);
        }

        $app
            ->container()
            ->instance('option', $option = new Option($data));
        $app
            ->container()
            ->alias('option', [IOption::class, Option::class]);

        $this->initialization($option);
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
            $e = sprintf('Env file `%s` was not found.', $fullFile);

            throw new RuntimeException($e);
        }

        $app->setEnvFile($file);
    }

    /**
     * 初始化处理.
     *
     * @codeCoverageIgnore
     */
    protected function initialization(Option $option): void
    {
        mb_internal_encoding('UTF-8');

        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($option->get('time_zone', 'UTC'));
        }

        if (\PHP_SAPI === 'cli') {
            return;
        }

        if (function_exists('gz_handler') && $option->get('start_gzip')) {
            ob_start('gz_handler');
        } else {
            ob_start();
        }
    }
}
