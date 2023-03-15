<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

/**
 * @OA\Get(
 *      path="/api/sources",
 *      tags={"Source"},
 *      security={{"bearerAuth":{}}},
 *      summary="Get all sources from third party api",
 *      description="Returns sources",
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
class SourceController extends Controller
{
    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllSources()
    {
        $api = new Api;
        $sources = $api->getAllSources();

        $data = ['sources' => $sources];
        return ResponseBuilder::asSuccess()->withData($data)->build();
    }
}
