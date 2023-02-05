<?php

declare(strict_types=1);

namespace Tests\Di\Fixtures;

class Test8 implements ITest8
{
    public function func1()
    {
        return \func_get_args();
    }

    public function func2($arg1 = 'hello')
    {
        return \func_get_args();
    }

    public static function staticFunc3()
    {
        return \func_get_args();
    }

    public function handle()
    {
        return ['call handle'];
    }
}
