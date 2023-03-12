<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class ApiController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $keyword = null;
        $date = null;
        $category = null;
        $source = null;
        $author = null;
        if ($request->has('keyword') && $request->filled('keyword')) {
            $keyword = $request->keyword;
        }

        if ($request->has('author') && $request->filled('author')) {
            $author = $request->author;
        }

        if ($request->has('date') && $request->filled('date')) {
            $date = $request->date;
        }

        if ($request->has('category') && $request->filled('category')) {
            $category = $request->category;
        }

        if ($request->has('source') && $request->filled('source')) {
            $source = $request->source;
        }

        $apiModel = new Api();
        // $response = $apiModel->fetchNewsFromNytimes($keyword, $date, $category, $author);
        // $response = $apiModel->fetchNewsFromGuardian($keyword, $date, $category, $author);
        $response = $apiModel->fetchNewsFromNewsApi($keyword, $date, $category, $author, $source);

        $data = ['articles' => $response];
        return ResponseBuilder::asSuccess()->withData($data)->build();
    }
}
