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

use InvalidArgumentException;
use Leevel\Console\{
    Option,
    Command,
    Argument
};
use Leevel\Option as Options;

/**
 * 配置缓存
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.05.06
 * @version 1.0
 */
class Cache extends Command
{

    /**
     * 命令名字
     *
     * @var string
     */
    protected $name = 'option:cache';

    /**
     * 命令行描述
     *
     * @var string
     */
    protected $description = 'Merge all option file to a file';

    /**
     * 响应命令
     *
     * @return void
     */
    public function handle()
    {
        $this->line('Start to cache option.');

        $data = Options::all();

        $cachePath = app()->pathCacheOptionFile();

        if ($this->checkCacheExists($cachePath) === true) {
            return false;
        }

        $this->writeCache($cachePath, $data);

        $this->info(sprintf('Option file %s cache successed.', $cachePath));
    }

    /**
     * 验证缓存
     *
     * @param string $cachePath
     * @return bool
     */
    protected function checkCacheExists(string $cachePath)
    {
        if (is_file($cachePath)) {
            $this->warn(sprintf('Option cache file %s is already exits.', $cachePath));
            $result = $this->confirm('You must need to clear the cache file.', true);

            if ($result) {
                unlink($cachePath);
                $this->warn('Please execute the command once again.');
            }

            return true;
        }

        return false;
    }

    /**
     * 写入缓存
     *
     * @param string $cachePath
     * @param array $data
     * @return void
     */
    protected function writeCache(string $cachePath, array $data)
    {
        if (! is_dir(dirname($cachePath))) {
            mkdir(dirname($cachePath), 0777, true);
        }

        $content = '<?' . 'php /* ' . date('Y-m-d H:i:s') .
            ' */ ?' . '>' . 
            PHP_EOL . '<?' . 'php return ' .
            var_export($data, true) . '; ?' . '>';
            
        if(! file_put_contents($cachePath, $content)) {
            throw new InvalidArgumentException(
                sprintf('Dir %s is not writeable', dirname($cachePath))
            );
        }

        chmod($cachePath, 0777);
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
