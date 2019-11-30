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

namespace Leevel\Filesystem\Fso;

/**
 * 创建软连接.
 *
 * @param string $target
 * @param string $link
 * @codeCoverageIgnore
 */
function link(string $target, string $link): void
{
    if (\DIRECTORY_SEPARATOR !== '\\') {
        symlink($target, $link);

        return;
    }

    $mode = is_dir($target) ? 'J' : 'H';
    exec("mklink /{$mode} \"{$link}\" \"{$target}\"");
}

class link
{
}
