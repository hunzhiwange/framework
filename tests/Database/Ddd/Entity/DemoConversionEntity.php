<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetterProp;
use stdClass;

class DemoConversionEntity extends Entity
{
    use GetterSetterProp;

    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    public const STRUCT = [
        'id' => [
            self::READONLY => true,
        ],
        'int1'           => [],
        'int2'           => [],
        'float1'         => [],
        'float2'         => [],
        'float3'         => [],
        'string1'        => [],
        'string2'        => [],
        'bool1'          => [],
        'bool2'          => [],
        'bool3'          => [],
        'bool4'          => [],
        'obj1'           => [],
        'obj2'           => [],
        'obj3'           => [],
        'arr1'           => [],
        'arr2'           => [],
        'json1'          => [],
        'json2'          => [],
        'coll1'          => [],
        'coll2'          => [],
        'invalid_setter' => [],
    ];

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

    private $_invalidSetter;

    public function setInt1($value): Entity
    {
        $this->_int1 = (int) $value;

        return $this;
    }

    public function getInt1(): int
    {
        return $this->_int1 + 1;
    }

    public function setInt2(int $value): Entity
    {
        $this->_int2 = $value;

        return $this;
    }

    public function getInt2(): int
    {
        return $this->_int2;
    }

    public function setFloat1($value): Entity
    {
        $this->_float1 = (float) $value;

        return $this;
    }

    public function getFloat1(): float
    {
        return $this->_float1 + 1;
    }

    public function setFloat2(float $value): Entity
    {
        $this->_float2 = $value;

        return $this;
    }

    public function getFloat2(): float
    {
        return $this->_float2;
    }

    public function setFloat3(float $value): Entity
    {
        $this->_float3 = $value;

        return $this;
    }

    public function getFloat3(): float
    {
        return $this->_float3;
    }

    public function setString1($value): Entity
    {
        $this->_string1 = (string) $value;

        return $this;
    }

    public function getString1(): string
    {
        return $this->_string1;
    }

    public function setString2(string $value): Entity
    {
        $this->_string2 = $value;

        return $this;
    }

    public function getString2(): string
    {
        return $this->_string2;
    }

    public function setBool1($value): Entity
    {
        $this->_bool1 = (bool) $value;

        return $this;
    }

    public function getBool1(): bool
    {
        return $this->_bool1;
    }

    public function setBool2($value): Entity
    {
        $this->_bool2 = (bool) $value;

        return $this;
    }

    public function getBool2(): bool
    {
        return $this->_bool2;
    }

    public function setBool3(bool $value): Entity
    {
        $this->_bool3 = $value;

        return $this;
    }

    public function getBool3(): bool
    {
        return $this->_bool3;
    }

    public function setBool4(bool $value): Entity
    {
        $this->_bool4 = $value;

        return $this;
    }

    public function getBool4(): bool
    {
        return $this->_bool4;
    }

    public function setObj1($value): Entity
    {
        $this->_obj1 = json_encode($value, JSON_FORCE_OBJECT);

        return $this;
    }

    public function getObj1(): stdClass
    {
        return json_decode($this->_obj1);
    }

    public function setObj2(string $value): Entity
    {
        $value = json_decode($value, true);
        $this->_obj2 = json_encode($value, JSON_FORCE_OBJECT);

        return $this;
    }

    public function getObj2(): stdClass
    {
        return json_decode($this->_obj2);
    }

    public function setObj3(stdClass $value): Entity
    {
        $this->_obj3 = json_encode($value);

        return $this;
    }

    public function getObj3(): stdClass
    {
        return json_decode($this->_obj3);
    }

    public function setArr1(array $value): Entity
    {
        $this->_arr1 = json_encode($value);

        return $this;
    }

    public function getArr1(): array
    {
        return json_decode($this->_arr1, true);
    }

    public function setArr2(string $value): Entity
    {
        $this->_arr2 = $value;

        return $this;
    }

    public function getArr2(): array
    {
        return json_decode($this->_arr2, true);
    }

    public function setJson1(array $value): Entity
    {
        $this->_json1 = json_encode($value);

        return $this;
    }

    public function getJson1(): array
    {
        return json_decode($this->_json1, true);
    }

    public function setJson2(string $value): Entity
    {
        $this->_json2 = $value;

        return $this;
    }

    public function getJson2(): array
    {
        return json_decode($this->_json2, true);
    }

    public function setColl1(string $value): Entity
    {
        $this->_coll1 = $value;

        return $this;
    }

    public function getColl1(): Collection
    {
        return new Collection(json_decode($this->_coll1, true));
    }

    public function setColl2(array $value): Entity
    {
        $this->_coll2 = json_encode($value);

        return $this;
    }

    public function getColl2(): Collection
    {
        return new Collection(json_decode($this->_coll2, true));
    }

    public function setInvalidSetter($value)
    {
        $this->_invalidSetter = $value;
    }
}
