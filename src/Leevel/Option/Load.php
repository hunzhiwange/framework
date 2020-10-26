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

namespace Leevel\Option;

use Dotenv\Dotenv;
use Leevel\Kernel\IApp;
use RuntimeException;

/**
 * 配置工具类.
 */
class Load
{
    /**
     * 配置路径.
     *
     * @var string
     */
    protected string $dir;

    /**
     * 已经载入数据.
     *
     * @var array
     */
    protected array $loaded = [];

    /**
     * 是否已经载入数据.
     *
     * @var bool
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
            $e = sprintf('Option load dir %s is not exits.', $dir);

            throw new RuntimeException($e);
        }

        $this->dir = $dir;
    }

    /**
     * 载入配置数据.
     */
    public function loadData(IApp $app): array
    {
        if (true === $this->isLoaded) {
            return $this->loaded;
        }

        $this->isLoaded = true;

        $env = $this->loadEnvData($app);
        $composer = $this->loadComposerOption($app->path());
        $data = $this->loadOptionData();

        // 读取额外的配置
        if ($composer['options']) {
            $data = $this->mergeComposerOption($data, $app, $composer['options']);
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

        return $dotenv->load();
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
                    if (is_int($key)) {
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
    protected function loadComposerOption(string $path): array
    {
        return (new ComposerOption($path))->loadData();
    }

    /**
     * 载入配置数据.
     *
     * @throws \RuntimeException
     */
    protected function loadOptionData(): array
    {
        $data = [];
        $files = glob($this->dir.'/*.php') ?: [];
        $findApp = false;
        foreach ($files as $file) {
            $type = substr(basename($file), 0, -4);
            if ('app' === $type) {
                $findApp = true;
            }
            $data[$type] = (array) include $file;
        }

        if (false === $findApp) {
            $e = 'Unable to load the app option file.';

            throw new RuntimeException($e);
        }

        return $data;
    }

    /**
     * 合并 composer 配置数据.
     *
     * @throws \RuntimeException
     */
    protected function mergeComposerOption(array $options, IApp $app, array $optionFiles): array
    {
        $path = $app->path();

        foreach ($optionFiles as $key => $files) {
            if (!is_array($files)) {
                $files = [$files];
            }

            $optionData = [];
            foreach ($files as $item) {
                if (!is_file($item)) {
                    $item = $path.'/'.$item;
                }
                if (!is_file($item)) {
                    $e = sprintf('Option file %s is not exist.', $item);

                    throw new RuntimeException($e);
                }
                $optionData = array_merge($optionData, include $item);
            }

            if (array_key_exists($key, $options)) {
                $options[$key] = array_merge($options[$key], $optionData);
            } else {
                $options[$key] = $optionData;
            }
        }

        return $options;
    }
}
