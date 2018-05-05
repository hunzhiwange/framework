<?php declare(strict_types=1);
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

use Exception;
use Dotenv\Dotenv;
use RuntimeException;
use Leevel\Bootstrap\IProject;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;

/**
 * 配置工具类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.18
 * @version 1.0
 */
class Load
{
    /**
     * 配置路径
     *
     * @var string
     */
    protected $dir = [];

    /**
     * 已经载入数据
     *
     * @var array
     */
    protected $loaded = [];

    /**
     * 构造函数
     *
     * @param string $dir
     * @return void
     */
    public function __construct(string $dir)
    {
        if (! is_string($dir)) {
            throw new RuntimeException('Option load dir is not exits.');
        }

        $this->dir = $dir;
    }

    /**
     * 载入配置数据
     *
     * @param \Leevel\Bootstrap\IProject $project
     * @return array
     */
    public function loadData(IProject $project): array
    {
        if ($this->loaded) {
            return $this->loaded;
        }

        $env = $this->loadEnvData($project);

        $data = $this->loadOptionData();

        $composer = $this->loadComposerOption($project->path());

        $providers = $this->loadDeferredProviderData($data['app']['provider']);

        $data['app']['_env'] = $env;

        $data['app']['_providers'] = $providers;

        return $this->loaded = $data;
    }

    /**
     * 载入环境变量数据
     * 
     * @param \Leevel\Bootstrap\IProject $project
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
     * 分析延迟加载服务提供者
     * 
     * @param array $providers
     * @return array
     */
    protected function loadDeferredProviderData(array $providers): array
    {
       $deferredProviders = $deferredAlias = [];

       foreach ($providers as $provider) {
            if (! class_exists($provider)) {
                continue;
            }

            if ($provider::isDeferred()) {
                $providers = $provider::providers();
                foreach ($providers as $key => $alias) {
                    if (is_int($key)) {
                        $key = $alias;
                    }

                    $deferredProviders[$key] = $provider;
                }

                $deferredAlias[$provider] = $providers;
            }
        }

        return [
            $deferredProviders,
            $deferredAlias
        ];
    }

    /**
     * 载入 Composer 配置数据
     * 
     * @param string $path
     * @return array
     */
    protected function loadComposerOption(string $path): array
    {
        $data = [];

        $composer = new ComposerOption($path);

        $composer->loadData();

        exit();


        return $data;
    }

    /**
     * 载入配置数据
     *
     * @return array
     */
    protected function loadOptionData(): array
    {
        $data = [];

        $files = glob($this->dir . '/*.php');

        $findApp = false;

        foreach ($files as $file) {
            $type = substr(basename($file), 0, -4);

            if ($type == 'app') {
                $findApp = true;
            }

            $data[$type] = (array) include $file;
        }


        if ($findApp === false) {
            throw new Exception('Unable to load the app option file.');
        }

        return $data;
    }
}
