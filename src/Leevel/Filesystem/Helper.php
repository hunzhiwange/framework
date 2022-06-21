<?php

declare(strict_types=1);

namespace Leevel\Filesystem;

use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;

/**
 * 助手类.
 *
 * @method static void createDirectory(string $dir, int $mode = 0777)                                  创建目录.
 * @method static void createFile(string $path, ?string $content = null, int $mode = 0666)             创建文件.
 * @method static void deleteDirectory(string $dir)                                                    删除目录.
 * @method static array distributed(int $dataId)                                                       根据 ID 获取打散目录.
 * @method static string getExtension(string $fileName, int $case = 0)                                 获取上传文件扩展名.
 * @method static bool isAbsolutePath(string $path)                                                    判断是否为绝对路径.
 * @method static void link(string $originDir, string $targetDir)                                      创建软连接.
 * @method static void listDirectory(string $path, bool $recursive, \Closure $cal, array $filter = []) 浏览目录.
 * @method static string tidyPath(string $path, bool $unix = true)                                     整理目录斜线风格.
 */
class Helper
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $helperClass = __NAMESPACE__.'\\Helper\\'.ucfirst($method);
        return $helperClass::handle(...$args);
    }
}

// import fn.
class_exists(un_camelize::class);
