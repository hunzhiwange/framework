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

use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Command;
use Leevel\Console\Option;
use Leevel\Filesystem\Helper\create_file;
use function Leevel\Filesystem\Helper\create_file;
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\IdeHelper as UtilsIdeHelper;

/**
 * IDE 帮助文件自动生成.
 *
 * @codeCoverageIgnore
 */
class IdeHelper extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'make:idehelper';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'IDE helper generation';

    /**
     * 响应命令.
     */
    public function handle(): int
    {
        $className = $this->parseClassName($this->path());
        $content = (new UtilsIdeHelper())->handle($className, $this->option('proxy'));

        echo PHP_EOL;
        echo $content;
        echo PHP_EOL.PHP_EOL;

        $message = sprintf('Ide helper for Class <comment>%s</comment> generate succeed.', $className);
        $this->info($message);

        if ($cachePath = $this->option('cachepath')) {
            $this->writeCache($cachePath, $content);
            $this->info(sprintf('Cache file of ide helper %s cache successed.', $cachePath));
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

            throw new InvalidArgumentException($e);
        }

        $className = (new ClassParser())->handle($pathOrClassName);

        return $className;
    }

    /**
     * 写入缓存.
     */
    protected function writeCache(string $cachePath, string $content): void
    {
        create_file($cachePath, $content);
    }

    /**
     * 取得路径.
     */
    protected function path(): string
    {
        return $this->argument('path');
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
                'proxy',
                'p',
                Option::VALUE_NONE,
                'Build proxy method.',
            ],
            [
                'cachepath',
                'c',
                Option::VALUE_OPTIONAL,
                'Cache path of content.',
            ],
        ];
    }
}

// import fn.
class_exists(create_file::class); // @codeCoverageIgnore
