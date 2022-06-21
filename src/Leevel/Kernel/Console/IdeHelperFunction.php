<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use DirectoryIterator;
use InvalidArgumentException;
use Leevel\Console\Command;
use Leevel\Filesystem\Helper\CreateFile;
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\IdeHelper as UtilsIdeHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * IDE 助手函数帮助文件自动生成.
 */
class IdeHelperFunction extends Command
{
    /**
     * 命令名字.
     */
    protected string $name = 'make:idehelper:function';

    /**
     * 命令行描述.
     */
    protected string $description = 'IDE helper generation for helper functions';

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $functionList = $this->parseFunctionList($dir = $this->getArgument('dir'));
        $content = (new UtilsIdeHelper())->handleFunction($functionList);

        echo PHP_EOL;
        echo $content;
        echo PHP_EOL.PHP_EOL;

        $message = sprintf('Ide helper for functions of dir <comment>%s</comment> generate succeed.', $dir);
        $this->info($message);

        if ($cachePath = $this->getOption('cachepath')) {
            $this->writeCache($cachePath, $content);
            $this->info(sprintf('Ide helper cache successed at %s.', $cachePath));
        }

        return 0;
    }

    /**
     * 分析函数列表.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseFunctionList(string $dir): array
    {
        if (!is_dir($dir)) {
            $e = sprintf('Dir `%s` is not exits.', $dir);

            throw new InvalidArgumentException($e);
        }

        $result = [];
        $classParser = new ClassParser();
        $functionList = new DirectoryIterator("glob://{$dir}/*.php");
        foreach ($functionList as $f) {
            $fn = $classParser->handle($f->getPathname());
            if (!function_exists($fn)) {
                class_exists($fn);
            }
            $result[] = $fn;
        }

        return $result;
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
                'dir',
                InputArgument::REQUIRED,
                'This is the source file dir.',
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
