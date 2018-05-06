<?php declare(strict_types=1);
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
namespace Leevel\Swoole\Console;

use Exception;
use Leevel\{
    Console\Option,
    Console\Command,
    Console\Argument
};
use Leevel\Swoole\ToolKit\AutoReload as AutoReloads;

/**
 * swoole 服务自动重启
 * 使用 inotify 监听 PHP 源码目录
 * 程序文件更新时自动重启 swoole 服务端
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.31
 * @version 1.0
 */
class AutoReload extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'swoole:autoreload';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Reload swoole service process when source code file is update';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        $this->warn($this->getVersion());

        // 设置服务器程序的 PID
        $pid = intval($this->argument('pid'));
        $autoReload = new AutoReloads($pid);

        // 设置要监听的源码目录
        $watchDir = $this->getWatchDir();
        foreach ($watchDir as $item) {
            $autoReload->watch($item);
        }

        // 监听后缀为 .php 的文件
        $autoReload->addFileType('.php');

        // 输入调试信息
        $this->info(sprintf('The listen pid is %d,ctrl + c can exit.', $pid));

        $this->table([
            'Watch dirs'
        ], array_map(function ($value) {
            return [$value];
        }, $watchDir));

        // 执行监听
        $autoReload->run();
    }

    /**
     * 获取监听目录
     *
     * @return array
     */
    protected function getWatchDir()
    {
        if (! $this->argument('dirs')) {
            $dirs = $this->getDefaultWatchDir();
        } else {
            $dirs = $this->parseWatchDir();
        }

        return $dirs;
    }

    /**
     * 分析监听目录
     *
     * @return array
     */
    protected function parseWatchDir()
    {
        $appPath = app()->path();

        $dirs = array_map(function($value) use($appPath) {
            if (strpos($value, '/') !== 0) {
                $value = $appPath . '/' . $value;
            }

            return $value;
        }, $this->argument('dirs'));

        return $dirs;
    }

    /**
     * 默认监听目录
     *
     * @return array
     */
    protected function getDefaultWatchDir()
    {
        return app('option')->get('swoole\autoreload_watch_dir');
    }

    /**
     * 返回 QueryPHP Version
     *
     * @return string
     */
    protected function getVersion()
    {
        return 'The AutoReload of Swoole Version ' . app()->version() . PHP_EOL;
    }

    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            [
                'pid',
                Argument::REQUIRED,
                'The pid need to be watch,you can use the command swoole:lists to see it master pid.'
            ],
            [
                'dirs',
                Argument::IS_ARRAY,
                'The dirs to be watch.'
            ]
        ];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'after',
                null,
                Option::VALUE_REQUIRED,
                'The after seconds reload the server when file updated.',
                app('option')->get('swoole\autoreload_after_seconds')
            ]
        ];
    }
}
