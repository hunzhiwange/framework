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

namespace Leevel\Database;

/**
 * database 仓储.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.11.08
 *
 * @version 1.0
 */
class Database implements IDatabase
{
    /**
     * 数据库连接对象
     *
     * @var \Leevel\Database\IConnect
     */
    protected $objConnect;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\IConnect $objConnect
     */
    public function __construct(IConnect $objConnect)
    {
        $this->objConnect = $objConnect;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        return $this->objConnect->{$method}(...$arrArgs);
    }
}
