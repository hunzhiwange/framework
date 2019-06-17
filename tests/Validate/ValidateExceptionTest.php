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

namespace Tests\Validate;

use Leevel\Http\IResponse;
use Leevel\Validate\IValidator;
use Leevel\Validate\ValidateException;
use Leevel\Validate\Validator;
use Tests\TestCase;

/**
 * validateException test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.13
 *
 * @version 1.0
 */
class ValidateExceptionTest extends TestCase
{
    public function testData(): void
    {
        $exception = new ValidateException(new Validator(), $this->createMock(IResponse::class));

        $this->assertInstanceof(IValidator::class, $exception->getValidator());
        $this->assertInstanceof(IResponse::class, $exception->getResponse());
    }
}
