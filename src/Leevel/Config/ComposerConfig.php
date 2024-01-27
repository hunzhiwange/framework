<?php

declare(strict_types=1);

namespace Leevel\Config;

/**
 * 读取 composer 配置信息.
 *
 * - 用于实现自动注册服务提供者、语言包、命令等核心配置.
 */
class ComposerConfig
{
    /**
     * 基础路径.
     */
    protected string $path;

    /**
     * 是否载入.
     */
    protected array $loaded = [];

    /**
     * 是否已经载入数据.
     */
    protected bool $isLoaded = false;

    /**
     * 支持的配置项.
     *
     * - 其它信息将被过滤掉.
     */
    protected array $supportedConfigs = [
        'providers',
        'ignores',
        'commands',
        'configs',
        'i18ns',
        'i18n-paths',
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
        if ($this->isLoaded) {
            return $this->loaded;
        }

        $data = [];
        foreach ($this->getPackages() as $package) {
            if ($config = $this->getLeevelConfig($package)) {
                $data = $data ? $this->mergeConfig($data, $config) : $config;
            }
        }

        $appConfig = $this->getAppComposerConfig();
        $this->isLoaded = true;

        return $this->loaded = $this->mergeConfig($data, $appConfig);
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
    protected function mergeConfig(array $olds, array $configs): array
    {
        $result = [];
        foreach ($this->supportedConfigs as $item) {
            $result[$item] = array_merge($olds[$item] ?? [], $configs[$item] ?? []);
        }

        return $result;
    }

    /**
     * 获取一个包的 leevel 配置.
     */
    protected function getLeevelConfig(array $package): array
    {
        if (empty($package['extra']['leevel'])) {
            return [];
        }

        return $this->normalizeConfig($package['extra']['leevel']);
    }

    /**
     * 格式化配置信息.
     */
    protected function normalizeConfig(array $configs): array
    {
        $result = [];
        foreach ($this->supportedConfigs as $item) {
            $tmp = $configs[$item] ?? [];
            if (!\is_array($tmp)) {
                $tmp = [$tmp];
            }
            $result[$item] = $tmp;
        }

        return $result;
    }

    /**
     * 取得应用的 composer 配置.
     */
    protected function getAppComposerConfig(): array
    {
        if (!is_file($path = $this->path.'/composer.json')) {
            return [];
        }

        return $this->getLeevelConfig($this->getFileContent($path));
    }

    /**
     * 获取配置信息.
     */
    protected function getFileContent(string $path): array
    {
        return (array) json_decode(file_get_contents($path) ?: '', true, 512, JSON_THROW_ON_ERROR);
    }
}
