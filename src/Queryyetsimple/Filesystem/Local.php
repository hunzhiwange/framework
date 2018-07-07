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
use League\Flysystem\Adapter\Local as AdapterLocal;

/**
 * filesystem.local.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.29
 * @see https://flysystem.thephpleague.com/adapter/local/
 *
 * @version 1.0
 */
class Local extends Connect implements IConnect
{
    /**
     * 配置.
     *
     * @var array
     */
    protected $option = [
        'path'          => '',
        'write_flags'   => LOCK_EX,
        'link_handling' => AdapterLocal::DISALLOW_LINKS,
        'permissions'   => [],
    ];

    /**
     * 创建连接.
     *
     * @return \League\Flysystem\AdapterInterface
     */
    public function makeConnect()
    {
        if (empty($this->getOption('path'))) {
            throw new InvalidArgumentException(
                'The local requires path option'
            );
        }

        return new AdapterLocal(
            $this->getOption('path'),
            $this->getOption('write_flags'),
            $this->getOption('link_handling'),
            $this->getOption('permissions')
        );
    }
}
