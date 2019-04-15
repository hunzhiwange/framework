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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Validate;

use InvalidArgumentException;

/**
 * 基础验证规则.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.26
 *
 * @version 1.0
 */
abstract class Rule
{
    /**
     * 字段.
     *
     * @var string
     */
    protected $field;

    /**
     * 待验证数据.
     *
     * @var mixed
     */
    protected $datas;

    /**
     * 参数属性.
     *
     * @var array
     */
    protected $parameter;

    /**
     * 初始化基本参数.
     *
     * @param string $field
     * @param mixed  $datas
     * @param array  $parameter
     */
    protected function initArgs(string $field, $datas, array $parameter): void
    {
        $this->field = $field;
        $this->datas = $datas;
        $this->parameter = $parameter;
    }

    /**
     * 数据是否满足正则条件.
     *
     * @param string $field
     * @param array  $parameter
     * @param int    $limitLength
     */
    protected function checkParameterLength(string $field, array $parameter, int $limitLength): void
    {
        if (count($parameter) < $limitLength) {
            $e = sprintf(
                'The rule %s requires at least %d arguments.', $field,
                $limitLength
            );

            throw new InvalidArgumentException($e);
        }
    }
}
