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

namespace Tests\Router\Petstore;

class Pet
{
    /**
     * @SWGwww\Get(
     *     path="/pet/findByTags",
     *     summary="Finds Pets by tags",
     *     tags={"pet","ttt"},
     *     description="Muliple tags can be provided with comma separated strings. Use tag1, tag2, tag3 for testing.",
     *     operationId="findPetsByTags",
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="tags",
     *         in="query",
     *         description="Tags to filter by",
     *         required=true,
     *         type="array",
     *         @SWG\Items(type="string"),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid tag value",
     *     ),
     *     security={
     *         {
     *             "petstore_auth": {"write:pets", "read:pets"}
     *         }
     *     },
     *     deprecated=true
     * )
     */
    public function findByTags()
    {
    }

    /**
     * @SWG\Get(
     *     path="/pet/findByStatus",
     *     summary="Finds Pets by status",
     *     description="Multiple status values can be provided with comma separated strings",
     *     operationId="findPetsByStatus",
     *     produces={"application/xml", "application/json"},
     *     tags={"pet"},
     *     @SWG\Parameter(
     *         name="status",
     *         in="query",
     *         description="Status values that need to be considered for filter",
     *         required=true,
     *         type="array",
     *         @SWG\Items(
     *             type="string",
     *             enum={"available", "pending", "sold"},
     *             default="available"
     *         ),
     *         collectionFormat="multi"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid status value",
     *     ),
     *     security={
     *       {"petstore_auth": {"write:pets", "read:pets"}}
     *     },
     *     __domain="",
     *     __prefix="",
     *     __params={"args1": "hello", "args2": "world"},
     *     __where={"hello": "[0-9]+", "world": "[A-Za-z]+"},
     *     __strict=true,
     *     __b2ind=":group/blog/list?arg1=1&arg2=2"
     * )
     */
    public function findByStatus()
    {
        echo 'test ';
    }

    /**
     * @SWG\Get(
     *     path="/pet/{petId}",
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
     *     }
     * )
     */
    public function getPetById()
    {
    }

    /**
     * @SWG\Post(
     *     path="/pet",
     *     tags={"pet"},
     *     operationId="addPet",
     *     summary="Add a new pet to the store",
     *     description="",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Pet object that needs to be added to the store",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Invalid input",
     *     ),
     *     security={{"petstore_auth":{"write:pets", "read:pets"}}}
     * )
     */
    public function addPet()
    {
    }

    /**
     * @SWG\Put(
     *     path="/pet",
     *     tags={"pet"},
     *     operationId="updatePet",
     *     summary="Update an existing pet",
     *     description="",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Pet object that needs to be added to the store",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid ID supplied",
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Pet not found",
     *     ),
     *     @SWG\Response(
     *         response=405,
     *         description="Validation exception",
     *     ),
     *     security={{"petstore_auth":{"write:pets", "read:pets"}}}
     * )
     */
    public function updatePet()
    {
    }

    /**
     * @SWG\Delete(
     *     path="/pet/{petId}",
     *     summary="Deletes a pet",
     *     description="",
     *     operationId="deletePet",
     *     produces={"application/xml", "application/json"},
     *     tags={"pet"},
     *     @SWG\Parameter(
     *         description="Pet id to delete",
     *         in="path",
     *         name="petId",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Parameter(
     *         name="api_key",
     *         in="header",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid ID supplied"
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Pet not found"
     *     ),
     *     security={{"petstore_auth":{"write:pets", "read:pets"}}}
     * )
     */
    public function deletePet()
    {
    }

    /**
     * @SWG\Post(
     *   path="/pet/{petId}",
     *   tags={"pet"},
     *   summary="Updates a pet in the store with form data",
     *   description="",
     *   operationId="updatePetWithForm",
     *   consumes={"application/x-www-form-urlencoded"},
     *   produces={"application/xml", "application/json"},
     *   @SWG\Parameter(
     *     name="petId",
     *     in="path",
     *     description="ID of pet that needs to be updated",
     *     required=true,
     *     type="integer",
     *     format="int64"
     *   ),
     *   @SWG\Parameter(
     *     name="name",
     *     in="formData",
     *     description="Updated name of the pet",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="status",
     *     in="formData",
     *     description="Updated status of the pet",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response="405",description="Invalid input"),
     *   security={{
     *     "petstore_auth": {"write:pets", "read:pets"}
     *   }}
     * )
     */
    public function updatePetWithForm()
    {
    }

    /**
     * @SWG\Post(
     *     path="/pet/{petId}/uploadImage",
     *     consumes={"multipart/form-data"},
     *     description="",
     *     operationId="uploadFile",
     *     @SWG\Parameter(
     *         description="Additional data to pass to server",
     *         in="formData",
     *         name="additionalMetadata",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="file to upload",
     *         in="formData",
     *         name="file",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Parameter(
     *         description="ID of pet to update",
     *         format="int64",
     *         in="path",
     *         name="petId",
     *         required=true,
     *         type="integer"
     *     ),
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response="200",
     *         description="successful operation",
     *     ),
     *     security={
     *         {
     *             "petstore_auth": {
     *                  "read:pets",
     *                  "write:pets"
     *             }
     *         }
     *     },
     *     summary="uploads an image",
     *     tags={
     *         "pet"
     *     }
     * )
     * */
    public function uploadFile()
    {
    }
}
