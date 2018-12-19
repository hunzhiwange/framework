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

namespace Leevel\Leevel\Testing;

use Leevel\Leevel\Project;
use Leevel\Support\Facade;
use PHPUnit\Framework\TestCase as TestCases;

/**
 * phpunit 基础测试类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.08
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
abstract class TestCase extends TestCases
{
    use Helper;

    /**
     * 创建的项目.
     *
     * @var \Leevel\Leevel\Project
     */
    protected $project;

    /**
     * Setup.
     */
    protected function setUp()
    {
        if (!$this->project) {
            $this->project = $this->createProject();
        }

        Facade::remove();
    }

    /**
     * tearDown.
     */
    protected function tearDown()
    {
        $this->project = null;
    }

    /**
     * 创建日志目录.
     *
     * @var array
     */
    abstract protected function makeLogsDir(): array;

    /**
     * 初始化项目.
     *
     * @return \Leevel\Leevel\Project
     */
    abstract protected function createProject(): Project;
}
