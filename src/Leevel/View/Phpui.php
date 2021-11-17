<?php

declare(strict_types=1);

namespace Leevel\View;

/**
 * phpui 模板处理类.
 */
class Phpui extends View implements IView
{
    /**
     * 配置.
     */
    protected array $option = [
        'theme_path' => '',
        'suffix'     => '.php',
    ];

    /**
     * {@inheritDoc}
     */
    public function display(string $file, array $vars = [], ?string $ext = null): string
    {
        $this->setVar($vars);
        $file = $this->parseDisplayFile($file, $ext);

        return $this->extractVarsAndIncludeFile($file);
    }
}
