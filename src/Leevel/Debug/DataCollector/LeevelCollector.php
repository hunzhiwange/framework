<?php

declare(strict_types=1);

namespace Leevel\Debug\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Leevel\Kernel\IApp;

/**
 * 框架基础信息收集器.
 */
class LeevelCollector extends DataCollector implements Renderable
{
    /**
     * 应用.
     */
    protected IApp $app;

    /**
     * 构造函数.
     */
    public function __construct(IApp $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(): array
    {
        $app = $this->app;

        return [
            'version' => $app::VERSION,
            'environment' => $app->environment(),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'leevel';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
    {
        return [
            'version' => [
                'icon' => 'github',
                'tooltip' => 'Version',
                'map' => 'leevel.version',
                'default' => '',
            ],
            'environment' => [
                'icon' => 'desktop',
                'tooltip' => 'Environment',
                'map' => 'leevel.environment',
                'default' => '',
            ],
        ];
    }
}
