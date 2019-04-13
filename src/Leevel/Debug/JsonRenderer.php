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

namespace Leevel\Debug;

/**
 * Json 渲染.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class JsonRenderer
{
    /**
     * debug 管理.
     *
     * @var \Leevel\Debug\Debug
     */
    protected $debugBar;

    /**
     * 构造函数.
     *
     * @param \Leevel\Debug\Debug $debugBar
     */
    public function __construct(Debug $debugBar)
    {
        $this->debugBar = $debugBar;
    }

    /**
     * 渲染数据.
     *
     * @return array
     */
    public function render(): array
    {
        return $this->debugBar->getData();
    }
}
