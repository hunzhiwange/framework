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
namespace Leevel\Option\Console;

use Leevel\Console\{
    Option,
    Command,
    Argument
};

/**
 * 配置缓存清理
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.05.06
 * @version 1.0
 */
class Clear extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $strName = 'option:clear';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $strDescription = 'Clear cache of option';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        $this->line('Start to clear cache option.');

        $cachePath = app()->pathCacheOptionFile();

        $this->clearCache($cachePath);

        $this->info(sprintf('Option file %s cache clear successed.', $cachePath));
    }

    /**
     * 删除缓存
     *
     * @param string $cachePath
     * @return void
     */
    protected function clearCache(string $cachePath)
    {
        if (! is_file($cachePath)) {
            $this->warn('Option cache files have been cleaned up.');
            return;
        }

        unlink($cachePath);
    }

    /**
     * 命令参数
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [];
    }
}
