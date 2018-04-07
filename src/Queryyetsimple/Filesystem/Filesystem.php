<?php
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

/**
 * filesystem 仓储
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.29
 * @version 1.0
 */
class Filesystem implements IFilesystem
{

    /**
     * 连接驱动
     *
     * @var \Leevel\Filesystem\IConnect
     */
    protected $oConnect;

    /**
     * 构造函数
     *
     * @param \Leevel\Filesystem\IConnect $oConnect
     * @return void
     */
    public function __construct(IConnect $oConnect)
    {
        $this->oConnect = $oConnect;
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        return $this->oConnect->$method(...$arrArgs);
    }
}
