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

namespace Leevel\Flow;

/**
 * 流程控制复用.
 *
 * - 灵感来源于做禅道二次开发，里面有这样的用法
 */
trait FlowControl
{
    /**
     * 逻辑代码是否处于条件表达式中.
     *
     * @var bool
     */
    protected bool $inFlowControl = false;

    /**
     * 条件表达式是否为真.
     *
     * @var bool
     */
    protected bool $isFlowControlTrue = false;

    /**
     * 条件语句 if.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function if($value = false): self
    {
        return $this->setFlowControl(true, $value ? true : false);
    }

    /**
     * 条件语句 elif.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function elif($value = false): self
    {
        return $this->setFlowControl(true, $value ? true : false);
    }

    /**
     * 条件语句 else.
     *
     * @return $this
     */
    public function else(): self
    {
        return $this->setFlowControl(true, !$this->isFlowControlTrue);
    }

    /**
     * 条件语句 fi.
     *
     * @return $this
     */
    public function fi(): self
    {
        return $this->setFlowControl(false, false);
    }

    /**
     * 设置当前条件表达式状态.
     *
     * @return $this
     */
    public function setFlowControl(bool $inFlowControl, bool $isFlowControlTrue): self
    {
        $this->inFlowControl = $inFlowControl;
        $this->isFlowControlTrue = $isFlowControlTrue;

        return $this;
    }

    /**
     * 验证一下条件表达式是否通过.
     */
    public function checkFlowControl(): bool
    {
        return $this->inFlowControl && !$this->isFlowControlTrue;
    }
}
