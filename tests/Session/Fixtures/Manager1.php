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

namespace Tests\Session\Fixtures;

use Leevel\Session\ISession;
use Leevel\Session\Manager as Managers;

class Manager1 extends Managers
{
    private static ISession $connect;

    public function connect($options = null, bool $onlyNew = false): object
    {
        return static::$connect;
    }

    public static function setConnect(ISession $connect): void
    {
        static::$connect = $connect;
    }
}
