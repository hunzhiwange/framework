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

namespace Leevel\Console;

use function Leevel\Support\Str\ends_with;
use Leevel\Support\Str\ends_with;
use RuntimeException;

/**
 * 命令行工具类导入类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.11.06
 *
 * @version 1.0
 */
class Load
{
    /**
     * 载入命名空间.
     *
     * @var array
     */
    protected $namespaces = [];

    /**
     * 已经载入数据.
     *
     * @var array
     */
    protected $loaded;

    /**
     * 构造函数.
     *
     * @param array $namespaces
     */
    public function __construct(array $namespaces = [])
    {
        $this->namespaces = $namespaces;
    }

    /**
     * 添加命名空间.
     *
     * @param array $namespaces
     *
     * @return $this
     */
    public function addNamespace(array $namespaces): self
    {
        $this->namespaces = array_merge($this->namespaces, $namespaces);

        return $this;
    }

    /**
     * 载入命令行数据.
     *
     * @return array
     */
    public function loadData(): array
    {
        if (null !== $this->loaded) {
            return $this->loaded;
        }

        $files = $this->findConsoleFile($this->namespaces);

        return $this->loaded = $files;
    }

    /**
     * 分析目录中的 PHP 命令包包含的文件.
     *
     * @param array $namespaces
     *
     * @return array
     */
    public function findConsoleFile(array $namespaces): array
    {
        $files = [];

        foreach ($namespaces as $key => $dir) {
            if (!is_dir($dir)) {
                throw new RuntimeException(sprintf('Console load dir %s is not exits.', $dir));
            }

            $currentFiles = glob($dir.'/*.php');
            $currentFiles = array_map(function ($item) use ($key) {
                return $key.'\\'.basename($item, '.php');
            }, $currentFiles);

            // 忽略索引
            $currentFiles = array_filter($currentFiles, function ($item) {
                return !ends_with($item, '\\index');
            });

            $files = array_merge($files, $currentFiles);
        }

        return $files;
    }
}

fns(ends_with::class);
