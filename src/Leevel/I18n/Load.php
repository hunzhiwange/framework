<?php

declare(strict_types=1);

namespace Leevel\I18n;

/**
 * 语言包工具导入类.
 */
class Load
{
    /**
     * 当前语言包.
     */
    protected string $i18n = 'zh-CN';

    /**
     * 载入路径.
     */
    protected array $dirs = [];

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
    public function __construct(array $dirs = [])
    {
        $this->dirs = $dirs;
    }

    /**
     * 设置当前语言包.
     */
    public function setI18n(string $i18n): self
    {
        $this->i18n = $i18n;

        return $this;
    }

    /**
     * 添加目录.
     */
    public function addDir(array $dirs): self
    {
        $this->dirs = array_unique(array_merge($this->dirs, $dirs));

        return $this;
    }

    /**
     * 载入语言包数据.
     */
    public function loadData(): array
    {
        if (true === $this->isLoaded) {
            return $this->loaded;
        }

        $files = $this->findPoFile($this->parseDir($this->dirs));
        $texts = $this->parsePoData($files);
        $this->isLoaded = true;

        return $this->loaded = $texts;
    }

    /**
     * 分析目录中的 PHP 语言包包含的文件.
     *
     * @throws \RuntimeException
     */
    protected function findPoFile(array $dirs): array
    {
        $files = [];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('I18n load dir %s is not exits.', $dir));
            }
            $files = array_merge($files, $this->getPoFiles($dir));
        }

        return $files;
    }

    /**
     * 获取目录中的 PO 文件.
     *
     * @throws \InvalidArgumentException
     */
    protected function getPoFiles(string $dir): array
    {
        // PHAR 模式下不支持 glob 读取文件
        $files = scandir($dir);
        if (false === $files) {
            throw new \InvalidArgumentException(sprintf('I18n load dir %s is invalid.', $dir));
        }

        $files = array_values(array_filter(
            $files,
            fn (string $item): bool => '.po' === substr($item, -3)
        ));

        return array_map(function (string $file) use ($dir) {
            return $dir.'/'.$file;
        }, $files);
    }

    /**
     * 分析 MO 文件语言包数据.
     */
    protected function parsePoData(array $files): array
    {
        return (new GettextLoader())->loadPoFile($files);
    }

    /**
     * 分析目录.
     */
    protected function parseDir(array $dirs): array
    {
        return array_map(fn (string $dir): string => $dir.'/'.$this->i18n, $dirs);
    }
}
