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

namespace Tests\Router\Apps\Petstore30\Controllers;

/**
 * Class Web.
 *
 *
 * @author  Xiangmin Liu <635750556@qq.com>
 */
class Web
{
    /**
     * @OA\Get(
     *     path="/web/v1/petLeevelForWeb/{petId:[A-Za-z]+}/",
     *     tags={"pet"},
     *     summary="Just test the router",
     *     operationId="petLeevelForWeb",
     *     @OA\Parameter(
     *         name="petId",
     *         in="path",
     *         description="ID of pet to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     }
     * )
     */
    public function petLeevelForWeb()
    {
    }

    /**
     * @OA\Get(
     *     path="/web/v2/petLeevelV2Web/",
     *     tags={"pet"},
     *     summary="Just test ignore the router",
     *     operationId="petLeevelV2Web",
     *     @OA\Parameter(
     *         name="petId",
     *         in="path",
     *         description="ID of pet to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     leevelIgnore=true
     * )
     */
    public function petLeevelV2ForWeb()
    {
    }

    /**
     * @OA\Get(
     *     path="/web/v1/petLeevelIgnoreForWeb/",
     *     tags={"pet"},
     *     summary="Just test ignore the router",
     *     operationId="petLeevelIgnoreForWeb",
     *     @OA\Parameter(
     *         name="petId",
     *         in="path",
     *         description="ID of pet to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     leevelIgnore=true
     * )
     */
    public function petLeevelIgnoreForWeb()
    {
    }
}
