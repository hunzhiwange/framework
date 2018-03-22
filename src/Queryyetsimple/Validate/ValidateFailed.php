<?php
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
namespace Queryyetsimple\Validate;

use Exception;

/**
 * 验证异常
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.25
 * @version 1.0
 */
class ValidateFailed extends Exception
{

    /**
     * 验证器
     *
     * @var \Queryyetsimple\Validate\IValidate
     */
    public $objValidate;

    /**
     * 响应组件
     *
     * @var \Queryyetsimple\Http\Response|null
     */
    public $objResponse;

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Validate\IValidate $objValidate
     * @param \Queryyetsimple\Http\Response $objResponse
     * @return void
     */
    public function __construct($objValidate, $objResponse = null)
    {
        parent::__construct('Validate failed');

        $this->objResponse = $objResponse;
        $this->objValidate = $objValidate;
    }

    /**
     * 响应实例
     *
     * @return \Queryyetsimple\Http\Response
     */
    public function getResponse()
    {
        return $this->objResponse;
    }

    /**
     * 返回验证器
     *
     * @return \Queryyetsimple\Validate\IValidate
     */
    public function getValidate()
    {
        return $this->objValidate;
    }
}
