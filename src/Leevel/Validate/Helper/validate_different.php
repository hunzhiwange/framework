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

namespace Leevel\Validate\Helper;

use InvalidArgumentException;
use Leevel\Validate\IValidator;

/**
 * 两个字段是否不同.
 *
 * @param mixed                       $value
 * @param array                       $parameter
 * @param \Leevel\Validate\IValidator $validator
 *
 * @return bool
 */
function validate_different($value, array $parameter, IValidator $validator): bool
{
    if (!array_key_exists(0, $parameter)) {
        throw new InvalidArgumentException('Missing the first element of parameter.');
    }

    return $value !== $validator->getFieldValue($parameter[0]);
}

class validate_different
{
}
