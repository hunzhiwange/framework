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

class TestConstructPropBlackEntity extends Entity
{
    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY             => true,
            self::CONSTRUCT_PROP_BLACK => true,
        ],
        'name' => [],
    ];

    private array $data = [];

    private static ?string $connect = null;

    public function setter(string $prop, $value): self
    {
        $this->data[$this->realProp($prop)] = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->data[$this->realProp($prop)] ?? null;
    }

    public static function withConnect(?string $connect = null): void
    {
        static::$connect = $connect;
    }

    public static function connect(): ?string
    {
        return static::$connect;
    }
}
