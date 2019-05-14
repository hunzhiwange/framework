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

namespace Leevel\Kernel\Console;

use DirectoryIterator;
use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Command;
use function Leevel\Filesystem\Fso\list_directory;
use Leevel\Filesystem\Fso\list_directory;
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\Doc as UtilsDoc;

/**
 * 解析单元测试用例为 Markdown 文档.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.02.28
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Doc extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'make:doc';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Markdown generation based on test cases.';

    /**
     * 文档解析器.
     *
     * @var \Leevel\Kernel\Utils\Doc
     */
    protected $utilsDoc;

    /**
     * 类名字分析器.
     *
     * @var \Leevel\Kernel\Utils\ClassParser
     */
    protected $classParser;

    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $this->includeBootstrapFile();

        if (!($files = $this->parseFiles())) {
            throw new InvalidArgumentException('Files was not found.');
        }

        $this->utilsDoc = new UtilsDoc($this->outputDir());
        $this->classParser = new ClassParser();

        $succeedCount = 0;

        foreach ($files as $file) {
            if (true === $this->convertMarkdown($file)) {
                $succeedCount++;
            }
        }

        $this->info(sprintf('A total of <comment>%d</comment> files generate succeed.', $succeedCount));
    }

    /**
     * 生成 markdown 文件.
     *
     * @param string $file
     *
     * @return bool
     */
    protected function convertMarkdown(string $file): bool
    {
        $className = $this->classParser->handle($file);

        if (!class_exists($className)) {
            return false;
        }

        $result = $this->utilsDoc->handleAndSave($className);

        if (true === $result) {
            $this->line(sprintf('Class <info>%s</info> was generate succeed.', $className));
        }

        return $result;
    }

    /**
     * 分析测试用例文件.
     *
     * @return array
     */
    protected function parseFiles(): array
    {
        $fileOrDir = dirname($this->testsDir()).'/'.$this->path();

        $result = [];

        if (is_file($fileOrDir)) {
            $result[] = $fileOrDir;
        } elseif (is_dir($fileOrDir)) {
            list_directory($fileOrDir, true, function (DirectoryIterator $file) use (&$result) {
                if ($file->isFile()) {
                    $result[] = $file->getPathname();
                }
            });
        }

        return $result;
    }

    /**
     * 载入测试用例启动文件.
     */
    protected function includeBootstrapFile(): void
    {
        $bootstrap = $this->testsDir().'/bootstrap.php';

        if (is_file($bootstrap)) {
            include $bootstrap;
        }
    }

    /**
     * 取得测试用例文件或者目录相对路径.
     *
     * @return string
     */
    protected function path(): string
    {
        return $this->argument('path');
    }

    /**
     * 取得测试用例基础目录.
     *
     * @return string
     */
    protected function testsDir(): string
    {
        return $this->argument('testsdir');
    }

    /**
     * 取得生成的 markdown 输出目录.
     *
     * @return string
     */
    protected function outputDir(): string
    {
        return $this->argument('outputdir');
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [
            [
                'path',
                Argument::REQUIRED,
                'This is the tests file or dir path.',
            ],
            [
                'testsdir',
                Argument::REQUIRED,
                'This is the tests dir path.',
            ],
            [
                'outputdir',
                Argument::REQUIRED,
                'This is the output dir path.',
            ],
        ];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [];
    }
}

// import fn.
class_exists(list_directory::class);
