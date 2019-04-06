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

namespace Leevel\Option\Console;

use InvalidArgumentException;
use Leevel\Console\Command;
use Leevel\Kernel\IApp;
use Leevel\Option\Load;

/**
 * 配置缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.06
 *
 * @version 1.0
 */
class Cache extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'option:cache';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Merge all option file to a file.';

    /**
     * 基础路径.
     *
     * @var string
     */
    protected $basePath;

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function handle(IApp $app): void
    {
        $this->line('Start to cache option.');

        $load = new Load($app->optionPath());
        $data = $load->loadData($app);

        $cachePath = $app->optionCachedPath();
        $this->basePath = $app->path();

        $this->writeCache($cachePath, $data);

        $this->info(sprintf('Option cache file %s cache successed.', $cachePath));
    }

    /**
     * 计算相对路径
     * 忽略未包含在基础路径中的缓存相对路径.
     *
     * @param string $cachePath
     *
     * @return int
     */
    protected function computeRelativePath(string $cachePath): int
    {
        if (false === strpos($cachePath, $this->basePath)) {
            return -1;
        }

        $relativePath = str_replace($this->basePath.'/', '', $cachePath);
        $relativePath = dirname($relativePath);

        return count(explode('/', $relativePath));
    }

    /**
     * 替换相对路径.
     *
     * @param string $data
     *
     * @return string
     */
    protected function replaceRelativePath(string $data): string
    {
        return str_replace("'".$this->basePath, '$baseDir.\'', $data);
    }

    /**
     * 写入缓存.
     *
     * @param string $cachePath
     * @param array  $data
     */
    protected function writeCache(string $cachePath, array $data): void
    {
        $dirname = dirname($cachePath);

        if (!is_dir($dirname)) {
            if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
                $e = sprintf('Unable to create the %s directory.', $dirname);

                throw new InvalidArgumentException($e);
            }

            mkdir($dirname, 0777, true);
        }

        $relativePathLevel = $this->computeRelativePath($cachePath);
        $isRelativePath = $relativePathLevel > -1;

        $content = '<?'.'php /* '.date('Y-m-d H:i:s').
            ' */ ?'.'>';

        if ($isRelativePath) {
            $content .= PHP_EOL.'<?'.'php $baseDir = dirname(__DIR__, '.$relativePathLevel.'); ?>';
        }

        $content .= PHP_EOL.'<?'.'php return '.
            var_export($data, true).'; ?'.'>';

        if ($isRelativePath) {
            $content = $this->replaceRelativePath($content);
        }

        if (!is_writable($dirname) ||
            !file_put_contents($cachePath, $content)) {
            $e = sprintf('Dir %s is not writeable.', $dirname);

            throw new InvalidArgumentException($e);
        }

        chmod($cachePath, 0666 & ~umask());
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
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
