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
     * @param string $strPath
     *
     * @return string
     */
    public static function fileContents($strPath)
    {
        if (static::isFile($strPath)) {
            return file_get_contents($strPath);
        }

        throw new RuntimeException(sprintf('File %s does not exist', $strPath));
    }

    /**
     * 创建目录.
     *
     * @param string $sDir
     * @param number $nMode
     *
     * @return true|void
     */
    public static function createDirectory($sDir, $nMode = 0777)
    {
        if (is_dir($sDir)) {
            return;
        }

        mkdir($sDir, $nMode, true);

        return true;
    }

    /**
     * 删除目录.
     *
     * @param string $sDir
     * @param bool   $bRecursive
     */
    public static function deleteDirectory($sDir, $bRecursive = false)
    {
        if (!file_exists($sDir) || !is_dir($sDir)) {
            return;
        }

        if (!$bRecursive) {
            rmdir($sDir);
        } else {
            $objDir = new DirectoryIterator($sDir);
            foreach ($objDir as $objFile) {
                if ($objFile->isDot()) {
                    continue;
                }

                if ($objFile->isFile()) {
                    if (!unlink($objFile->getRealPath())) {
                        return;
                    }
                } elseif ($objFile->isDir()) {
                    static::deleteDirectory($objFile->getRealPath(), $bRecursive);
                }
            }
            rmdir($sDir);
        }
    }

    /**
     * 复制目录.
     *
     * @param string $sSourcePath
     * @param string $sTargetPath
     * @param array  $arrFilter
     */
    public static function copyDirectory($sSourcePath, $sTargetPath, $arrFilter = [])
    {
        if (!is_dir($sSourcePath)) {
            return;
        }

        if (file_exists($sTargetPath)) {
            return;
        }

        $objDir = new DirectoryIterator($sSourcePath);
        foreach ($objDir as $objFile) {
            if ($objFile->isDot() || in_array($objFile->getFilename(), $arrFilter, true)) {
                continue;
            }

            $sNewPath = $sTargetPath.'/'.$objFile->getFilename();

            if ($objFile->isFile()) {
                if (!is_dir($sNewPath)) {
                    static::createDirectory(dirname($sNewPath));
                }
                if (!copy($objFile->getRealPath(), $sNewPath)) {
                    return;
                }
            } elseif ($objFile->isDir()) {
                if (!static::copyDirectory($objFile->getRealPath(), $sNewPath)) {
                    return;
                }
            }
        }
    }

    /**
     * 浏览目录.
     *
     * @param string   $sPath
     * @param \Closure $cal
     * @param array    $arrFilter
     */
    public static function listDirectory($sPath, Closure $cal, $arrFilter = [])
    {
        if (!is_dir($sPath)) {
            return;
        }

        $objDir = new DirectoryIterator($sPath);
        foreach ($objDir as $objFile) {
            if ($objFile->isDot() || in_array($objFile->getFilename(), $arrFilter, true)) {
                continue;
            }

            call_user_func($cal, $objFile);
            if ($objFile->isDir()) {
                static::listDirectory($objFile->getPath().'/'.$objFile->getFilename(), $cal, $arrFilter);
            }
        }
    }

    /**
     * 只读取一级目录.
     *
     * @param string $sDir
     * @param string $strReturnType
     * @param bool   $booFullpath
     * @param array  $arrFilter
     * @param array  $arrAllowedExt
     * @param array  $arrFilterExt
     *
     * @return array
     */
    public static function lists($sDir, $strReturnType = 'dir', $booFullpath = false, $arrFilter = [], $arrAllowedExt = [], $arrFilterExt = [])
    {
        $arrFilter = array_merge([
            '.svn',
            '.git',
            'node_modules',
            '.gitkeep',
        ], $arrFilter);

        $arrReturnData = [
            'file' => [],
            'dir'  => [],
        ];

        if (is_dir($sDir)) {
            $arrFiles = [];

            $objDir = new DirectoryIterator($sDir);
            foreach ($objDir as $objFile) {
                if ($objFile->isDot() || in_array($objFile->getFilename(), $arrFilter, true)) {
                    continue;
                }

                if ($objFile->isDir() && in_array($strReturnType, [
                    'dir',
                    'both',
                ], true)) {
                    $arrReturnData['dir'][] = $booFullpath ? $objFile->getRealPath() : $objFile->getFilename();
                }

                $strExt = static::getExtension($objFile->getFilename(), 2);

                if ($objFile->isFile() && in_array($strReturnType, [
                    'file',
                    'both',
                ], true) && (!$arrFilterExt || !in_array($strExt, $arrFilterExt, true)) && (!$arrAllowedExt || in_array($strExt, $arrAllowedExt, true))) {
                    $arrReturnData['file'][] = $booFullpath ? $objFile->getRealPath() : $objFile->getFilename();
                }
            }

            if ('file' === $strReturnType) {
                return $arrReturnData['file'];
            }
            if ('dir' === $strReturnType) {
                return $arrReturnData['dir'];
            }

            return $arrReturnData;
        }

        return [];
    }

    /**
     * 整理目录斜线风格
     *
     * @param string $sPath
     * @param bool   $bUnix
     *
     * @return string
     */
    public static function tidyPath($sPath, $bUnix = true)
    {
        $sPath = str_replace('\\', '/', $sPath);
        $sPath = preg_replace('|/+|', '/', $sPath);
        $sPath = str_replace(':/', ':\\', $sPath);
        if (!$bUnix) {
            $sPath = str_replace('/', '\\', $sPath);
        }

        return rtrim($sPath, '\\/');
    }

    /**
     * 格式化文件或者目录为 Linux 风格
     *
     * @param string $strPath
     * @param bool   $booWindowsWithLetter
     *
     * @return string
     */
    public static function tidyPathLinux($strPath, $booWindowsWithLetter = false)
    {
        $strPath = ltrim(static::tidyPath($strPath, true), '//');
        if (false !== strpos($strPath, ':\\')) {
            $arrTemp = explode(':\\', $strPath);
            $strPath = (true === $booWindowsWithLetter ? strtolower($arrTemp[0]).'/' : '').$arrTemp[1];
        }

        return '/'.$strPath;
    }

    /**
     * 判断是否为绝对路径.
     *
     * @param string $sPath
     *
     * @return bool
     */
    public static function isAbsolute($sPath)
    {
        return preg_match('/^(\/|[a-z]:)/i', $sPath);
    }

    /**
     * 根据 ID 获取打散目录.
     *
     * @param int $intDataId
     *
     * @return array
     */
    public static function distributed($intDataId)
    {
        $intDataId = abs((int) $intDataId);
        $intDataId = sprintf('%09d', $intDataId); // 格式化为 9 位数，前面不够填充 0
        return [
            substr($intDataId, 0, 3).'/'.substr($intDataId, 3, 2).'/'.substr($intDataId, 5, 2).'/',
            substr($intDataId, -2),
        ];
    }

    /**
     * 新建文件.
     *
     * @param $sPath string
     * @param $nMode=0766 int
     *
     * @return bool
     */
    public static function createFile($sPath, $nMode = 0766)
    {
        $sDir = dirname($sPath);

        if (is_file($sDir)) {
            throw new InvalidArgumentException('Dir cannot be a file.');
        }

        if (!file_exists($sDir) && static::createDirectory($sDir)) {
            throw new RuntimeException(sprint('Create dir %s failed.', $sDir));
        }

        if ($hFile = fopen($sPath, 'a')) {
            chmod($sPath, $nMode);

            return fclose($hFile);
        }

        throw new RuntimeException(sprint('Create file %s failed.', $sPath));
    }

    /**
     * 获取上传文件扩展名.
     *
     * @param string $sFileName 文件名
     * @param int    $nCase     格式化参数 0 默认，1 转为大小 ，转为大小
     *
     * @return string
     */
    public static function getExtension($sFileName, $nCase = 0)
    {
        $sFileName = pathinfo($sFileName, PATHINFO_EXTENSION);
        if (1 === $nCase) {
            return strtoupper($sFileName);
        }
        if (2 === $nCase) {
            return strtolower($sFileName);
        }

        return $sFileName;
    }

    /**
     * 获取文件名字.
     *
     * @param string $strPath
     *
     * @return string
     */
    public static function getName($strPath)
    {
        return pathinfo($strPath, PATHINFO_FILENAME);
    }

    /**
     * 是否为目录.
     *
     * @param string $strDirectory
     *
     * @return bool
     */
    public static function isDirectory($strDirectory)
    {
        return is_dir($strDirectory);
    }

    /**
     * 是否可写.
     *
     * @param string $strPath
     *
     * @return bool
     */
    public static function isWritable($strPath)
    {
        return is_writable($strPath);
    }

    /**
     * 是否为文件.
     *
     * @param string $strFile
     *
     * @return bool
     */
    public static function isFile($strFile)
    {
        return is_file($strFile);
    }
}
