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

namespace Leevel\Stack;

use InvalidArgumentException;

/**
 * 栈.
 *
 * - 后进先出
 *
 * @see http://php.net/manual/zh/class.splstack.php
 */
class Stack extends LinkedList
{
    /**
     * 入栈.
     *
     * @param mixed $value
     */
    public function push(mixed $value): void
    {
        parent::push($value);
    }

    /**
     * 出栈.
     */
    public function pop(): mixed
    {
        return parent::pop();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function validate($value): void
    {
        if (!$this->checkType($value)) {
            $e = sprintf(
                'The stack element type verification failed, and the allowed type is %s.',
                implode(',', $this->type)
            );

            throw new InvalidArgumentException($e);
        }
    }
}
