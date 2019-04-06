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

namespace Leevel\Debug\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Leevel\Kernel\IApp;

/**
 * 框架基础信息收集器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class LeevelCollector extends DataCollector implements Renderable
{
    /**
     * 应用管理.
     *
     * @var \Leevel\Kernel\IApp
     */
    protected $app;

    /**
     * 构造函数.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function __construct(IApp $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        $app = $this->app;

        return [
            'version'     => $app::VERSION,
            'environment' => $app->environment(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'leevel';
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets(): array
    {
        return [
            'version' => [
                'icon'    => 'github',
                'tooltip' => 'Version',
                'map'     => 'leevel.version',
                'default' => '',
            ],
            'environment' => [
                'icon'    => 'desktop',
                'tooltip' => 'Environment',
                'map'     => 'leevel.environment',
                'default' => '',
            ],
        ];
    }
}
