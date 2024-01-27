<?php

declare(strict_types=1);

namespace Tests\Server\Fixtures;

use Swoole\Http\Response;

/**
 * @see https://github.com/swoole/ide-helper/blob/master/output/swoole/namespace/Http/Response.php
 */
class SwooleHttpResponseDemo extends Response
{
    public function header($key, $value, $ucwords = null): void
    {
        $GLOBALS['swoole.response']['header'][] = \func_get_args();
    }

    public function status($http_code, $reason = null): void
    {
        $GLOBALS['swoole.response']['status'] = \func_get_args();
    }

    public function write($content): void
    {
        $GLOBALS['swoole.response']['write'] = \func_get_args();
    }

    public function redirect($location, $http_code = null): void
    {
        $GLOBALS['swoole.response']['redirect'] = \func_get_args();
    }
}
