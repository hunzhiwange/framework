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

/**
 * 加载文件收集器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.20
 *
 * @version 1.0
 */
class FilesCollector extends DataCollector implements Renderable
{
    /**
     * {@inheritdoc}
     */
    public function collect(): array
    {
        return [
            'messages' => $this->getIncludedFiles(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgets(): array
    {
        return [
            'files' => [
                'icon'    => 'files-o',
                'widget'  => 'PhpDebugBar.Widgets.VariableListWidget',
                'map'     => 'files.messages',
                'default' => '{}',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'files';
    }

    /**
     * 获取系统加载文件.
     *
     * @return array
     */
    protected function getIncludedFiles(): array
    {
        return get_included_files();
    }
}
