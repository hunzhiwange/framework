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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\View\Console;

use Leevel\Console\Command;
use Leevel\Kernel\IProject;
use Leevel\Option\IOption;
use Leevel\View\Compiler;
use Leevel\View\IView;
use Leevel\View\Parser;
use RuntimeException;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Finder\Finder;

/**
 * 视图缓存.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.01
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Cache extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'view:cache';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Compile all view files';

    /**
     * IOC 容器.
     *
     * @var \Leevel\Kernel\IProject
     */
    protected $project;

    /**
     * 视图分析器.
     *
     * @var \Leevel\View\Parser
     */
    protected $parser;

    /**
     * 视图 HTML 仓储.
     *
     * @var \Leevel\View\IView
     */
    protected $html;

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IProject $project
     * @param \Leevel\Option\IOption  $option
     */
    public function handle(IProject $project, IOption $option)
    {
        $this->project = $project;
        $this->parser = $this->createParser();
        $this->html = $this->getHtmlView();

        $this->line('Start to cache view.');

        foreach ($this->paths() as $path) {
            $this->compiles($this->findFiles([$path]), $path);
        }

        $this->info('View files cache successed.');
    }

    /**
     * 编译视图文件.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     * @param string                           $path
     */
    protected function compiles(Finder $finder, string $path): void
    {
        if (0 === count($finder)) {
            $this->comment(
                sprintf('Compile files not found in path `%s` and skipped.', $path)
            );

            return;
        }

        $this->info(sprintf('Start to compiles path `%s`', $path));

        $progressBar = new ProgressBar(new ConsoleOutput(), count($finder));
        $progressBar->setFormat('[View:cache]%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');

        foreach ($finder as $file) {
            $progressBar->advance();

            $this->parser->doCompile(
                $file->getRealPath(),
                $this->html->getCachePath($file->getRealPath())
            );
        }

        $progressBar->finish();
        $this->line('');
    }

    /**
     * 查找视图目录中的视图文件.
     *
     * @param array $paths
     *
     * @return \Symfony\Component\Finder\Finder
     */
    protected function findFiles(array $paths): Finder
    {
        return (new Finder())->
            in($paths)->
            exclude(['vendor', 'node_modules'])->
            name('*.html')->
            files();
    }

    /**
     * 获取视图扫描目录.
     *
     * @return array
     */
    protected function paths(): array
    {
        return array_merge([$this->project->themesPath()], $this->composerPaths());
    }

    /**
     * 取得应用的 composer 配置.
     *
     * @return array
     */
    protected function composerPaths(): array
    {
        $path = $this->project->path().'/composer.json';

        if (!is_file($path)) {
            return [];
        }

        $options = $this->getFileContent($path);

        $paths = !empty($options['extra']['leevel-console']['view-cache']['paths']) ?
            $options['extra']['leevel-console']['view-cache']['paths'] : [];

        $path = $this->project->path();

        $paths = array_map(function (string $value) use ($path) {
            if (!is_file($value)) {
                $value = $path.'/'.$value;
            }

            if (!is_dir($value)) {
                throw new RuntimeException(
                    sprintf('View dir %s is not exist.', $value)
                );
            }

            return $value;
        }, $paths);

        return $paths;
    }

    /**
     * 获取配置信息.
     *
     * @param string $path
     *
     * @return array
     */
    protected function getFileContent(string $path): array
    {
        return json_decode(file_get_contents($path), true);
    }

    /**
     * 创建模板分析器.
     *
     * @return \Leevel\View\Parser
     */
    protected function createParser(): Parser
    {
        return (new Parser(new Compiler()))->
        registerCompilers()->

        registerParsers();
    }

    /**
     * 获取 HTML 视图仓储.
     *
     * @return \Leevel\View\IView
     */
    protected function getHtmlView(): IView
    {
        return $this->project->make('view.views')->connect('html');
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
