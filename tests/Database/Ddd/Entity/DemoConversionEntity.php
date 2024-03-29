<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Database\Ddd\Struct;

class DemoConversionEntity extends Entity
{
    public const TABLE = 'test';

    public const ID = 'id';

    public const AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    protected ?int $id = null;

    #[Struct([
    ])]
    protected $int1;

    #[Struct([
    ])]
    protected $int2;

    #[Struct([
    ])]
    protected $int3;

    #[Struct([
    ])]
    protected $float1;

    #[Struct([
    ])]
    protected $float2;

    #[Struct([
    ])]
    protected $float3;

    #[Struct([
    ])]
    protected $string1;

    #[Struct([
    ])]
    protected $string2;

    #[Struct([
    ])]
    protected $bool1;

    #[Struct([
    ])]
    protected $bool2;

    #[Struct([
    ])]
    protected $bool3;

    #[Struct([
    ])]
    protected $bool4;

    #[Struct([
    ])]
    protected $obj1;

    #[Struct([
    ])]
    protected $obj2;

    #[Struct([
    ])]
    protected $obj3;

    #[Struct([
    ])]
    protected $arr1;

    #[Struct([
    ])]
    protected $arr2;

    #[Struct([
    ])]
    protected $json1;

    #[Struct([
    ])]
    protected $json2;

    #[Struct([
    ])]
    protected $coll1;

    #[Struct([
    ])]
    protected $coll2;

    #[Struct([
    ])]
    protected $invalidSetter;

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

    public function getObj1(): \stdClass
    {
        return json_decode($this->getter('obj1'));
    }

    public function setObj2(string $value): Entity
    {
        $value = json_decode($value, true);

        return $this->setter('obj2', json_encode($value, JSON_FORCE_OBJECT));
    }

    public function getObj2(): \stdClass
    {
        return json_decode($this->getter('obj2'));
    }

    public function setObj3(\stdClass $value): Entity
    {
        return $this->setter('obj3', json_encode($value));
    }

    public function getObj3(): \stdClass
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
        $data = json_decode($this->getter('coll1'), true);

        return new Collection([new DemoEntity($data)]);
    }

    public function setColl2(array $value): Entity
    {
        return $this->setter('coll2', json_encode($value));
    }

    public function getColl2(): Collection
    {
        $data = json_decode($this->getter('coll2'), true);

        return new Collection([new DemoEntity($data)]);
    }

    public function setInvalidSetter($value): void
    {
    }
}
