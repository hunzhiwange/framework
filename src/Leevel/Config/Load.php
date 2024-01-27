<?php

declare(strict_types=1);

namespace Leevel\Config;

use Dotenv\Dotenv;
use Leevel\Kernel\IApp;

/**
 * 配置工具类.
 */
class Load
{
    use Env;

    /**
     * 配置路径.
     */
    protected string $dir;

    /**
     * 已经载入数据.
     */
    protected array $loaded = [];

    /**
     * 是否已经载入数据.
     */
    protected bool $isLoaded = false;

    /**
     * 构造函数.
     *
     * @throws \RuntimeException
     */
    public function __construct(string $dir)
    {
        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('Config load dir %s is not exits.', $dir));
        }

        $this->dir = $dir;
    }

    /**
     * 载入配置数据.
     */
    public function loadData(IApp $app): array
    {
        if ($this->isLoaded) {
            return $this->loaded;
        }

        $this->isLoaded = true;

        $env = $this->loadEnvData($app);
        $composer = $this->loadComposerConfig($app->path());
        $data = $this->loadConfigData();

        // 读取额外的配置
        if ($composer['configs']) {
            $data = $this->mergeComposerConfig($data, $app, $composer['configs']);
        }

        // 环境变量
        $data['app'][':env'] = $env;

        // 延迟服务提供者
        $composer['providers'] = array_diff($composer['providers'], $composer['ignores']);
        $providers = $this->loadDeferredProviderData($composer['providers']);
        $data['app'][':deferred_providers'] = $providers;

        // composer 配置
        $data['app'][':composer'] = $composer;

        return $this->loaded = $data;
    }

    /**
     * 载入环境变量数据.
     */
    protected function loadEnvData(IApp $app): array
    {
        $dotenv = Dotenv::createMutable($app->envPath(), $app->envFile());
        $this->setEnvVars($envVars = $dotenv->load());

        return $envVars;
    }

    /**
     * 分析延迟加载服务提供者.
     */
    protected function loadDeferredProviderData(array &$providers): array
    {
        $deferredProviders = $deferredAlias = [];
        foreach ($providers as $k => $provider) {
            if (!class_exists($provider)) {
                unset($providers[$k]);

                continue;
            }

            if ($provider::isDeferred()) {
                $providerAlias = $provider::providers();
                foreach ($providerAlias as $key => $alias) {
                    if (\is_int($key)) {
                        $key = $alias;
                    }
                    $deferredProviders[$key] = $provider;
                }
                $deferredAlias[$provider] = $providerAlias;
                unset($providers[$k]);
            }
        }

        $providers = array_values($providers);

        return [$deferredProviders, $deferredAlias];
    }

    /**
     * 载入 Composer 配置数据.
     */
    protected function loadComposerConfig(string $path): array
    {
        return (new ComposerConfig($path))->loadData();
    }

    /**
     * 载入配置数据.
     *
     * @throws \RuntimeException
     */
    protected function loadConfigData(): array
    {
        $data = [];

        // PHAR 模式下不支持 glob 读取文件
        $files = scandir($this->dir) ?: [];
        if ($files) {
            $files = array_values(array_filter(
                $files,
                fn (string $item): bool => '.php' === substr($item, -4)
            ));
        }

        $findApp = false;
        foreach ($files as $file) {
            $type = substr($file, 0, -4);
            if (false === $findApp && 'app' === $type) {
                $findApp = true;
            }

            $data[$type] = (array) include $this->dir.'/'.$file;
        }

        if (false === $findApp) {
            throw new \RuntimeException('Unable to load the app config file.');
        }

        return $data;
    }

    /**
     * 合并 composer 配置数据.
     *
     * @throws \RuntimeException
     */
    protected function mergeComposerConfig(array $configs, IApp $app, array $configFiles): array
    {
        $path = $app->path();

        foreach ($configFiles as $key => $files) {
            if (!\is_array($files)) {
                $files = [$files];
            }

            $configData = [];
            foreach ($files as $item) {
                if (!is_file($item)) {
                    $item = $path.'/'.$item;
                }
                if (!is_file($item)) {
                    throw new \RuntimeException(sprintf('Config file %s is not exist.', $item));
                }
                $configData = array_merge($configData, include $item);
            }

            if (\array_key_exists($key, $configs)) {
                $configs[$key] = array_merge($configs[$key], $configData);
            } else {
                $configs[$key] = $configData;
            }
        }

        return $configs;
    }
}
