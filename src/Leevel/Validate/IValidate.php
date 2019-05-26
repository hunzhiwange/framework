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

/**
 * 验证工厂接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.26
 *
 * @version 1.0
 *
 * @see \Leevel\Validate\Proxy\IValidate 请保持接口设计的一
 */
interface IValidate
{
    /**
     * 创建一个验证器.
     *
     * @param array $data
     * @param array $rules
     * @param array $names
     * @param array $messages
     *
     * @return \Leevel\Validate\IValidator
     */
    public function make(array $data = [], array $rules = [], array $names = [], array $messages = []): IValidator;

    /**
     * 初始化默认验证消息.
     */
    public static function initMessages(): void;
}
