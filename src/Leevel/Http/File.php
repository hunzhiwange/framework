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

namespace Leevel\Http;

use function Leevel\Filesystem\Fso\create_directory;
use Leevel\Filesystem\Fso\create_directory;
use SplFileObject;

/**
 * 文件
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.26
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class File extends SplFileObject
{
    /**
     * 构造函数.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        parent::__construct($path);
    }

    /**
     * 移动文件.
     *
     * @param string $directory
     * @param string $name
     *
     * @return \Leevel\Http\File
     */
    public function move(string $directory, ?string $name = null): self
    {
        $target = $this->getTargetFile($directory, $name);

        $this->moveToTarget($this->getPathname(), $target);

        return new self($target);
    }

    /**
     * 获取目标文件.
     *
     * @param string $directory
     * @param string $name
     *
     * @return string
     */
    protected function getTargetFile(string $directory, ?string $name = null): string
    {
        create_directory($directory);

        $target = rtrim($directory, '/\\').\DIRECTORY_SEPARATOR.
            ($name ?? $this->getBasename());

        return $target;
    }

    /**
     * 移动文件到目标文件.
     *
     * @param string $sourcePath
     * @param string $target
     * @param bool   $isUploaded
     */
    protected function moveToTarget(string $sourcePath, string $target, bool $isUploaded = false): void
    {
        set_error_handler(function ($type, $msg) {
            throw new FileException($msg);
        });

        $method = $isUploaded ? 'move_uploaded_file' : 'rename';
        $method($sourcePath, $target);

        restore_error_handler();

        chmod($target, 0666 & ~umask());
    }
}

// import fn.
class_exists(create_directory::class);
