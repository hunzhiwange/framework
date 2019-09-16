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

use InvalidArgumentException;
use Leevel\Console\Argument;
use Leevel\Console\Command;
use Leevel\Kernel\Utils\ClassParser;
use Leevel\Kernel\Utils\IdeHelper as UtilsIdeHelper;

/**
 * IDE 帮助文件自动生成.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.31
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class IdeHelper extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'make:idehelper';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'IDE helper generation';

    /**
     * 响应命令.
     */
    public function handle(): void
    {
        $className = $this->parseClassName($this->path());
        $classContent = (new UtilsIdeHelper())->handle($className);

        echo PHP_EOL;
        echo $classContent;
        echo PHP_EOL.PHP_EOL;

        $message = sprintf('The @method for Class <comment>%s</comment> generate succeed.', $className);
        $this->info($message);
    }

    /**
     * 分析类名.
     *
     * @param string $pathOrClassName
     *
     * @throws \InvalidArgumentException
     *
     * @return string
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
     * 取得路径.
     *
     * @return string
     */
    protected function path(): string
    {
        return $this->argument('path');
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
                'This is the source file path or class name.',
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
