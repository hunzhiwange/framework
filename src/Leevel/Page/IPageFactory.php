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

namespace Leevel\Page;

/**
 * 分页工厂接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
interface IPageFactory
{
    /**
     * 创建分页对象.
     *
     * @param int   $perPage
     * @param int   $totalRecord
     * @param array $option
     *
     * @return \Leevel\Page\Page
     */
    public function make(int $perPage, int $totalRecord, array $option = []): Page;

    /**
     * 创建一个无限数据的分页对象.
     *
     * @param int   $perPage
     * @param array $optoin
     *
     * @return \Leevel\Page\Page
     */
    public function makeMacro(int $perPage, array $option = []): Page;

    /**
     * 创建一个只有上下页的分页对象.
     *
     * @param int   $perPage
     * @param array $option
     *
     * @return \Leevel\Page\Page
     */
    public function makePrevNext(int $perPage, array $option = []): Page;
}
