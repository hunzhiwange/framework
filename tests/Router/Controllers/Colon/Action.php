<?php

declare(strict_types=1);

namespace Tests\Router\Controllers\Colon;

class Action
{
    public function fooBar(): string
    {
        return 'hello colon with action and action is not single class';
    }

    public function moreFooBar(): string
    {
        return 'hello colon with action and action is not single class with more than one';
    }

    public function beforeButFirst(): string
    {
        return 'hello colon with action and action is not single class before but first';
    }
}
