<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\IdeHelper as UtilsIdeHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * IDE 帮助文件自动生成.
 */
class IdeHelper extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'make:idehelper';

    /**
     * 命令行描述.
     */
    protected string $description = 'IDE helper generation';

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $className = $this->parseClassName($this->getArgument('path'));
        $content = (new UtilsIdeHelper())->handle($className);

        echo PHP_EOL;
        echo $content;
        echo PHP_EOL.PHP_EOL;

        $message = sprintf('Ide helper for class <comment>%s</comment> generate succeed.', $className);
        $this->info($message);

        if ($cachePath = $this->getOption('cachepath')) {
            $this->writeCache($cachePath, $content);
            $this->info(sprintf('Ide helper cache successed at %s.', $cachePath));
        }

        return 0;
    }

    /**
     * 分析类名.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseClassName(string $pathOrClassName): string
    {
        if (class_exists($pathOrClassName) || interface_exists($pathOrClassName)) {
            return $pathOrClassName;
        }

        if (!is_file($pathOrClassName)) {
            $e = sprintf('File `%s` is not exits.', $pathOrClassName);

            throw new \InvalidArgumentException($e);
        }

        return (new ClassParser())->handle($pathOrClassName);
    }

    /**
     * 写入缓存.
     */
    protected function writeCache(string $cachePath, string $content): void
    {
        CreateFile::handle($cachePath, $content);
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
                'This is the source file path or class name.',
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
                'cachepath',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Cache path of content.',
            ],
        ];
    }
}
