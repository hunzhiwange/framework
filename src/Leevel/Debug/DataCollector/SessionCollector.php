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
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Leevel\Session\ISession;

/**
 * Session 收集器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /**
     * Session 仓储.
     *
     * @var \Leevel\Session\ISession
     */
    protected $session;

    /**
     * 构造函数.
     *
     * @param \Leevel\Session\ISession $session
     */
    public function __construct(ISession $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function collect()
    {
        $data = [];

        foreach ($this->session->all() as $key => $value) {
            $data[$key] = is_string($value) ? $value : $this->formatVar($value);
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'session';
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets()
    {
        return [
            'session' => [
                'icon'    => 'archive',
                'widget'  => 'PhpDebugBar.Widgets.VariableListWidget',
                'map'     => 'session',
                'default' => '{}',
            ],
        ];
    }
}
