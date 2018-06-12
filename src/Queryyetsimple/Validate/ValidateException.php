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

namespace Leevel\Validate;

use Exception;

/**
 * 验证异常.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.08.25
 *
 * @version 1.0
 */
class ValidateException extends Exception
{
    /**
     * 验证器.
     *
     * @var \Leevel\Validate\IValidate
     */
    public $objValidate;

    /**
     * 响应组件.
     *
     * @var null|\Leevel\Http\Response
     */
    public $objResponse;

    /**
     * 构造函数.
     *
     * @param \Leevel\Validate\IValidate $objValidate
     * @param \Leevel\Http\Response      $objResponse
     */
    public function __construct($objValidate, $objResponse = null)
    {
        parent::__construct('Validate failed');

        $this->objResponse = $objResponse;
        $this->objValidate = $objValidate;
    }

    /**
     * 响应实例.
     *
     * @return \Leevel\Http\Response
     */
    public function getResponse()
    {
        return $this->objResponse;
    }

    /**
     * 返回验证器.
     *
     * @return \Leevel\Validate\IValidate
     */
    public function getValidate()
    {
        return $this->objValidate;
    }
}
