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

namespace Leevel\Log\Helper;

use Leevel\Leevel\App;
use Leevel\Log\ILog;

/**
 * 日志.
 *
 * @param null|string $message = null
 * @param array       $context
 * @param string      $level
 *
 * @return mixed
 */
function log(?string $message = null, array $context = [], string $level = ILog::INFO)
{
    $service = App::singletons()->make('logs');

    if (null === $message) {
        return $service;
    }

    $service->log($level, $message, $context);
}
