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

namespace Petstore30;

/**
 * Class Order.
 *
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 * @OA\Schema(
 *     title="Order model",
 *     description="Order model",
 * )
 */
class Order
{
    /**
     * @OA\Property(
     *     format="int64",
     *     title="ID",
     *     default=1,
     *     description="ID",
     * )
     *
     * @var int
     */
    private $id;

    /**
     * @OA\Property(
     *     default=1,
     *     format="int64",
     *     description="Pet ID",
     *     title="Pet ID",
     * )
     *
     * @var int
     */
    private $petId;

    /**
     * @OA\Property(
     *     default=12,
     *     format="in32",
     *     description="Quantity",
     *     title="Quantity",
     * )
     *
     * @var int
     */
    private $quantity;

    /**
     * @OA\Property(
     *     default="2017-02-02 18:31:45",
     *     format="datetime",
     *     description="Shipping date",
     *     title="Shipping date",
     *     title="Pet ID",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $shipDate;

    /**
     * @OA\Property(
     *     default="placed",
     *     title="Order status",
     *     description="Order status",
     *     enum={"placed", "approved", "delivered"},
     *     title="Pet ID",
     * )
     *
     * @var string
     */
    private $status;

    /**
     * @OA\Property(
     *     default="false",
     *     format="int64",
     *     description="Complete status",
     *     title="Complete status",
     * )
     *
     * @var bool
     */
    private $complete;
}
