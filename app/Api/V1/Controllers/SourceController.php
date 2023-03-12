<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

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
