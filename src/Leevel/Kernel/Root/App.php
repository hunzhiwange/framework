<?php

declare(strict_types=1);

use Leevel\Kernel\Proxy\App as ProxyApp;

if (!class_exists('App')) {
    /**
     * 代理 app 别名.
     */
    class App extends ProxyApp {}
}
