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

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;

/**
 * TestToArrayEntity.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.02
 *
 * @version 1.0
 */
class TestToArrayEntity extends Entity
{
    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'          => [],
        'name'        => [],
        'description' => [],
        'address'     => [],
        'foo_bar'     => [],
        'hello'       => [],
    ];

    private $id;

    private $name;

    private $description;

    private $address;

    private $fooBar;

    private $hello;

    private $append1;

    private $append2;

    public function setter(string $prop, $value): void
    {
        $this->{$prop} = $value;
    }

    public function getter(string $prop)
    {
        return $this->{$prop};
    }

    protected function getAppend1(): string
    {
        return 'append 1';
    }

    protected function getAppend2(): string
    {
        return 'append 2';
    }
}
