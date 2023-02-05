<?php

declare(strict_types=1);

namespace Leevel\Console;

/**
 * 命令行工具类导入类.
 */
class Load
{
    /**
     * 载入命名空间.
     */
    protected array $namespaces = [];

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
     */
    public function __construct(array $namespaces = [])
    {
        $this->namespaces = $namespaces;
    }

    /**
     * 添加命名空间.
     */
    public function addNamespace(array $namespaces): self
    {
        $this->namespaces = array_merge($this->namespaces, $namespaces);

        return $this;
    }

    /**
     * 载入命令行数据.
     */
    public function loadData(): array
    {
        if (true === $this->isLoaded) {
            return $this->loaded;
        }

        $this->isLoaded = true;

        $files = $this->findConsoleFile($this->namespaces);

        return $this->loaded = $files;
    }

    /**
     * 分析目录中的 PHP 命令包包含的文件.
     *
     * @throws \RuntimeException
     */
    public function findConsoleFile(array $namespaces): array
    {
        $files = [];
        foreach ($namespaces as $key => $dir) {
            if (!is_dir($dir)) {
                $e = sprintf('Console load dir %s is not exits.', $dir);

                throw new \RuntimeException($e);
            }

            $currentFiles = glob($dir.'/*.php');
            $currentFiles = array_map(
                fn (string $item): string => $key.'\\'.basename($item, '.php'),
                $currentFiles,
            );

            $files = array_merge($files, $currentFiles);
        }

        return $files;
    }
}
