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

namespace Leevel\Console;

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
     * 响应命令.
     */
    public function handle()
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
     *
     * @return string
     */
    protected function replaceTemplateSource()
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
     * 保存模板
     */
    protected function saveTemplateResult()
    {
        $saveFilePath = $this->getSaveFilePath();

        if (!is_dir(dirname($saveFilePath))) {
            mkdir(dirname($saveFilePath), 0777, true);
        }

        if (is_file($saveFilePath)) {
            throw new RuntimeException(
                'File is already exits.'.PHP_EOL.
                $this->formatFile($this->getSaveFilePath())
            );
        }

        if (!file_put_contents($saveFilePath, $this->getTemplateResult())) {
            throw new RuntimeException(
                'Can not write file.'.PHP_EOL.
                $this->formatFile($this->getSaveFilePath())
            );
        }
    }

    /**
     * 获取模板编译结果.
     *
     * @return string
     */
    protected function getTemplateResult()
    {
        return $this->templateResult;
    }

    /**
     * 分析模板源码
     */
    protected function parseTemplateSource()
    {
        $templateSource = $this->getTemplatePath();

        if (!is_file($templateSource)) {
            throw new RuntimeException(
                'Template not found.'.PHP_EOL.
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
    protected function getTemplateSource()
    {
        return $this->templateSource;
    }

    /**
     * 分析变量替换.
     *
     * @return array
     */
    protected function parseSourceAndReplace()
    {
        $replaceKeyValue = array_merge(option('console\template'), $this->getDefaultReplaceKeyValue());

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
    protected function getDefaultReplaceKeyValue()
    {
        return array_merge([
            'namespace' => $this->getNamespace(),
            'file_name' => ucfirst($this->argument('name')),
            'date_y' => date('Y'),
        ], $this->getCustomReplaceKeyValue());
    }

    /**
     * 设置文件保存路径.
     *
     * @param string $saveFilePath
     */
    protected function setSaveFilePath($saveFilePath)
    {
        $this->saveFilePath = $saveFilePath;
    }

    /**
     * 读取文件保存路径.
     *
     * @return string
     */
    protected function getSaveFilePath()
    {
        return $this->saveFilePath;
    }

    /**
     * 获取命名空间路径.
     *
     * @return string
     */
    protected function getNamespacePath()
    {
        if ('/' != ($namespacePath = $this->getContainer()->getPathByNamespace($this->getNamespace()).'/')) {
            $namespacePath = $this->getContainer()->pathAnApplication(lcfirst($this->getNamespace())).'/';
        }

        return $namespacePath;
    }

    /**
     * 分析命名空间.
     */
    protected function parseNamespace()
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
    protected function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * 读取命名空间.
     *
     * @return string
     */
    protected function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * 设置创建类型.
     *
     * @param string $makeType
     */
    protected function setMakeType($makeType)
    {
        $this->makeType = $makeType;
    }

    /**
     * 读取创建类型.
     *
     * @return string
     */
    protected function getMakeType()
    {
        return $this->makeType;
    }

    /**
     * 设置模板文件路径.
     *
     * @param string $templatePath
     */
    protected function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;
    }

    /**
     * 读取模板文件路径.
     *
     * @return string
     */
    protected function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * 设置自定义变量替换.
     *
     * @param mixed  $key
     * @param string $value
     */
    protected function setCustomReplaceKeyValue($key, $value)
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
     * @param string $makeType
     *
     * @return array
     */
    protected function getCustomReplaceKeyValue()
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
    protected function formatFile($file)
    {
        return $file;
    }
}
