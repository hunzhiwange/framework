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

namespace Tests\Protocol\Fixtures;

use Swoole\Http\Response;

/**
 * @see https://github.com/swoole/ide-helper/blob/master/output/swoole/namespace/Http/Response.php
 */
class SwooleHttpResponseDemo extends Response
{
    public function header($key, $value, $ucwords = null)
    {
        $GLOBALS['swoole.response']['header'][] = \func_get_args();
    }

    public function status($http_code, $reason = null)
    {
        $GLOBALS['swoole.response']['status'] = \func_get_args();
    }

    public function write($content)
    {
        $GLOBALS['swoole.response']['write'] = \func_get_args();
    }

    public function cookie($name, $value = null, $expires = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        $GLOBALS['swoole.response']['cookie'][] = \func_get_args();
    }

    public function redirect($location, $http_code = null)
    {
        $GLOBALS['swoole.response']['redirect'] = \func_get_args();
    }
}
