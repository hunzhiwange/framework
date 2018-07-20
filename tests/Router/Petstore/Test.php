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

/*
 * @SWG\Get(
 *     path="/test/hello4",
 *     summary="Find pet by ID",
 *     description="Returns a single pet",
 *     operationId="getPetById",
 *     tags={"pet"},
 *     produces={"application/xml", "application/json"},
 *     @SWG\Parameter(
 *         description="ID of pet to return",
 *         in="path",
 *         name="petId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="successful operation",
 *         @SWG\Schema(ref="#/definitions/Pet")
 *     ),
 *     @SWG\Response(
 *         response="400",
 *         description="Invalid ID supplied"
 *     ),
 *     @SWG\Response(
 *         response="404",
 *         description="Pet not found"
 *     ),
 *     security={
 *       {"api_key": {}}
 *     },
 *     _domain="www.queryphp.cn"
 * )
 */

/*
 * @SWG\Get(
 *     path="/45/{id}",
 *     summary="Find pet by ID",
 *     description="Returns a single pet",
 *     operationId="getPetById",
 *     tags={"pet"},
 *     produces={"application/xml", "application/json"},
 *     @SWG\Parameter(
 *         description="ID of pet to return",
 *         in="path",
 *         name="petId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="successful operation",
 *         @SWG\Schema(ref="#/definitions/Pet")
 *     ),
 *     @SWG\Response(
 *         response="400",
 *         description="Invalid ID supplied"
 *     ),
 *     @SWG\Response(
 *         response="404",
 *         description="Pet not found"
 *     ),
 *     security={
 *       {"api_key": {}}
 *     },
 *     _domain="www.queryphp.cn"
 * )
 */
