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

/**
 * @OA\Info(
 *     description="This is a sample Petstore server.  You can find
 * out more about Swagger at
 * [http://swagger.io](http://swagger.io) or on
 * [irc.freenode.net, #swagger](http://swagger.io/irc/).",
 *     version="1.0.0",
 *     title="Swagger Petstore",
 *     termsOfService="http://swagger.io/terms/",
 *     @OA\Contact(
 *         email="apiteam@swagger.io"
 *     ),
 *     @OA\License(
 *         name="Apache 2.0",
 *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *     )
 * )
 */
class Foobar
{
}

/**
 * @OA\Tag(
 *     name="pet",
 *     leevelGroup="pet",
 *     description="Everything about your Pets",
 *     @OA\ExternalDocumentation(
 *         description="Find out more",
 *         url="http://swagger.io"
 *     )
 * )
 * @OA\Tag(
 *     name="store",
 *     leevelGroup="store",
 *     description="Access to Petstore orders",
 * )
 * @OA\Tag(
 *     name="user",
 *     leevelGroup="user",
 *     description="Operations about user",
 *     @OA\ExternalDocumentation(
 *         description="Find out more about store",
 *         url="http://swagger.io"
 *     )
 * )
 * @OA\Server(
 *     description="SwaggerHUB API Mocking",
 *     url="https://virtserver.swaggerhub.com/swagger/Petstore/1.0.0"
 * )
 * @OA\ExternalDocumentation(
 *     description="Find out more about Swagger",
 *     url="http://swagger.io",
 *     leevels={
 *         "/api/v1": {
 *             "middlewares": "group1",
 *             "group": true
 *         },
 *         "/api/v2": {
 *             "middlewares": "group2",
 *             "group": true
 *         },
 *         "/api/v3": {
 *             "middlewares": "demo1,demo3:30,world",
 *             "group": true
 *         },
 *         "/api/v3": {
 *             "middlewares": {"demo1", "group3"},
 *             "group": true
 *         },
 *         "/api/v4": {
 *             "middlewares": "notFound",
 *             "group": true
 *         },
 *     }
 * )
 */
class Foobar
{
}
