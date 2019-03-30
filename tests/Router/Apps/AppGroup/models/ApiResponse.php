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

namespace Petstore30;

/**
 * Class ApiResponse.
 *
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OA\Schema(
 *     type="object",
 *     description="Api response",
 *     title="Api response"
 * )
 */
class ApiResponse
{
    /**
     * @OA\Property(
     *     description="Code",
     *     title="Code",
     *     format="int32"
     * )
     *
     * @var int
     */
    private $code;

    /**
     * OAS\Property(
     *    description="Type",
     *    title="Type",
     * ).
     *
     * @var string
     */
    private $type;

    /**
     * @OA\Property(
     *     description="Message",
     *     title="Message"
     * )
     *
     * @var string
     */
    private $message;
}
