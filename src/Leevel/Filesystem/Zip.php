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

use InvalidArgumentException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;

/**
 * filesystem.zip.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/adapter/zip-archive/
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Zip extends Filesystem implements IFilesystem
{
    /**
     * 配置.
     *
     * @var array
     */
    protected array $option = [
        'path' => '',
    ];

    /**
     * 创建连接.
     *
     * @throws \InvalidArgumentException
     */
    protected function makeAdapter(): AdapterInterface
    {
        if (empty($this->option['path'])) {
            $e = 'The zip requires path option.';

            throw new InvalidArgumentException($e);
        }

        if (!class_exists('League\Flysystem\ZipArchive\ZipArchiveAdapter')) {
            $e = 'Please run composer require league/flysystem-ziparchive.';

            throw new InvalidArgumentException($e);
        }

        return new ZipArchiveAdapter($this->option['path']);
    }
}
