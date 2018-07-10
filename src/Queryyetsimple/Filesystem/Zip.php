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

use InvalidArgumentException;
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
 */
class Zip extends Connect implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'path' => '',
    ];

    /**
     * 创建连接.
     *
     * @return \League\Flysystem\AdapterInterface
     */
    public function makeConnect()
    {
        if (empty($this->option['path'])) {
            throw new InvalidArgumentException(
                'The zip requires path option'
            );
        }

        if (!class_exists('League\Flysystem\ZipArchive\ZipArchiveAdapter')) {
            throw new InvalidArgumentException(
                'Please run composer require league/flysystem-ziparchive'
            );
        }

        return new ZipArchiveAdapter($this->option['path']);
    }
}
