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

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;

class DemoCreateAutoFillEntity extends Entity
{
    use GetterSetter;

    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'name' => [
            self::CREATE_FILL       => 'name for '.self::CREATE_FILL,
        ],
        'description' => [
            self::CREATE_FILL   => null,
        ],
        'address' => [
            self::CREATE_FILL    => null,
        ],
        'foo_bar' => [
            self::CREATE_FILL    => null,
        ],
        'hello' => [
            self::CREATE_FILL      => null,
        ],
    ];

    protected function fillDescription($old): string
    {
        return 'set description.';
    }

    protected function fillAddress($old): string
    {
        return 'address is set now.';
    }

    protected function fillFooBar($old): string
    {
        return 'foo bar.';
    }

    protected function fillHello($old): string
    {
        return 'hello field.';
    }
}