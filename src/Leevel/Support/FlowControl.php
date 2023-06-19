<?php

declare(strict_types=1);

namespace Leevel\Support;

/**
 * 流程控制复用.
 *
 * - 灵感来源于做禅道二次开发，里面有这样的用法
 */
trait FlowControl
{
    /**
     * 条件表达式级别.
     */
    protected int $flowControlLevel = 0;

    /**
     * 普通条件表达式是否为真.
     */
    protected array $lastFlowControlMatched = [];

    /**
     * Else条件表达式是否为真.
     */
    protected array $elseFlowControlMatched = [];

    /**
     * 条件语句 if.
     */
    public function if(mixed $condition): static
    {
        $parentLevel = $this->flowControlLevel;
        ++$this->flowControlLevel;

        if (isset($this->lastFlowControlMatched[$parentLevel])) {
            $parentLastFlowControlMatched = $this->lastFlowControlMatched[$parentLevel] ?? false;
            if ($parentLastFlowControlMatched) {
                $this->elseFlowControlMatched[$this->flowControlLevel] = true;
                $this->lastFlowControlMatched[$this->flowControlLevel] = (bool) $condition;
                if ($this->lastFlowControlMatched[$this->flowControlLevel]) {
                    $this->elseFlowControlMatched[$this->flowControlLevel] = false;
                }
            } else {
                $this->lastFlowControlMatched[$this->flowControlLevel] = false;
                $this->elseFlowControlMatched[$this->flowControlLevel] = false;
            }
        } else {
            $this->lastFlowControlMatched[$this->flowControlLevel] = false;
            $this->elseFlowControlMatched[$this->flowControlLevel] = true;

            $this->lastFlowControlMatched[$this->flowControlLevel] = (bool) $condition;
            if ($this->lastFlowControlMatched[$this->flowControlLevel]) {
                $this->elseFlowControlMatched[$this->flowControlLevel] = false;
            }
        }

        return $this;
    }

    /**
     * 条件语句 elif.
     */
    public function elif(mixed $condition): static
    {
        if (!$this->lastFlowControlMatched[$this->flowControlLevel] && $condition) {
            $this->lastFlowControlMatched[$this->flowControlLevel] = true;
            $this->elseFlowControlMatched[$this->flowControlLevel] = false;
        } else {
            $this->lastFlowControlMatched[$this->flowControlLevel] = false;
        }

        return $this;
    }

    /**
     * 条件语句 else.
     */
    public function else(): static
    {
        $this->lastFlowControlMatched[$this->flowControlLevel] = $this->elseFlowControlMatched[$this->flowControlLevel];

        return $this;
    }

    /**
     * 条件语句 fi.
     */
    public function fi(): static
    {
        if (isset($this->lastFlowControlMatched[$this->flowControlLevel])) {
            unset($this->lastFlowControlMatched[$this->flowControlLevel]);
        }

        if (isset($this->elseFlowControlMatched[$this->flowControlLevel])) {
            unset($this->elseFlowControlMatched[$this->flowControlLevel]);
        }

        --$this->flowControlLevel;

        return $this;
    }

    /**
     * 验证一下条件表达式是否通过.
     */
    protected function checkFlowControl(): bool
    {
        return $this->lastFlowControlMatched[$this->flowControlLevel] ?? false;
    }
}
