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

    public function getAllCategories(Request $request)
    {
        $categories = $this->categoryRepository->getCategories();
        $data = ['categories' => $categories];

        return ResponseBuilder::asSuccess()->withData($data)->build();
    }
}
