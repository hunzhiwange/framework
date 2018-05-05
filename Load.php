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
namespace Leevel\Console;

use RuntimeException;

/**
 * 命令行工具类导入类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.06
 * @version 1.0
 */
class Load
{
    /**
     * 载入命名空间
     *
     * @var array
     */
    protected $namespaces = [];

    /**
     * 已经载入数据
     *
     * @var array
     */
    protected $loaded;

    /**
     * 构造函数
     *
     * @param array $namespaces
     * @return void
     */
    public function __construct(array $namespaces = [])
    {
        $this->namespaces = $namespaces;
    }

    /**
     * 添加命名空间
     *
     * @param array $namespaces
     * @return $this
     */
    public function addNamespace(array $namespaces)
    {
        $this->namespaces = array_merge($this->namespaces, $namespaces);
        return $this;
    }

    /**
     * 载入命令行数据
     *
     * @return array
     */
    public function loadData()
    {
        if (! is_null($this->loaded)) {
            return $this->loaded;
        }

        $files = $this->findConsoleFile($this->namespaces);

        return $this->loaded = $files;
    }

    /**
     * 分析目录中的 PHP 命令包包含的文件
     *
     * @param array $namespaces
     * @return array
     */
    public function findConsoleFile(array $namespaces)
    {
        $files = [ ];
        
        foreach ($namespaces as $key => $dir) {
            if (! is_dir($dir)) {
                throw new RuntimeException('Console load dir is not exits.');
            }

            $files = array_merge($files, array_map(function ($item) use ($key) {
                return $key . '\\' . basename($item, '.php');
            }, glob($dir . '/*.php')));
        }

        return $files;
    }
}
