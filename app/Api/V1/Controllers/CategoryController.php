<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @OA\Get(
     *      path="/api/categories",
     *      tags={"Category"},
     *      security={{"bearerAuth":{}}},
     *      summary="Get all gatecories",
     *      description="Returns categories",
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
    public function getAllCategories(Request $request)
    {
        $categories = $this->categoryRepository->getCategories();
        $data = ['categories' => $categories];

        return ResponseBuilder::asSuccess()->withData($data)->build();
    }
}
