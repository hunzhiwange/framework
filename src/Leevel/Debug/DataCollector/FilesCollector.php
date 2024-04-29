<?php

declare(strict_types=1);

namespace Leevel\Debug\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * 加载文件收集器.
 */
class FilesCollector extends DataCollector implements Renderable
{
    public function collect(): array
    {
        return [
            'messages' => $this->getIncludedFiles(),
        ];
    }

    public function getWidgets(): array
    {
        return [
            'files' => [
                'icon' => 'files-o',
                'widget' => 'PhpDebugBar.Widgets.VariableListWidget',
                'map' => 'files.messages',
                'default' => '{}',
            ],
        ];
    }

    public function getName(): string
    {
        return 'files';
    }

    /**
     * 获取系统加载文件.
     */
    protected function getIncludedFiles(): array
    {
        return get_included_files();
    }
}
