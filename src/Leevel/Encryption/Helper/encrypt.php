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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Encryption\Helper;

use Leevel\Di\Container;

/**
 * 加密字符串.
 *
 * @since 2016.11.26
 *
 * @version 1.0
 */
function encrypt(string $value, int $expiry = 0): string
{
    /** @var \Leevel\Encryption\Encryption $service */
    $service = Container::singletons()->make('encryption');

    return $service->encrypt($value, $expiry);
}

class encrypt
{
}
