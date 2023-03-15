<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class AuthorController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/authors",
     *      tags={"Author"},
     *      security={{"bearerAuth":{}}},
     *      summary="Get all authors from third party api",
     *      description="Returns authors",
     *      @OA\Response(
     *          response=200,
     *          description="ok",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden",
     *      ),
     * @OA\Response(
     *      response=400,
     *      description="Bad Request",
     *   ),
     * @OA\Response(
     *      response=404,
     *      description="not found",
     *   ),
     *  )
     *
     */
    public function getAllAuthors()
    {
        $api = new Api;
        $authors = $api->getAllAuthors();

        $data = ['authors' => $authors];
        return ResponseBuilder::asSuccess()->withData($data)->build();
    }
}
