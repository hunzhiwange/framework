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

namespace Leevel\Filesystem;

use Closure;
use DirectoryIterator;
use InvalidArgumentException;
use Leevel\Support\TMacro;
use RuntimeException;

/**
 * File System Object 管理.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 */
class Fso
{
    use TMacro;

    /**
     * 取得文件内容.
     *
     * @param string $path
     *
     * @return string
     */
    public static function fileContents(string $path)
    {
        if (is_file($path)) {
            return file_get_contents($path);
        }

        throw new RuntimeException(
            sprintf('File %s does not exist', $path)
        );
    }

    /**
     * 创建目录.
     *
     * @param string $dir
     * @param number $mode
     *
     * @return true
     */
    public static function createDirectory(string $dir, $mode = 0777)
    {
        if (is_dir($dir)) {
            return true;
        }

        mkdir($dir, $mode, true);

        return true;
    }

    /**
     * 删除目录.
     *
     * @param string $dir
     * @param bool   $recursive
     */
    public static function deleteDirectory(string $dir, bool $recursive = false)
    {
        if (!file_exists($dir) || !is_dir($dir)) {
            return;
        }

        if (!$recursive) {
            rmdir($dir);
        } else {
            $instance = new DirectoryIterator($dir);

            foreach ($instance as $file) {
                if ($file->isDot()) {
                    continue;
                }

                if ($file->isFile()) {
                    if (!unlink($file->getRealPath())) {
                        return;
                    }
                } elseif ($file->isDir()) {
                    static::deleteDirectory($file->getRealPath(), $recursive);
                }
            }

            rmdir($dir);
        }
    }

    /**
     * 复制目录.
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @param array  $filter
     */
    public static function copyDirectory(string $sourcePath, string $targetPath, array $filter = [])
    {
        if (!is_dir($sourcePath)) {
            return;
        }

        $instance = new DirectoryIterator($sourcePath);

        foreach ($instance as $file) {
            if ($file->isDot() ||
                in_array($file->getFilename(), $filter, true)) {
                continue;
            }

            $newPath = $targetPath.'/'.$file->getFilename();

            if ($file->isFile()) {
                if (!is_dir($newPath)) {
                    static::createDirectory(dirname($newPath));
                }

                if (!copy($file->getRealPath(), $newPath)) {
                    return;
                }
            } elseif ($file->isDir()) {
                if (!is_dir($newPath)) {
                    static::createDirectory($newPath);
                }

                if (!static::copyDirectory($file->getRealPath(), $newPath, $filter)) {
                    return;
                }
            }
        }
    }

    /**
     * 浏览目录.
     *
     * @param string   $path
     * @param bool     $recursive
     * @param \Closure $cal
     * @param array    $filter
     */
    public static function listDirectory(string $path, bool $recursive, Closure $cal, array $filter = [])
    {
        if (!is_dir($path)) {
            return;
        }

        $instance = new DirectoryIterator($path);

        foreach ($instance as $file) {
            if ($file->isDot() ||
                in_array($file->getFilename(), $filter, true)) {
                continue;
            }

            call_user_func($cal, $file);

            if (true === $recursive && $file->isDir()) {
                static::listDirectory($file->getPath().'/'.$file->getFilename(), true, $cal, $filter);
            }
        }
    }

    /**
     * 整理目录斜线风格
     *
     * @param string $path
     * @param bool   $unix
     *
     * @return string
     */
    public static function tidyPath(string $path, bool $unix = true)
    {
        $path = str_replace('\\', '/', $path);
        $path = preg_replace('|/+|', '/', $path);
        $path = str_replace(':/', ':\\', $path);

        if (!$unix) {
            $path = str_replace('/', '\\', $path);
        }

        return rtrim($path, '\\/');
    }

    /**
     * 判断是否为绝对路径.
     *
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolute(string $path): bool
    {
        return preg_match('/^(\/|[a-z]:)/i', $path) ? true : false;
    }

    /**
     * 根据 ID 获取打散目录.
     *
     * @param int $dataId
     *
     * @return array
     */
    public static function distributed(int $dataId): array
    {
        $dataId = abs((int) $dataId);
        $dataId = sprintf('%09d', $dataId); // 格式化为 9 位数，前面不够填充 0

        return [
            substr($dataId, 0, 3).'/'.
                substr($dataId, 3, 2).'/'.
                substr($dataId, 5, 2).'/',
            substr($dataId, -2),
        ];
    }

    /**
     * 新建文件.
     *
     * @param string $path
     * @param int    $mode
     *
     * @return bool
     */
    public static function createFile(string $path, int $mode = 0666)
    {
        $dirname = dirname($path);

        if (is_file($dirname)) {
            throw new InvalidArgumentException(
                'Dir can not be a file.'
            );
        }

        if (!is_dir($dirname)) {
            if (is_dir(dirname($dirname)) && !is_writable(dirname($dirname))) {
                throw new InvalidArgumentException(
                    sprintf('Unable to create the %s directory.', $dirname)
                );
            }

            mkdir($dirname, 0777, true);
        }

        if (!is_writable($dirname) || !($file = fopen($path, 'a'))) {
            throw new InvalidArgumentException(
                sprintf('The directory "%s" is not writable', $dirname)
            );
        }

        $mode = $mode & ~umask();

        chmod($path, $mode);

        return fclose($file);
    }

    /**
     * 获取上传文件扩展名.
     *
     * @param string $fileName 文件名
     * @param int    $case     格式化参数 0 默认，1 转为大小 ，转为大小
     *
     * @return string
     */
    public static function getExtension(string $fileName, int $case = 0)
    {
        $fileName = pathinfo($fileName, PATHINFO_EXTENSION);

        if (1 === $case) {
            return strtoupper($fileName);
        }

        if (2 === $case) {
            return strtolower($fileName);
        }

        return $fileName;
    }

    /**
     * 获取文件名字.
     *
     * @param string $path
     *
     * @return string
     */
    public static function getName(string $path)
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * 创建软连接.
     *
     * @param string $target
     * @param string $link
     * @codeCoverageIgnore
     */
    public static function link(string $target, string $link)
    {
        if (DIRECTORY_SEPARATOR !== '\\') {
            return symlink($target, $link);
        }

        $mode = is_dir($target) ? 'J' : 'H';

        exec("mklink /{$mode} \"{$link}\" \"{$target}\"");
    }
}
