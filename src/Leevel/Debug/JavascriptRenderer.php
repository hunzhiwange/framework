<?php

declare(strict_types=1);

namespace Leevel\Debug;

use DebugBar\DebugBar;
use DebugBar\JavascriptRenderer as BaseJavascriptRenderer;

/**
 * Javascript 渲染.
 */
class JavascriptRenderer extends BaseJavascriptRenderer
{
    /**
     * 构造函数.
     */
    public function __construct(DebugBar $debugBar, ?string $baseUrl = null, ?string $basePath = null)
    {
        parent::__construct($debugBar, $baseUrl, $basePath);
        $this->addInlineAssets([file_get_contents(__DIR__.'/resources/debug.css')], [], []);
    }
}
