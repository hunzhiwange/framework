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

namespace Leevel\Encryption;

use function Leevel\Support\Helper\fn;
use function Leevel\Support\Str\un_camelize;

/**
 * 安全函数.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 */
class Safe
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        $fn = '\\Leevel\\Encryption\\Safe\\'.un_camelize($method);

        return fn($fn, ...$args);
    }
}

// @codeCoverageIgnoreStart
if (!function_exists('Leevel\\Support\\Str\\un_camelize')) {
    include dirname(__DIR__).'/Support/Str/un_camelize.php';
}

if (!function_exists('Leevel\\Support\\Helper\\fn')) {
    include dirname(__DIR__).'/Support/Helper/fn.php';
}
// @codeCoverageIgnoreEnd
