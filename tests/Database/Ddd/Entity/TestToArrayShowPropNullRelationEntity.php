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

class TestToArrayShowPropNullRelationEntity extends Entity
{
    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id'          => [],
        'name'        => [],
        'description' => [],
        'address'     => [
            self::SHOW_PROP_NULL => '',
        ],
        'foo_bar'     => [
            self::SHOW_PROP_NULL => null,
        ],
        'hello'       => [
            self::SHOW_PROP_NULL => 'default_value',
        ],
        'target' => [
            self::HAS_ONE     => TestToArrayShowPropNullRelationTargetEntity::class,
            self::SOURCE_KEY  => 'id',
            self::TARGET_KEY  => 'id',
        ],
    ];

    private static $leevelConnect;

    private $id;

    private $name;

    private $description;

    private $address;

    private $fooBar;

    private $hello;

    private $target;

    public function setter(string $prop, $value): IEntity
    {
        $this->{$this->realProp($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->realProp($prop)};
    }

    public static function withConnect($connect): void
    {
        static::$leevelConnect = $connect;
    }

    public static function connect()
    {
        return static::$leevelConnect;
    }
}