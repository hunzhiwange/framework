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

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\IMeta;
use Leevel\Database\Ddd\Meta;

/**
 * TestEventEntity.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.05
 *
 * @version 1.0
 */
class TestEventEntity extends Entity
{
    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'   => [],
        'name' => [],
    ];

    private $id;

    private $name;

    public function setter(string $prop, $value): IEntity
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }

    public static function meta($connect = null): IMeta
    {
        return MockMeta::instance('test', $connect);
    }
}

class MockMeta extends Meta
{
    public function insert(array $saveData)
    {
        return 10;
    }
}
