<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 流程控制复用接口.
 */
interface IFlowControl
{
    /**
     * 条件语句 if.
     */
    public function if(mixed $condition): static;

    /**
     * 条件语句 elif.
     */
    public function elif(mixed $condition): static;

    /**
     * 条件语句 else.
     */
    public function else(): static;

    /**
     * 条件语句 fi.
     */
    public function fi(): static;
}
