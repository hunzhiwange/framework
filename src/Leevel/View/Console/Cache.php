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

namespace Leevel\View\Console;

use Leevel\Console\Command;
use Leevel\Kernel\IApp;
use Leevel\View\Compiler;
use Leevel\View\Html;
use Leevel\View\Parser;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Finder\Finder;

/**
 * 视图缓存.
 */
class Cache extends Command
{
    /**
     * 命令名字.
    */
    protected string $name = 'view:cache';

    /**
     * 命令行描述.
    */
    protected string $description = 'Compile all view files';

    /**
     * 应用.
     */
    protected IApp $app;

    /**
     * 视图分析器.
     */
    protected Parser $parser;

    /**
     * 视图 HTML 仓储.
     */
    protected Html $html;

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->app = $app;
        $this->parser = $this->createParser();
        $this->html = $this->getHtmlView();

        $this->line('Start to cache view.');

        foreach ($this->paths() as $path) {
            $this->compiles($this->findFiles([$path]), $path);
        }

        $this->info('View files cache successed.');

        return 0;
    }

    /**
     * 编译视图文件.
     */
    protected function compiles(Finder $finder, string $path): void
    {
        if (0 === count($finder)) {
            $this->comment(sprintf('Compile files not found in path `%s` and skipped.', $path));

            return;
        }

        $this->info(sprintf('Start to compiles path `%s`', $path));

        $progressBar = new ProgressBar(new ConsoleOutput(), count($finder));
        $progressBar->setFormat('[View:cache]%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        foreach ($finder as $file) {
            $progressBar->advance();
            $this->parser->doCompile(
                $file->getRealPath(),
                $this->html->parseCachePath($file->getRealPath())
            );
        }

        $progressBar->finish();
        $this->line('');
    }

    /**
     * 查找视图目录中的视图文件.
     */
    protected function findFiles(array $paths): Finder
    {
        return (new Finder())
            ->in($paths)
            ->exclude(['vendor', 'node_modules'])
            ->name('*.html')
            ->files();
    }

    /**
     * 获取视图扫描目录.
     */
    protected function paths(): array
    {
        return array_merge([$this->app->themesPath()], $this->composerPaths());
    }

    /**
     * 取得应用的 composer 配置.
     *
     * @throws \RuntimeException
     */
    protected function composerPaths(): array
    {
        $path = $this->app->path().'/composer.json';
        if (!is_file($path)) {
            return [];
        }

        $option = $this->getFileContent($path);
        $paths = $option['extra']['leevel-console']['view-cache']['paths'] ?? [];
        $path = $this->app->path();
        $paths = array_map(function (string $value) use ($path) {
            if (!is_file($value)) {
                $value = $path.'/'.$value;
            }

            if (!is_dir($value)) {
                $e = sprintf('View dir %s is not exist.', $value);

                throw new RuntimeException($e);
            }

            return $value;
        }, $paths);

        return $paths;
    }

    /**
     * 获取配置信息.
     */
    protected function getFileContent(string $path): array
    {
        return json_decode(file_get_contents($path) ?: '', true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * 创建模板分析器.
     */
    protected function createParser(): Parser
    {
        return (new Parser(new Compiler()))
            ->registerCompilers()
            ->registerParsers();
    }

    /**
     * 获取 HTML 视图仓储.
     */
    protected function getHtmlView(): Html
    {
        return $this->app
            ->container()
            ->make('view.views')
            ->connect('html');
    }
}
