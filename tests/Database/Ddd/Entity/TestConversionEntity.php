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

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IEntity;
use stdClass;

/**
 * TestConversionEntity.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.02
 *
 * @version 1.0
 */
class TestConversionEntity extends Entity
{
    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            'readonly' => true,
        ],
        'int1'    => [],
        'int2'    => [],
        'float1'  => [],
        'float2'  => [],
        'float3'  => [],
        'string1' => [],
        'string2' => [],
        'bool1'   => [],
        'bool2'   => [],
        'bool3'   => [],
        'bool4'   => [],
        'obj1'    => [],
        'obj2'    => [],
        'obj3'    => [],
        'arr1'    => [],
        'arr2'    => [],
        'json1'   => [],
        'json2'   => [],
        'coll1'   => [],
        'coll2'   => [],
    ];

    private $id;

    private $int1;

    private $int2;

    private $int3;

    private $float1;

    private $float2;

    private $float3;

    private $string1;

    private $string2;

    private $bool1;

    private $bool2;

    private $bool3;

    private $bool4;

    private $obj1;

    private $obj2;

    private $obj3;

    private $arr1;

    private $arr2;

    private $json1;

    private $json2;

    private $coll1;

    private $coll2;

    public function setterInt1($value)
    {
        $this->int1 = (int) $value;
    }

    public function getterInt1(): int
    {
        return $this->int1 + 1;
    }

    public function setterInt2(int $value)
    {
        $this->int2 = $value;
    }

    public function getterInt2(): int
    {
        return $this->int2;
    }

    public function setterFloat1($value)
    {
        $this->float1 = (float) $value;
    }

    public function getterFloat1(): float
    {
        return $this->float1 + 1;
    }

    public function setterFloat2(float $value)
    {
        $this->float2 = $value;
    }

    public function getterFloat2(): float
    {
        return $this->float2;
    }

    public function setterFloat3(float $value)
    {
        $this->float3 = $value;
    }

    public function getterFloat3(): float
    {
        return $this->float3;
    }

    public function setterString1($value)
    {
        $this->string1 = (string) $value;
    }

    public function getterString1(): string
    {
        return $this->string1;
    }

    public function setterString2(string $value)
    {
        $this->string2 = $value;
    }

    public function getterString2(): string
    {
        return $this->string2;
    }

    public function setterBool1($value)
    {
        $this->bool1 = (bool) $value;
    }

    public function getterBool1(): bool
    {
        return $this->bool1;
    }

    public function setterBool2($value)
    {
        $this->bool2 = (bool) $value;
    }

    public function getterBool2(): bool
    {
        return $this->bool2;
    }

    public function setterBool3(bool $value)
    {
        $this->bool3 = $value;
    }

    public function getterBool3(): bool
    {
        return $this->bool3;
    }

    public function setterBool4(bool $value)
    {
        $this->bool4 = $value;
    }

    public function getterBool4(): bool
    {
        return $this->bool4;
    }

    public function setterObj1($value)
    {
        $this->obj1 = json_encode($value, JSON_FORCE_OBJECT);
    }

    public function getterObj1(): stdClass
    {
        return json_decode($this->obj1);
    }

    public function setterObj2(string $value)
    {
        $value = json_decode($value, true);
        $this->obj2 = json_encode($value, JSON_FORCE_OBJECT);
    }

    public function getterObj2(): stdClass
    {
        return json_decode($this->obj2);
    }

    public function setterObj3(stdClass $value)
    {
        $this->obj3 = json_encode($value);
    }

    public function getterObj3(): stdClass
    {
        return json_decode($this->obj3);
    }

    public function setterArr1(array $value)
    {
        $this->arr1 = json_encode($value);
    }

    public function getterArr1(): array
    {
        return json_decode($this->arr1, true);
    }

    public function setterArr2(string $value)
    {
        $this->arr2 = $value;
    }

    public function getterArr2(): array
    {
        return json_decode($this->arr2, true);
    }

    public function setterJson1(array $value)
    {
        $this->json1 = json_encode($value);
    }

    public function getterJson1(): array
    {
        return json_decode($this->json1, true);
    }

    public function setterJson2(string $value)
    {
        $this->json2 = $value;
    }

    public function getterJson2(): array
    {
        return json_decode($this->json2, true);
    }

    public function setterColl1(string $value)
    {
        $this->coll1 = $value;
    }

    public function getterColl1(): Collection
    {
        return new Collection(json_decode($this->coll1, true));
    }

    public function setterColl2(array $value)
    {
        $this->coll2 = json_encode($value);
    }

    public function getterColl2(): Collection
    {
        return new Collection(json_decode($this->coll2, true));
    }

    public function setter(string $prop, $value): IEntity
    {
        $this->{$this->prop($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{$this->prop($prop)};
    }
}
