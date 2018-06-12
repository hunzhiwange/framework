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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Stack;

use InvalidArgumentException;

/**
 * 栈，后进先出.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.21
 * @see http://php.net/manual/zh/class.splstack.php
 *
 * @version 1.0
 */
class Stack extends LinkedList implements IStackQueue
{
    /**
     * 入栈.
     *
     * @param mixed $value
     */
    public function in($value)
    {
        $this->push($value);
    }

    /**
     * 出栈.
     *
     * @return mixed
     */
    public function out()
    {
        return $this->pop();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value)
    {
        if (! $this->checkType($value)) {
            throw new InvalidArgumentException(
                sprintf('The stack element type verification failed, and the allowed type is %s.',
                    implode(',', $this->type)
                )
            );
        }
    }
}
