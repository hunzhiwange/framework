<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\TraverseDirectory;
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\Doc as UtilsDoc;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * 解析单元测试用例为 Markdown 文档.
 */
class Doc extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'make:doc';

    /**
     * 命令行描述.
     */
    protected string $description = 'Markdown generation based on test cases';

    /**
     * 文档解析器.
     */
    protected UtilsDoc $utilsDoc; /** @phpstan-ignore-line */

    /**
     * 类名字分析器.
     */
    protected ClassParser $classParser; /** @phpstan-ignore-line */

    /**
     * 响应命令.
     *
     * @throws \InvalidArgumentException
     */
    public function handle(): int
    {
        $this->includeBootstrapFile();

        if (!($files = $this->parseFiles())) {
            throw new \InvalidArgumentException('Files was not found.');
        }

        $this->utilsDoc = new UtilsDoc(
            (string) $this->getArgument('outputdir'),
            (string) $this->getOption('i18n'),
            'zh-CN',
            (string) $this->getOption('git')
        );
        if ($this->getOption('logdir')) {
            $this->utilsDoc->setLogPath($this->getOption('logdir'));
        }
        $this->classParser = new ClassParser();

        $succeedCount = 0;
        foreach ($files as $file) {
            if (true === $this->convertMarkdown($file)) {
                ++$succeedCount;
            }
        }

        $message = sprintf('A total of <comment>%d</comment> files generate succeed.', $succeedCount);
        $this->info($message);

        return self::SUCCESS;
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
            /** @phpstan-ignore-next-line */
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
        $fileOrDir = (string) $this->getArgument('path');
        if (is_file($fileOrDir)) {
            $result[] = $fileOrDir;
        } elseif (is_dir($fileOrDir)) {
            TraverseDirectory::handle($fileOrDir, true, function (\DirectoryIterator $file) use (&$result): void {
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
        if (is_file($bootstrap = (string) $this->getOption('bootstrap'))) {
            include $bootstrap;
        }
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [
            [
                'path',
                InputArgument::REQUIRED,
                'This is the tests file or dir path.',
            ],
            [
                'outputdir',
                InputArgument::REQUIRED,
                'This is the output dir path.',
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
                InputOption::VALUE_OPTIONAL,
                'This is the i18n.',
            ],
            [
                'logdir',
                null,
                InputOption::VALUE_OPTIONAL,
                'This is the log dir path.',
            ],
            [
                'bootstrap',
                null,
                InputOption::VALUE_OPTIONAL,
                'This is the tests bootstrap file.',
            ],
            [
                'git',
                null,
                InputOption::VALUE_OPTIONAL,
                'This is the git repository.',
                '',
            ],
        ];
    }
}
