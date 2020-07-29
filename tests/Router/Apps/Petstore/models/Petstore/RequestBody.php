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

namespace Petstore;

/**
 * @OA\RequestBody(
 *     request="Pet",
 *     zh-CN:description="Pet object that needs to be added to the store",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             ref="#/components/schemas/Pet"
 *         )
 *     ),
 *     @OA\MediaType(
 *         mediaType="application/xml",
 *         @OA\Schema(
 *             ref="#/components/schemas/Pet"
 *         )
 *     )
 * )
 */
class _
{
}

/**
 * @OA\RequestBody(
 *     request="UserArray",
 *     zh-CN:description="List of user object",
 *     required=true,
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             type="array",
 *             @OA\Items(
 *                 ref="#/components/schemas/User"
 *             )
 *         )
 *     )
 * )
 */
class _
{
}
