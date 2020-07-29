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
 * Class Pet.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 *
 * @OA\Schema(
 *     zh-CN:description="Pet model",
 *     zh-CN:title="Pet model",
 *     type="object",
 *     required={"name", "photoUrls"},
 *     @OA\Xml(
 *         name="Pet"
 *     )
 * )
 */
class Pet
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
     *     zh-CN:description="Category relation",
     *     zh-CN:title="Category",
     * )
     *
     * @var \Petstore\Category
     */
    private $category;

    /**
     * @OA\Property(
     *     format="int64",
     *     zh-CN:description="Pet name",
     *     zh-CN:title="Pet name",
     * )
     *
     * @var int
     */
    private $name;

    /**
     * @OA\Property(
     *     zh-CN:description="Photo urls",
     *     zh-CN:title="Photo urls",
     *     @OA\Xml(
     *         name="photoUrl",
     *         wrapped=true
     *     ),
     *     @OA\Items(
     *         type="string",
     *         default="images/image-1.png"
     *     )
     * )
     *
     * @var array
     */
    private $photoUrls;

    /**
     * @OA\Property(
     *     zh-CN:description="Pet tags",
     *     zh-CN:title="Pet tags",
     *     @OA\Xml(
     *         name="tag",
     *         wrapped=true
     *     ),
     * )
     *
     * @var \Petstore\Tag[]
     */
    private $tags;
}
