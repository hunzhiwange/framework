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

namespace Leevel\Option\Console;

use Leevel\Console\Command;
use Leevel\Filesystem\Helper\create_file;
use function Leevel\Filesystem\Helper\create_file;
use Leevel\Kernel\IApp;
use Leevel\Option\Load;

/**
 * 配置缓存.
 */
class Cache extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected string $name = 'option:cache';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Merge all option file to a file';

    /**
     * 基础路径.
     *
     * @var string
     */
    protected string $basePath;

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->line('Start to cache option.');

        $load = new Load($app->optionPath());
        $data = $load->loadData($app);
        $cachePath = $app->optionCachedPath();
        $this->basePath = $app->path();
        $this->writeCache($cachePath, $data);

        $this->info(sprintf('Option cache file %s cache successed.', $cachePath));

        return 0;
    }

    /**
     * 计算相对路径
     * 忽略未包含在基础路径中的缓存相对路径.
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
     */
    protected function replaceRelativePath(string $data): string
    {
        return str_replace("'".$this->basePath, '$baseDir.\'', $data);
    }

    /**
     * 写入缓存.
     */
    protected function writeCache(string $cachePath, array $data): void
    {
        $relativePathLevel = $this->computeRelativePath($cachePath);
        $isRelativePath = $relativePathLevel > -1;
        $content = '<?'.'php /* '.date('Y-m-d H:i:s').' */ ?'.'>';

        if ($isRelativePath) {
            $content .= PHP_EOL.'<?'.'php $baseDir = dirname(__DIR__, '.$relativePathLevel.'); ?>';
        }

        $content .= PHP_EOL.'<?'.'php return '.
            var_export($data, true).'; ?'.'>';

        if ($isRelativePath) {
            $content = $this->replaceRelativePath($content);
        }

        create_file($cachePath, $content);
    }
}

// import fn.
class_exists(create_file::class);
