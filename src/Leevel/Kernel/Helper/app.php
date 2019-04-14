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

namespace Leevel\Kernel\Helper;

use Leevel\Leevel\App;

/**
 * 返回应用容器或者注入.
 *
 * @param string $service
 * @param array  $args
 *
 * @return \Leevel\Leevel\App|mixed
 */
function app(?string $service = null, array $args = [])
{
    $app = App::singletons();

    if (null === $service) {
        return $app;
    }

    return $app->make($service, $args);
}
