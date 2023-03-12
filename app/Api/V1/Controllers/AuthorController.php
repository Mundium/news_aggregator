<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class AuthorController extends Controller
{
    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllAuthors()
    {
        $api = new Api;
        $authors = $api->getAllAuthors();

        $data = ['authors' => $authors];
        return ResponseBuilder::asSuccess()->withData($data)->build();
    }
}
