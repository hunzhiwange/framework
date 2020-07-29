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
 * Class User.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OA\Schema(
 *     zh-CN:title="User model",
 *     zh-CN:description="User model",
 *     type="object"
 * )
 */
class User
{
    /**
     * @OA\Property(
     *     format="int64",
     *     zh-CN:description="ID",
     *     zh-CN:title="ID",
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @OA\Property(
     *     zh-CN:description="Username",
     *     zh-CN:title="Username",
     * )
     *
     * @var string
     */
    private $username;

    /**
     * @OA\Property(
     *     zh-CN:description="First name",
     *     zh-CN:title="First name",
     * )
     *
     * @var string
     */
    private $firstName;

    /**
     * @OA\Property(
     *     zh-CN:description="Last name",
     *     zh-CN:title="Last name",
     * )
     *
     * @var string
     */
    private $lastName;

    /**
     * @OA\Property(
     *     format="email",
     *     zh-CN:description="Email",
     *     zh-CN:title="Email",
     * )
     *
     * @var string
     */
    private $email;

    /**
     * @OA\Property(
     *     format="int64",
     *     zh-CN:description="Password",
     *     zh-CN:title="Password",
     *     maximum=255
     * )
     *
     * @var string
     */
    private $password;

    /**
     * @OA\Property(
     *     format="msisdn",
     *     zh-CN:description="Phone",
     *     zh-CN:title="Phone",
     * )
     *
     * @var string
     */
    private $phone;

    /**
     * @OA\Property(
     *     format="int32",
     *     zh-CN:description="User status",
     *     zh-CN:title="User status",
     * )
     *
     * @var int
     */
    private $userStatus;
}
