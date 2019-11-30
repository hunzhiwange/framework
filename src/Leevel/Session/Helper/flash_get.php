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

namespace Leevel\Session\Helper;

use Leevel\Di\Container;

/**
 * 返回闪存数据.
 *
 * @param string     $key
 * @param null|mixed $defaults
 *
 * @return mixed
 */
function flash_get(string $key, $defaults = null)
{
    /** @var \Leevel\Session\ISession $session */
    $session = Container::singletons()->make('sessions');

    return $session->getFlash($key, $defaults);
}

class flash_get
{
}
