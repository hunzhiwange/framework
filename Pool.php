<?php declare(strict_types=1);
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
namespace Leevel\Kernel;

/**
 * 对象池
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.05.17
 * @version 1.0
 */
class Pool
{

    /**
     * 项目
     *
     * @var \Leevel\Kernel\IProject
     */
    protected $project;

    /**
     * 对象池数据
     *
     * @var array
     */
    protected $pools;

    /**
     * 构造函数
     *
     * @param \Leevel\Kernel\IProject $project
     * @return void
     */
    public function __construct(IProject $project)
    {
        $this->project = $project;
    }

    /**
     * 响应 HTTP 请求
     *
     * @param \Leevel\Http\Request $request
     * @return \Leevel\Http\IResponse
     */
    public function handle(Request $request)
    {
    }
}
