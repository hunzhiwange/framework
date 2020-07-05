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

namespace Leevel\Kernel\Console;

use DirectoryIterator;
use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Command;
use function Leevel\Filesystem\Helper\list_directory;
use Leevel\Filesystem\Helper\list_directory;
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\Doc as UtilsDoc;

/**
 * 解析单元测试用例为 Markdown 文档.
 *
 * @codeCoverageIgnore
 */
class Doc extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'make:doc';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Markdown generation based on test cases';

    /**
     * 文档解析器.
     *
     * @var \Leevel\Kernel\Utils\Doc
     */
    protected UtilsDoc $utilsDoc;

    /**
     * 类名字分析器.
     *
     * @var \Leevel\Kernel\Utils\ClassParser
     */
    protected ClassParser $classParser;

    /**
     * 响应命令.
     *
     * @throws \InvalidArgumentException
     */
    public function handle(): int
    {
        $this->includeBootstrapFile();

        if (!($files = $this->parseFiles())) {
            throw new InvalidArgumentException('Files was not found.');
        }

        $this->utilsDoc = new UtilsDoc($this->outputDir(), $this->git(), $this->i18n());
        if ($this->getOption('logdir')) {
            $this->utilsDoc->setLogPath($this->getOption('logdir'));
        }
        $this->classParser = new ClassParser();

        $succeedCount = 0;
        foreach ($files as $file) {
            if (true === $this->convertMarkdown($file)) {
                $succeedCount++;
            }
        }

        $message = sprintf('A total of <comment>%d</comment> files generate succeed.', $succeedCount);
        $this->info($message);

        return 0;
    }

    /**
     * 生成 markdown 文件.
     */
    protected function convertMarkdown(string $file): bool
    {
        $className = $this->classParser->handle($file);
        if (!class_exists($className)) {
            return false;
        }

        $result = $this->utilsDoc->handleAndSave($className);
        if (false !== $result) {
            $message = sprintf('Class <info>%s</info> was generate succeed at <info>%s</info>.', $className, $result[0]);
            $this->line($message);
        }

        return false !== $result;
    }

    /**
     * 分析测试用例文件.
     */
    protected function parseFiles(): array
    {
        $result = [];
        $fileOrDir = $this->path();
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
        if (is_file($bootstrap = $this->bootstrap())) {
            include $bootstrap;
        }
    }

    /**
     * 取得测试用例文件或者目录相对路径.
     */
    protected function path(): string
    {
        return $this->getArgument('path');
    }

    /**
     * 取得测试用例初始化引导文件.
     */
    protected function bootstrap(): string
    {
        return $this->getArgument('bootstrap');
    }

    /**
     * 取得生成的 markdown 输出目录.
     */
    protected function outputDir(): string
    {
        return $this->getArgument('outputdir');
    }

    /**
     * i18n 参数.
     */
    protected function i18n(): string
    {
        return $this->getOption('i18n');
    }

    /**
     * 取得生成的 markdown Git 原始仓库地址.
     */
    protected function git(): string
    {
        return $this->getArgument('git');
    }

    /**
     * 命令参数.
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
                'bootstrap',
                Argument::REQUIRED,
                'This is the tests bootstrap file.',
            ],
            [
                'outputdir',
                Argument::REQUIRED,
                'This is the output dir path.',
            ],
            [
                'git',
                Argument::REQUIRED,
                'This is the git repository.',
            ],
        ];
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [
            [
                'i18n',
                null,
                Argument::OPTIONAL,
                'This is the i18n.',
            ],
            [
                'logdir',
                null,
                Argument::OPTIONAL,
                'This is the log dir path.',
            ],
        ];
    }
}

// import fn.
class_exists(list_directory::class); // @codeCoverageIgnore
