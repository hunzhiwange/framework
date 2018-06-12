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

use League\Flysystem\Filesystem as LeagueFilesystem;
use Leevel\Option\TClass;

/**
 * connect 驱动抽象类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/api/
 *
 * @version 1.0
 */
abstract class Connect
{
    use TClass;

    /**
     * Filesystem.
     *
     * @var \League\Flysystem\Filesystem
     */
    protected $objFilesystem;

    /**
     * 构造函数.
     *
     * @param array $arrOption
     */
    public function __construct(array $arrOption = [])
    {
        $this->options($arrOption);
        $this->filesystem();
    }

    /**
     * call.
     *
     * @param string $sMethod
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        return $this->objFilesystem->{$sMethod}(...$arrArgs);
    }

    /**
     * 返回 Filesystem.
     *
     * @return \League\Flysystem\Filesystem
     */
    public function getFilesystem()
    {
        return $this->objFilesystem;
    }

    /**
     * 生成 Filesystem.
     *
     * @return \League\Flysystem\Filesystem
     */
    protected function filesystem()
    {
        return $this->objFilesystem = new LeagueFilesystem($this->makeConnect(), $this->getOptions());
    }
}
