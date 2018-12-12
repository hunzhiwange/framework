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

namespace Tests\Router\Apps\AppForAnnotation\Controllers;

use Leevel\Http\IRequest;

class Domain
{
    /**
     * @OA\Get(
     *     path="/domain/test",
     *     tags={"pet"},
     *     summary="Just test the router",
     *     operationId="petLeevel",
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
     *     requestBody={"$ref": "#/components/requestBodies/Pet"},
     *     leevelDomain="queryphp.com"
     * )
     */
    public function fooNotMatchedDomain()
    {
    }

    /**
     * @OA\Get(
     *     path="/domain/test2",
     *     tags={"pet"},
     *     summary="Just test the router",
     *     operationId="petLeevel",
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
     *     requestBody={"$ref": "#/components/requestBodies/Pet"},
     *     leevelDomain="queryphp.com"
     * )
     */
    public function barMatchedDomain()
    {
        return 'barMatchedDomain';
    }

    /**
     * @OA\Get(
     *     path="/domain/test3",
     *     tags={"pet"},
     *     summary="Just test the router",
     *     operationId="petLeevel",
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
     *     requestBody={"$ref": "#/components/requestBodies/Pet"},
     *     leevelDomain="{subdomain:[A-Za-z]+}-vip.{domain}.queryphp.com"
     * )
     */
    public function barMatchedDomainWithVar(IRequest $request)
    {
        return 'barMatchedDomainWithVar and params are '.
            json_encode($request->params->all());
    }

    /**
     * @OA\Get(
     *     path="/domain/test4",
     *     tags={"pet"},
     *     summary="Just test the router",
     *     operationId="petLeevel",
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
     *     requestBody={"$ref": "#/components/requestBodies/Pet"},
     *     leevelDomain="api"
     * )
     */
    public function barMatchedDomainWithoutExtend()
    {
        return 'barMatchedDomainWithoutExtend';
    }
}
