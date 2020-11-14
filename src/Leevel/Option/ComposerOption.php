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

/**
 * 读取 composer 配置信息.
 *
 * - 用于实现自动注册服务提供者、语言包、命令等核心配置.
 */
class ComposerOption
{
    /**
     * 基础路径.
     *
     * @var string
     */
    protected string $path;

    /**
     * 是否载入.
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
     * 支持的配置项.
     *
     * - 其它信息将被过滤掉.
     *
     * @return array
     */
    protected array $supportedOptions = [
        'providers',
        'ignores',
        'commands',
        'options',
        'i18ns',
        'metas',
    ];

    /**
     * 构造函数.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * 读取配置信息.
     */
    public function loadData(): array
    {
        if (true === $this->isLoaded) {
            return $this->loaded;
        }

        $data = [];
        foreach ($this->getPackages() as $package) {
            if ($option = $this->getLeevelOption($package)) {
                $data = $data ? $this->mergeOption($data, $option) : $option;
            }
        }

        $appOption = $this->getAppComposerOption();
        $this->isLoaded = true;

        return $this->loaded = $this->mergeOption($data, $appOption);
    }

    /**
     * 获取包列表.
     */
    protected function getPackages(): array
    {
        $packages = [];
        if (is_file($installedJson = $this->path.'/vendor/composer/installed.json')) {
            $packages = $this->getFileContent($installedJson);
            // 兼容 Composer 2.0
            if (isset($packages['packages'])) {
                $packages = $packages['packages'];
            }
        }

        return $packages;
    }

    /**
     * 合并配置信息.
     */
    protected function mergeOption(array $olds, array $options): array
    {
        $result = [];
        foreach ($this->supportedOptions as $item) {
            $result[$item] = array_merge($olds[$item] ?? [], $options[$item] ?? []);
        }

        return $result;
    }

    /**
     * 获取一个包的 leevel 配置.
     */
    protected function getLeevelOption(array $package): array
    {
        if (empty($package['extra']['leevel'])) {
            return [];
        }

        return $this->normalizeOption($package['extra']['leevel']);
    }

    /**
     * 格式化配置信息.
     */
    protected function normalizeOption(array $options): array
    {
        $result = [];
        foreach ($this->supportedOptions as $item) {
            $tmp = $options[$item] ?? [];
            if (!is_array($tmp)) {
                $tmp = [$tmp];
            }
            $result[$item] = $tmp;
        }

        return $result;
    }

    /**
     * 取得应用的 composer 配置.
     */
    protected function getAppComposerOption(): array
    {
        if (!is_file($path = $this->path.'/composer.json')) {
            return [];
        }

        return $this->getLeevelOption($this->getFileContent($path));
    }

    /**
     * 获取配置信息.
     */
    protected function getFileContent(string $path): array
    {
        return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }
}
