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

namespace Leevel\Validate;

use Exception;
use Leevel\Http\Response;

/**
 * 验证异常.
 */
class ValidateException extends Exception
{
    /**
     * 验证器.
     *
     * @var \Leevel\Validate\IValidator
     */
    public $validator;

    /**
     * 响应组件.
     *
     * @var \Leevel\Http\Response
     */
    public $response;

    /**
     * 构造函数.
     *
     * @param \Leevel\Validate\IValidator $validator
     */
    public function __construct(IValidator $validator, ?Response $response = null)
    {
        parent::__construct('Validate failed.');

        $this->response = $response;
        $this->validator = $validator;
    }

    /**
     * 响应实例.
     *
     * @return \Leevel\Http\Response
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * 返回验证器.
     *
     * @return \Leevel\Validate\IValidator
     */
    public function getValidator(): IValidator
    {
        return $this->validator;
    }
}
