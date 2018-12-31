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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Console;

use InvalidArgumentException;
use RuntimeException;

/**
 * 生成器基类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.02
 *
 * @version 1.0
 */
abstract class Make extends Command
{
    /**
     * 创建类型.
     *
     * @var string
     */
    protected $makeType;

    /**
     * 文件保存路径.
     *
     * @var string
     */
    protected $saveFilePath;

    /**
     * 模板路径.
     *
     * @var string
     */
    protected $templatePath;

    /**
     * 模板源码
     *
     * @var string
     */
    protected $templateSource;

    /**
     * 保存的模板结果.
     *
     * @var string
     */
    protected $templateResult;

    /**
     * 自定义替换.
     *
     * @var array
     */
    protected $customReplaceKeyValue = [];

    /**
     * 全局替换.
     *
     * @var array
     */
    protected static $globalReplace = [];

    /**
     * 命令空间.
     *
     * @var string
     */
    protected $namespace;

    /**
     * 设置全局变量替换.
     *
     * @param array $replace
     */
    public static function setGlobalReplace(array $replace): void
    {
        static::$globalReplace = $replace;
    }

    /**
     * 获取全局变量替换.
     *
     * @param array $replace
     *
     * @return array
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
        $this->info(sprintf('%s <%s> created successfully.', $this->getMakeType(), $this->argument('name')));
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
     */
    protected function saveTemplateResult(): void
    {
        $saveFilePath = $this->getSaveFilePath();

        if (is_file($saveFilePath)) {
            throw new RuntimeException(
                'File is already exits.'.PHP_EOL.
                $this->formatFile($saveFilePath)
            );
        }

        $dirname = dirname($saveFilePath);

        if (!is_dir($dirname)) {
            if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
                throw new InvalidArgumentException(
                    sprintf('Unable to create the %s directory.', $dirname)
                );
            }

            mkdir($dirname, 0777, true);
        }

        if (!is_writable($dirname) ||
            !file_put_contents($saveFilePath, $this->getTemplateResult())) {
            throw new RuntimeException(
                'Can not write file.'.PHP_EOL.
                $this->formatFile($saveFilePath)
            );
        }
    }

    /**
     * 获取模板编译结果.
     *
     * @return string
     */
    protected function getTemplateResult(): string
    {
        return $this->templateResult;
    }

    /**
     * 分析模板源码.
     */
    protected function parseTemplateSource(): void
    {
        $templateSource = $this->getTemplatePath();

        if (!is_file($templateSource)) {
            throw new RuntimeException(
                'Stub not found.'.PHP_EOL.
                $this->formatFile($templateSource)
            );
        }

        $this->templateSource = file_get_contents($templateSource);
    }

    /**
     * 获取模板源码
     *
     * @return string
     */
    protected function getTemplateSource(): string
    {
        return $this->templateSource;
    }

    /**
     * 分析变量替换.
     *
     * @return array
     */
    protected function parseSourceAndReplace(): array
    {
        $replaceKeyValue = array_merge(static::$globalReplace, $this->getDefaultReplaceKeyValue());

        $sourceKey = array_map(function ($item) {
            return '{{'.$item.'}}';
        }, array_keys($replaceKeyValue));

        $replace = array_values($replaceKeyValue);

        return [
            $sourceKey,
            $replace,
        ];
    }

    /**
     * 取得系统的替换变量.
     *
     * @return array
     */
    protected function getDefaultReplaceKeyValue(): array
    {
        return array_merge([
            'namespace' => $this->getNamespace(),
            'date_y'    => date('Y'),
        ], $this->getCustomReplaceKeyValue());
    }

    /**
     * 设置文件保存路径.
     *
     * @param string $saveFilePath
     */
    protected function setSaveFilePath(string $saveFilePath): void
    {
        $this->saveFilePath = $saveFilePath;
    }

    /**
     * 读取文件保存路径.
     *
     * @return string
     */
    protected function getSaveFilePath(): string
    {
        return $this->saveFilePath;
    }

    /**
     * 获取命名空间路径.
     *
     * @return string
     */
    protected function getNamespacePath(): string
    {
        if ('/' === ($namespacePath = $this->getContainer()->getPathByComposer($this->getNamespace()).'/')) {
            $namespacePath = $this->getContainer()->appPath(lcfirst($this->getNamespace())).'/';
        }

        return $namespacePath;
    }

    /**
     * 分析命名空间.
     */
    protected function parseNamespace(): void
    {
        $namespace = $this->option('namespace');

        if (empty($namespace)) {
            $namespace = 'app';
        }

        $namespace = ucfirst($namespace);

        $this->setNamespace($namespace);
    }

    /**
     * 设置命名空间.
     *
     * @param string $namespace
     */
    protected function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * 读取命名空间.
     *
     * @return string
     */
    protected function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * 设置创建类型.
     *
     * @param string $makeType
     */
    protected function setMakeType(string $makeType): void
    {
        $this->makeType = $makeType;
    }

    /**
     * 读取创建类型.
     *
     * @return string
     */
    protected function getMakeType(): string
    {
        return $this->makeType;
    }

    /**
     * 设置模板文件路径.
     *
     * @param string $templatePath
     */
    protected function setTemplatePath(string $templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    /**
     * 读取模板文件路径.
     *
     * @return string
     */
    protected function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * 设置自定义变量替换.
     *
     * @param mixed $key
     * @param mixed $value
     */
    protected function setCustomReplaceKeyValue($key, $value = null)
    {
        if (is_array($key)) {
            $this->customReplaceKeyValue = array_merge($this->customReplaceKeyValue, $key);
        } else {
            $this->customReplaceKeyValue[$key] = $value;
        }
    }

    /**
     * 读取自定义变量替换.
     *
     * @return array
     */
    protected function getCustomReplaceKeyValue(): array
    {
        return $this->customReplaceKeyValue;
    }

    /**
     * 格式化文件路径.
     *
     * @param string $file
     *
     * @return string
     */
    protected function formatFile(string $file): string
    {
        return $file;
    }
}
