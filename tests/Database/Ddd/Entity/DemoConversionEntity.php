<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\GetterSetter;
use Leevel\Support\Collection;
use stdClass;

class DemoConversionEntity extends Entity
{
    use GetterSetter;

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
        return $this->setter('int1', (int) $value);
    }

    public function getInt1(): int
    {
        return $this->getter('int1') + 1;
    }

    public function setInt2(int $value): Entity
    {
        return $this->setter('int2', $value);
    }

    public function getInt2(): int
    {
        return $this->getter('int2');
    }

    public function setFloat1($value): Entity
    {
        return $this->setter('float1', (float) $value);
    }

    public function getFloat1(): float
    {
        return $this->getter('float1') + 1;
    }

    public function setFloat2(float $value): Entity
    {
        return $this->setter('float2', $value);
    }

    public function getFloat2(): float
    {
        return $this->getter('float2');
    }

    public function setFloat3(float $value): Entity
    {
        return $this->setter('float3', $value);
    }

    public function getFloat3(): float
    {
        return $this->getter('float3');
    }

    public function setString1($value): Entity
    {
        return $this->setter('string1', (string) $value);
    }

    public function getString1(): string
    {
        return $this->getter('string1');
    }

    public function setString2(string $value): Entity
    {
        return $this->setter('string2', $value);
    }

    public function getString2(): string
    {
        return $this->getter('string2');
    }

    public function setBool1($value): Entity
    {
        return $this->setter('bool1', (bool) $value);
    }

    public function getBool1(): bool
    {
        return $this->getter('bool1');
    }

    public function setBool2($value): Entity
    {
        return $this->setter('bool2', (bool) $value);
    }

    public function getBool2(): bool
    {
        return $this->getter('bool2');
    }

    public function setBool3(bool $value): Entity
    {
        return $this->setter('bool3', $value);
    }

    public function getBool3(): bool
    {
        return $this->getter('bool3');
    }

    public function setBool4(bool $value): Entity
    {
        return $this->setter('bool4', $value);
    }

    public function getBool4(): bool
    {
        return $this->getter('bool4');
    }

    public function setObj1($value): Entity
    {
        return $this->setter('obj1', json_encode($value, JSON_FORCE_OBJECT));
    }

    public function getObj1(): stdClass
    {
        return json_decode($this->getter('obj1'));
    }

    public function setObj2(string $value): Entity
    {
        $value = json_decode($value, true);

        return $this->setter('obj2', json_encode($value, JSON_FORCE_OBJECT));
    }

    public function getObj2(): stdClass
    {
        return json_decode($this->getter('obj2'));
    }

    public function setObj3(stdClass $value): Entity
    {
        return $this->setter('obj3', json_encode($value));
    }

    public function getObj3(): stdClass
    {
        return json_decode($this->getter('obj3'));
    }

    public function setArr1(array $value): Entity
    {
        return $this->setter('arr1', json_encode($value));
    }

    public function getArr1(): array
    {
        return json_decode($this->getter('arr1'), true);
    }

    public function setArr2(string $value): Entity
    {
        return $this->setter('arr2', $value);
    }

    public function getArr2(): array
    {
        return json_decode($this->getter('arr2'), true);
    }

    public function setJson1(array $value): Entity
    {
        return $this->setter('json1', json_encode($value));
    }

    public function getJson1(): array
    {
        return json_decode($this->getter('json1'), true);
    }

    public function setJson2(string $value): Entity
    {
        return $this->setter('json2', $value);
    }

    public function getJson2(): array
    {
        return json_decode($this->getter('json2'), true);
    }

    public function setColl1(string $value): Entity
    {
        return $this->setter('coll1', $value);
    }

    public function getColl1(): Collection
    {
        return new Collection(json_decode($this->getter('coll1'), true));
    }

    public function setColl2(array $value): Entity
    {
        return $this->setter('coll2', json_encode($value));
    }

    public function getColl2(): Collection
    {
        return new Collection(json_decode($this->getter('coll2'), true));
    }

    public function setInvalidSetter($value)
    {
    }
}
