<?php

declare(strict_types=1);

namespace Leevel\Debug\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;
use Leevel\Session\ISession;

/**
 * Session 收集器.
 */
class SessionCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /**
     * Session 仓储.
     */
    protected ISession $session;

    /**
     * 构造函数.
     */
    public function __construct(ISession $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritDoc}
     */
    public function collect(): array
    {
        $data = [];
        foreach ($this->session->all() as $key => $value) {
            $data[$key] = is_string($value) ? $value : $this->formatVar($value);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'session';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets(): array
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
