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
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\IdeHelper as UtilsIdeHelper;

/**
 * IDE 助手函数帮助文件自动生成.
 *
 * @codeCoverageIgnore
 */
class IdeHelperFunction extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'make:idehelper:function';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'IDE helper generation for helper functions';

    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $functionList = $this->parseFunctionList($dir = $this->dir());
        $content = (new UtilsIdeHelper())->handleFunction($functionList);

        echo PHP_EOL;
        echo $content;
        echo PHP_EOL.PHP_EOL;

        $message = sprintf('The @method for functions of dir <comment>%s</comment> generate succeed.', $dir);
        $this->info($message);
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
     * 取得目录.
     */
    protected function dir(): string
    {
        return $this->argument('dir');
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [
            [
                'dir',
                Argument::REQUIRED,
                'This is the source file dir.',
            ],
        ];
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [];
    }
}
