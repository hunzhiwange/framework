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

namespace Leevel\Filesystem;

use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 助手类.
 *
 * @method static bool createDirectory(string $dir, int $mode = 0777)                                  创建目录.
 * @method static void createFile(string $path, ?string $content = null, int $mode = 0666)             创建文件.
 * @method static void deleteDirectory(string $dir)                                                    删除目录.
 * @method static array distributed(int $dataId)                                                       根据 ID 获取打散目录.
 * @method static string getExtension(string $fileName, int $case = 0)                                 获取上传文件扩展名.
 * @method static bool isAbsolutePath(string $path)                                                    判断是否为绝对路径.
 * @method static void link(string $target, string $link)                                              创建软连接.
 * @method static void listDirectory(string $path, bool $recursive, \Closure $cal, array $filter = []) 浏览目录.
 * @method static string tidyPath(string $path, bool $unix = true)                                     整理目录斜线风格.
 */
class Helper
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        $fn = __NAMESPACE__.'\\Helper\\'.un_camelize($method);
        if (!function_exists($fn)) {
            class_exists($fn);
        }

        return $fn(...$args);
    }
}

// import fn.
class_exists(un_camelize::class);
