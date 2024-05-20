<?php

declare(strict_types=1);

namespace Tests\Server\Fixtures;

use Swoole\Http\Response;

/**
 * @see https://github.com/swoole/ide-helper/blob/master/output/swoole/namespace/Http/Response.php
 */
class SwooleHttpResponseDemo extends Response
{
    public function header(string $key, array|string $value, bool $format = true): bool
    {
        $GLOBALS['swoole.response']['header'][] = \func_get_args();

        return true;
    }

    public function status(int $http_code, ?string $reason = null): bool
    {
        $GLOBALS['swoole.response']['status'] = \func_get_args();

        return true;
    }

    public function write(string $content): bool
    {
        $GLOBALS['swoole.response']['write'] = \func_get_args();

        return true;
    }

    public function redirect(string $location, int $http_code = 302): bool
    {
        $GLOBALS['swoole.response']['redirect'] = \func_get_args();

        return true;
    }
}
