<?php

declare(strict_types=1);

namespace Tests\Support\Fixtures;

use Leevel\Support\Dto;

class Dto1 extends Dto
{
    public static array $testStatic = [];

    public string $demoStringProp;

    public int $demoIntProp;

    public ?float $demoFloatProp = null;

    public bool $demoTrueProp = true;

    public bool $demoFalseProp = false;

    public DtoProp1 $demoObjectProp;

    public DtoProp2 $demoObject2Prop;

    public Dto2 $demoObject3Prop;

    public mixed $demoMixedProp = true;

    protected string $demoProtectedProp;

    private mixed $demoPrivateProp;
}
