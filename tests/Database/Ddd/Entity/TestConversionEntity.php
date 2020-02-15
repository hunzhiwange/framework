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

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use stdClass;

class TestConversionEntity extends Entity
{
    const TABLE = 'test';

    const ID = 'id';

    const AUTO = 'id';

    const STRUCT = [
        'id' => [
            self::READONLY => true,
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

    private static ?string $connect = null;

    private $_id;

    private $_int1;

    private $_int2;

    private $_int3;

    private $_float1;

    private $_float2;

    private $_float3;

    private $_string1;

    private $_string2;

    private $_bool1;

    private $_bool2;

    private $_bool3;

    private $_bool4;

    private $_obj1;

    private $_obj2;

    private $_obj3;

    private $_arr1;

    private $_arr2;

    private $_json1;

    private $_json2;

    private $_coll1;

    private $_coll2;

    public function setInt1($value)
    {
        $this->_int1 = (int) $value;
    }

    public function getInt1(): int
    {
        return $this->_int1 + 1;
    }

    public function setInt2(int $value)
    {
        $this->_int2 = $value;
    }

    public function getInt2(): int
    {
        return $this->_int2;
    }

    public function setFloat1($value)
    {
        $this->_float1 = (float) $value;
    }

    public function getFloat1(): float
    {
        return $this->_float1 + 1;
    }

    public function setFloat2(float $value)
    {
        $this->_float2 = $value;
    }

    public function getFloat2(): float
    {
        return $this->_float2;
    }

    public function setFloat3(float $value)
    {
        $this->_float3 = $value;
    }

    public function getFloat3(): float
    {
        return $this->_float3;
    }

    public function setString1($value)
    {
        $this->_string1 = (string) $value;
    }

    public function getString1(): string
    {
        return $this->_string1;
    }

    public function setString2(string $value)
    {
        $this->_string2 = $value;
    }

    public function getString2(): string
    {
        return $this->_string2;
    }

    public function setBool1($value)
    {
        $this->_bool1 = (bool) $value;
    }

    public function getBool1(): bool
    {
        return $this->_bool1;
    }

    public function setBool2($value)
    {
        $this->_bool2 = (bool) $value;
    }

    public function getBool2(): bool
    {
        return $this->_bool2;
    }

    public function setBool3(bool $value)
    {
        $this->_bool3 = $value;
    }

    public function getBool3(): bool
    {
        return $this->_bool3;
    }

    public function setBool4(bool $value)
    {
        $this->_bool4 = $value;
    }

    public function getBool4(): bool
    {
        return $this->_bool4;
    }

    public function setObj1($value)
    {
        $this->_obj1 = json_encode($value, JSON_FORCE_OBJECT);
    }

    public function getObj1(): stdClass
    {
        return json_decode($this->_obj1);
    }

    public function setObj2(string $value)
    {
        $value = json_decode($value, true);
        $this->_obj2 = json_encode($value, JSON_FORCE_OBJECT);
    }

    public function getObj2(): stdClass
    {
        return json_decode($this->_obj2);
    }

    public function setObj3(stdClass $value)
    {
        $this->_obj3 = json_encode($value);
    }

    public function getObj3(): stdClass
    {
        return json_decode($this->_obj3);
    }

    public function setArr1(array $value)
    {
        $this->_arr1 = json_encode($value);
    }

    public function getArr1(): array
    {
        return json_decode($this->_arr1, true);
    }

    public function setArr2(string $value)
    {
        $this->_arr2 = $value;
    }

    public function getArr2(): array
    {
        return json_decode($this->_arr2, true);
    }

    public function setJson1(array $value)
    {
        $this->_json1 = json_encode($value);
    }

    public function getJson1(): array
    {
        return json_decode($this->_json1, true);
    }

    public function setJson2(string $value)
    {
        $this->_json2 = $value;
    }

    public function getJson2(): array
    {
        return json_decode($this->_json2, true);
    }

    public function setColl1(string $value)
    {
        $this->_coll1 = $value;
    }

    public function getColl1(): Collection
    {
        return new Collection(json_decode($this->_coll1, true));
    }

    public function setColl2(array $value)
    {
        $this->_coll2 = json_encode($value);
    }

    public function getColl2(): Collection
    {
        return new Collection(json_decode($this->_coll2, true));
    }

    public function setter(string $prop, $value): self
    {
        $this->{'_'.$this->realProp($prop)} = $value;

        return $this;
    }

    public function getter(string $prop)
    {
        return $this->{'_'.$this->realProp($prop)};
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
