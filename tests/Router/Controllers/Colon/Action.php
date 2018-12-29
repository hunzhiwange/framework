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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router\Controllers\Colon;

/**
 * action.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.29
 *
 * @version 1.0
 */
class Action
{
    public function fooBar(): string
    {
        return 'hello colon with action and action is not single class';
    }

    public function moreFooBar(): string
    {
        return 'hello colon with action and action is not single class with more than one';
    }

    public function beforeButFirst(): string
    {
        return 'hello colon with action and action is not single class before but first';
    }
}
