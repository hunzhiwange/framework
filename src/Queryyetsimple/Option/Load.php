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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Option;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;
use Exception;
use Leevel\Kernel\IProject;
use RuntimeException;

/**
 * 配置工具类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.18
 *
 * @version 1.0
 */
class Load
{
    /**
     * 配置路径.
     *
     * @var string
     */
    protected $dir = [];

    /**
     * 已经载入数据.
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * 构造函数.
     *
     * @param string $dir
     */
    public function __construct(string $dir)
    {
        if (!is_string($dir)) {
            throw new RuntimeException('Option load dir is not exits.');
        }

        $this->dir = $dir;
    }

    /**
     * 载入配置数据.
     *
     * @param \Leevel\Kernel\IProject $project
     *
     * @return array
     */
    public function loadData(IProject $project): array
    {
        if ($this->loaded) {
            return $this->loaded;
        }

        $env = $this->loadEnvData($project);

        $composer = $this->loadComposerOption($project->path());

        $data = $this->loadOptionData();

        // 读取额外的配置
        if ($composer['options']) {
            $data = $this->mergeComposerOption($data, $project, $composer['options']);
        }

        // 环境变量
        $data['app']['_env'] = $env;

        // 延迟服务提供者
        $composer['providers']              = array_diff($composer['providers'], $composer['ignores']);
        $providers                          = $this->loadDeferredProviderData($composer['providers']);
        $data['app']['_deferred_providers'] = $providers;

        // composer 配置
        $data['app']['_composer'] = $composer;

        return $this->loaded = $data;
    }

    /**
     * 载入环境变量数据.
     *
     * @param \Leevel\Kernel\IProject $project
     *
     * @return array
     */
    protected function loadEnvData(IProject $project): array
    {
        try {
            (new Dotenv($project->pathEnv(), $project->envFile()))->load();
        } catch (InvalidPathException $e) {
            exit($e->getMessage());
        } catch (InvalidFileException $e) {
            exit($e->getMessage());
        }

        return $_ENV;
    }

    /**
     * 分析延迟加载服务提供者.
     *
     * @param array $providers
     *
     * @return array
     */
    protected function loadDeferredProviderData(array &$providers): array
    {
        $deferredProviders = $deferredAlias = [];

        foreach ($providers as $k => $provider) {
            if (!class_exists($provider)) {
                unset($providers[$key]);

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

        return [
            $deferredProviders,
            $deferredAlias,
        ];
    }

    /**
     * 载入 Composer 配置数据.
     *
     * @param string $path
     *
     * @return array
     */
    protected function loadComposerOption(string $path): array
    {
        return (new ComposerOption($path))->loadData();
    }

    /**
     * 载入配置数据.
     *
     * @return array
     */
    protected function loadOptionData(): array
    {
        $data = [];

        $files = glob($this->dir.'/*.php');

        $findApp = false;

        foreach ($files as $file) {
            $type = substr(basename($file), 0, -4);

            if ('app' === $type) {
                $findApp = true;
            }

            $data[$type] = (array) include $file;
        }

        if (false === $findApp) {
            throw new Exception('Unable to load the app option file.');
        }

        return $data;
    }

    /**
     * 合并 composer 配置数据.
     *
     * @param array                   $options
     * @param \Leevel\Kernel\IProject $project
     * @param array                   $optionFiles
     *
     * @return array
     */
    protected function mergeComposerOption(array $options, IProject $project, array $optionFiles): array
    {
        $data = [];

        $path = $project->path();

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
                    throw new Exception(sprintf('Option file %s is not exist.', $item));
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
