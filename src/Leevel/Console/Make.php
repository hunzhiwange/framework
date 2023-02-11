<?php

declare(strict_types=1);

namespace Leevel\Console;

use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Kernel\IApp;

/**
 * 生成器基类.
 */
abstract class Make extends Command
{
    /**
     * 创建类型.
     */
    protected string $makeType = '';

    /**
     * 文件保存路径.
     */
    protected string $saveFilePath = '';

    /**
     * 模板路径.
     */
    protected string $templatePath = '';

    /**
     * 模板源码
     */
    protected string $templateSource = '';

    /**
     * 保存的模板结果.
     */
    protected string $templateResult = '';

    /**
     * 自定义替换.
     */
    protected array $customReplaceKeyValue = [];

    /**
     * 全局替换.
     */
    protected static array $globalReplace = [];

    /**
     * 命令空间.
     */
    protected ?string $namespace = null;

    /**
     * 设置全局变量替换.
     */
    public static function setGlobalReplace(array $replace): void
    {
        static::$globalReplace = $replace;
    }

    /**
     * 获取全局变量替换.
     */
    public static function getGlobalReplace(): array
    {
        return static::$globalReplace;
    }

    /**
     * 创建文件.
     */
    protected function create(): void
    {
        // 替换模板变量
        $this->replaceTemplateSource();

        // 保存文件
        $this->saveTemplateResult();

        // 保存成功输出消息
        // @phpstan-ignore-next-line
        $this->info(sprintf('%s <%s> created successfully.', $this->getMakeType(), $this->getArgument('name')));
        $this->comment($this->formatFile($this->getSaveFilePath()));
    }

    /**
     * 替换模板变量.
     */
    protected function replaceTemplateSource(): void
    {
        // 解析模板源码
        $this->parseTemplateSource();

        // 获取替换变量
        $sourceAndReplace = $this->parseSourceAndReplace();

        // 第一次替换基本变量
        // 第二次替换基本变量中的变量
        $templateSource = str_replace($sourceAndReplace[0], $sourceAndReplace[1], $this->getTemplateSource());
        $this->templateResult = str_replace($sourceAndReplace[0], $sourceAndReplace[1], $templateSource);
    }

    /**
     * 保存模板.
     *
     * @throws \RuntimeException
     */
    protected function saveTemplateResult(): void
    {
        $saveFilePath = $this->getSaveFilePath();
        if (is_file($saveFilePath)) {
            $e = 'File is already exits.'.PHP_EOL.
                $this->formatFile($saveFilePath);

            throw new \RuntimeException($e);
        }

        CreateFile::handle($saveFilePath, $this->getTemplateResult());
    }

    /**
     * 获取模板编译结果.
     */
    protected function getTemplateResult(): string
    {
        return $this->templateResult;
    }

    /**
     * 分析模板源码.
     *
     * @throws \RuntimeException
     */
    protected function parseTemplateSource(): void
    {
        $templateSource = $this->getTemplatePath();
        if (!is_file($templateSource)) {
            $e = 'Stub not found.'.PHP_EOL.$this->formatFile($templateSource);

            throw new \RuntimeException($e);
        }

        $this->templateSource = file_get_contents($templateSource) ?: '';
    }

    /**
     * 获取模板源码
     */
    protected function getTemplateSource(): string
    {
        return $this->templateSource;
    }

    /**
     * 分析变量替换.
     */
    protected function parseSourceAndReplace(): array
    {
        $replaceKeyValue = array_merge(static::$globalReplace, $this->getDefaultReplaceKeyValue());
        $sourceKey = array_map(function ($item) {
            return '{{'.$item.'}}';
        }, array_keys($replaceKeyValue));
        $replace = array_values($replaceKeyValue);

        return [$sourceKey, $replace];
    }

    /**
     * 取得系统的替换变量.
     */
    protected function getDefaultReplaceKeyValue(): array
    {
        $defaultReplace = [
            'namespace' => $this->getNamespace(),
            'date_y' => date('Y'),
        ];

        return array_merge($defaultReplace, $this->getCustomReplaceKeyValue());
    }

    /**
     * 设置文件保存路径.
     */
    protected function setSaveFilePath(string $saveFilePath): void
    {
        $this->saveFilePath = $saveFilePath;
    }

    /**
     * 读取文件保存路径.
     */
    protected function getSaveFilePath(): string
    {
        return $this->saveFilePath;
    }

    /**
     * 获取命名空间路径.
     */
    protected function getNamespacePath(): string
    {
        /** @var IApp $app */
        $app = $this
            ->getContainer()
            ->make(IApp::class)
        ;

        return $app->namespacePath($this->getNamespace()).'/';
    }

    /**
     * 分析命名空间.
     */
    protected function parseNamespace(): void
    {
        $namespace = (string) ($this->getOption('namespace') ?: 'App');
        $this->setNamespace(ucfirst($namespace));
    }

    /**
     * 设置命名空间.
     */
    protected function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * 读取命名空间.
     */
    protected function getNamespace(): string
    {
        return $this->namespace ?: '';
    }

    /**
     * 整理子目录.
     */
    protected function normalizeSubDir(?string $subDir = null, bool $isNamespace = false): string
    {
        if (!$subDir) {
            return '';
        }

        $subDir = str_replace('\\', '/', $subDir);
        $subDir = explode('/', $subDir);
        $subDir = array_map(fn ($item) => ucfirst($item), $subDir);

        return false === $isNamespace ? implode('/', $subDir).'/' : '\\'.implode('\\', $subDir);
    }

    /**
     * 设置创建类型.
     */
    protected function setMakeType(string $makeType): void
    {
        $this->makeType = $makeType;
    }

    /**
     * 读取创建类型.
     */
    protected function getMakeType(): string
    {
        return $this->makeType;
    }

    /**
     * 设置模板文件路径.
     */
    protected function setTemplatePath(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    /**
     * 读取模板文件路径.
     */
    protected function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * 设置自定义变量替换.
     */
    protected function setCustomReplaceKeyValue(mixed $key, mixed $value = null): void
    {
        if (\is_array($key)) {
            $this->customReplaceKeyValue = array_merge($this->customReplaceKeyValue, $key);
        } else {
            $this->customReplaceKeyValue[$key] = $value;
        }
    }

    /**
     * 读取自定义变量替换.
     */
    protected function getCustomReplaceKeyValue(): array
    {
        return $this->customReplaceKeyValue;
    }

    /**
     * 格式化文件路径.
     */
    protected function formatFile(string $file): string
    {
        return $file;
    }
}
