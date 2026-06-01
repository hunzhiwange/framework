<?php

declare(strict_types=1);

namespace Tests\Database\Ddd\Entity;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection as Collection;
use Leevel\Database\Ddd\Struct;

class DemoConversionEntity extends Entity
{
    public const string TABLE = 'test';

    public const string ID = 'id';

    public const string AUTO = 'id';

    #[Struct([
        self::READONLY => true,
    ])]
    public ?int $id = null {
        set(int|string|null $v) {
            $this->id = $this->set('id', $v);
        }
    }

    #[Struct([])]
    public ?int $int1 = null {
        get => $this->getter('int1') + 1;
        set(int|string|null $v) {
            $this->int1 = $this->set('int1', (int) $v);
        }
    }

    #[Struct([])]
    public ?int $int2 = null {
        set(int|string|null $v) {
            $this->int2 = $this->set('int2', (int) $v);
        }
    }

    #[Struct([])]
    public ?float $float1 = null {
        get => $this->getter('float1') + 1;
        set(float|string|null $v) {
            $this->float1 = $this->set('float1', (float) $v);
        }
    }

    #[Struct([])]
    public ?float $float2 = null {
        set(float|string|null $v) {
            $this->float2 = $this->set('float2', (float) $v);
        }
    }

    #[Struct([])]
    public ?float $float3 = null {
        set(float|string|null $v) {
            $this->float3 = $this->set('float3', (float) $v);
        }
    }

    #[Struct([])]
    public ?string $string1 = null {
        set(string|object|null $v) {
            $this->string1 = $this->set('string1', (string) $v);
        }
    }

    #[Struct([])]
    public ?string $string2 = null {
        set(string|null $v) {
            $this->string2 = $this->set('string2', (string) $v);
        }
    }

    #[Struct([])]
    public ?bool $bool1 = null {
        set(bool|string|null $v) {
            $this->bool1 = $this->set('bool1', (bool) $v);
        }
    }

    #[Struct([])]
    public ?bool $bool2 = null {
        set(bool|string|null $v) {
            $this->bool2 = $this->set('bool2', (bool) $v);
        }
    }

    #[Struct([])]
    public ?bool $bool3 = null {
        set(bool|string|null $v) {
            $this->bool3 = $this->set('bool3', (bool) $v);
        }
    }

    #[Struct([])]
    public ?bool $bool4 = null {
        set(bool|string|null $v) {
            $this->bool4 = $this->set('bool4', (bool) $v);
        }
    }

    #[Struct([])]
    public \stdClass|string|null $obj1 = null {
        get => json_decode($this->get('obj1'));
        set(string|\stdClass|array|null $v) {
            $this->obj1 = $this->set('obj1', json_encode($v, JSON_FORCE_OBJECT));
        }
    }

    #[Struct([])]
    public \stdClass|string|null $obj2 = null {
        get => json_decode($this->get('obj2'));
        set(string|\stdClass|null $v) {
            $this->obj2 = $this->set('obj2', json_encode(json_decode($v, true), JSON_FORCE_OBJECT));
        }
    }

    #[Struct([])]
    public \stdClass|string|null $obj3 = null {
        get => json_decode($this->get('obj3'));
        set(string|\stdClass|null $v) {
            $this->obj3 = $this->set('obj3', json_encode($v));
        }
    }

    #[Struct([])]
    public array|string|null $arr1 = null {
        get => json_decode($this->get('arr1'), true);
        set(string|array|null $v) {
            $this->arr1 = $this->set('arr1', json_encode($v));
        }
    }

    #[Struct([])]
    public array|string|null $arr2 = null {
        get => json_decode($this->get('arr2'), true);
        set(string|array|null $v) {
            $this->arr2 = $this->set('arr2', $v);
        }
    }

    #[Struct([])]
    public array|string|null $json1 = null {
        get => json_decode($this->get('json1'), true);
        set(string|array|null $v) {
            $this->json1 = $this->set('json1', json_encode($v));
        }
    }

    #[Struct([])]
    public array|string|null $json2 = null {
        get => json_decode($this->get('json2'), true);
        set(string|array|null $v) {
            $this->json2 = $this->set('json2', $v);
        }
    }

    #[Struct([])]
    public Collection|string|null $coll1 = null {
        get => new Collection([new DemoEntity(json_decode($this->get('coll1'), true))]);
        set(string|Collection|null $v) {
            $this->coll1 = $this->set('coll1', $v);
        }
    }

    #[Struct([])]
    public array|Collection|string|null $coll2 = null {
        get => new Collection([new DemoEntity(json_decode($this->get('coll2'), true))]);
        set(string|Collection|array|null $v) {
            $this->coll2 = $this->set('coll2', json_encode($v));
        }
    }
}
