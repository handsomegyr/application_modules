<?php

namespace App\Campaign\Controllers;

use App\Sign\Models\Log;

/**
 * Swagger2事例
 * @author Administrator
 *        
 */

/**
 * @SWG\Swagger(
 *     basePath="/",
 *     host="www.applicationmodule.com",
 *     schemes={"http","https"},
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Swagger2事例",
 *         description="Swagger2事例",
 *         @SWG\Contact(name="GYR2 company"),
 *         @SWG\License(name="MIT")
 *     ),
 *     @SWG\Definition(
 *         definition="Pet",
 *         required={"name", "photoUrls"}, 
 *         type="object",
 *         @SWG\Xml(name="Pet"),
 *		   @SWG\Property(property="id",type="string",format="int64"),
 *         @SWG\Property(property="name",type="string",example="doggie"),
 *         @SWG\Property(property="category",type="string"),
 *         @SWG\Property(property="photoUrls",type="string[]",@SWG\Xml(name="photoUrl",wrapped=true)),
 *         @SWG\Property(property="category",type="string",enum={"available", "pending", "sold"})
 *     ),
 *     @SWG\Definition(
 *         definition="ErrorResponseModel",
 *         type="object",
 *         required={"success", "error_code", "error_msg"},
 *		   @SWG\Property(
 *             property="success",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="error_code",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="error_msg",
 *             type="string"
 *         )
 *     ),
 *     @SWG\Definition(
 *         definition="SuccessResponseModel",
 *         type="object",
 *         required={"success", "message", "result"},
 *		   @SWG\Property(
 *             property="success",
 *             type="integer",
 *             format="int32"
 *         ),
 *         @SWG\Property(
 *             property="message",
 *             type="string"
 *         ),
 *         @SWG\Property(
 *             property="result",
 *             type="object"
 *         )
 *     )
 * )
 */
class Swagger2Controller extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();
        $this->view->disable();
    }


    /**
     * @SWG\Get(
     *     path="/campaign/swagger2/findbytags",
     *     summary="Finds by tags",
     *     tags={"Swagger2"},
     *     description="Muliple tags can be provided with comma separated strings. Use tag1, tag2, tag3 for testing.",
     *     operationId="findByTags",
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
     *         @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *     )
     * )
     */
    public function findbytagsAction()
    {
        $tags = $this->get("tags", '');
        if (empty($tags)) {
            echo $this->error(-1, 'tags is empty');
            return false;
        }
        echo $this->result("OK", array('tags' => $tags));
        return true;
    }

    /**
     * @SWG\Get(
     *     path="/campaign/swagger2/findbystatus",
     *     summary="Finds by status",
     *     description="Multiple status values can be provided with comma separated strings",
     *     operationId="findByStatus",
     *     produces={"application/xml", "application/json"},
     *     tags={"Swagger2"},
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
     *         @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *     )
     * )
     */
    public function findbystatusAction()
    {
        $status = $this->get("status", '');
        if (empty($status)) {
            echo $this->error(-1, 'status is empty');
            return false;
        }
        echo $this->result("OK", array('status' => $status));
        return true;
    }

    /**
     * @SWG\Get(
     *     path="/campaign/swagger2/getbyid",
     *     summary="Find by ID",
     *     description="Returns a single pet",
     *     operationId="getById",
     *     tags={"Swagger2"},
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
     *         @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *     )
     * )
     */
    public function getpetbyidAction()
    {
        $petId = $this->get("petId", '');
        if (empty($petId)) {
            echo $this->error(-1, 'petId is empty');
            return false;
        }
        echo $this->result("OK", array('petId' => $petId));
        return true;
    }

    /**
     * @SWG\Post(
     *     path="/campaign/swagger2/add",
     *     tags={"Swagger2"},
     *     operationId="add",
     *     summary="Add a new pet to the store",
     *     description="",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Pet object that needs to be added to the store",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Pet"),
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *     )
     * )
     */
    public function addAction()
    {
        $body = $this->get("body", '');
        if (empty($body)) {
            echo $this->error(-1, 'body is empty');
            return false;
        }
        echo $this->result("OK", array('body' => $body));
        return true;
    }

    /**
     * @SWG\Post(
     *     path="/campaign/swagger2/update",
     *     tags={"Swagger2"},
     *     operationId="update",
     *     summary="Update an existing pet",
     *     description="",
     *     consumes={"application/json", "application/xml"},
     *     produces={"application/xml", "application/json"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Pet object that needs to be added to the store",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Pet"),
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *     )
     * )
     */
    public function updateAction()
    {
        $body = $this->get("body", '');
        if (empty($body)) {
            echo $this->error(-1, 'body is empty');
            return false;
        }
        echo $this->result("OK", array('tags' => $tags));
        return true;
    }

    /**
     * @SWG\Post(
     *     path="/campaign/swagger2/delete",
     *     summary="Deletes a pet",
     *     description="",
     *     operationId="deletePet",
     *     produces={"application/xml", "application/json"},
     *     tags={"Swagger2"},
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
     *         @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *     )
     * )
     */
    public function deleteAction()
    {
        $petId = $this->get("petId", '');
        if (empty($petId)) {
            echo $this->error(-1, 'petId is empty');
            return false;
        }
        echo $this->result("OK", array('petId' => $petId));
        return true;
    }

    /**
     * @SWG\Post(
     *   path="/campaign/swagger2/updatewithform",
     *   tags={"Swagger2"},
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
     *   @SWG\Response(
     *      @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *   ),
     *   @SWG\Response(
     *      @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *   )
     * )
     */
    public function updatewithformAction()
    {
        $petId = $this->get("petId", '');
        if (empty($petId)) {
            echo $this->error(-1, 'petId is empty');
            return false;
        }
        echo $this->result("OK", array('petId' => $petId));
        return true;
    }

    /**
     * @SWG\Post(
     *     path="/campaign/swagger2/uploadfile",
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
     *         @SWG\Schema(ref="#/definitions/SuccessResponseModel")
     *     ),
     *     @SWG\Response(
     *         @SWG\Schema(ref="#/definitions/ErrorResponseModel")
     *     )
     *     summary="uploads an image",
     *     tags={
     *         "pet"
     *     }
     * )
     * */
    public function uploadfileAction()
    {
        if (empty($_FILES['file'])) {
            echo $this->error(-1, 'file is empty');
            return false;
        }
        echo $this->result("OK", array('file' => $_FILES['file']));
        return true;
    }
}
