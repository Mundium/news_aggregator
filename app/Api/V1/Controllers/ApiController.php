<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

        if ($request->has('date') && $request->filled('date')) {
            $date = $request->date;
        }

        $apiModel = new Api();

        $response_ny_time = $apiModel->fetchNewsFromNytimes($keyword, $date);
        $response_ny_time = $this->standardizeNYTimesResponse($response_ny_time);

        $response_guardian = $apiModel->fetchNewsFromGuardian($keyword, $date);
        $response_guardian = $this->standardizeGuardianResponse($response_guardian);

        $response_news_api = $apiModel->fetchNewsFromNewsApi($keyword, $date);
        $response_news_api = $this->standardizeNewsApiResponse($response_news_api);

        $response = array_merge($response_ny_time, $response_guardian, $response_news_api);

        $response = collect($response);
        if ($request->has('author') && $request->filled('author')) {
            $author = $request->author;
            $response = $response->where('author', $author);
        }

        if ($request->has('category') && $request->filled('category')) {
            $category = $request->category;
            $response = $response->where('category', $category);
        }

        if ($request->has('source') && $request->filled('source')) {
            $source = $request->source;
            $response = $response->where('source', $source);
        }

        $data = ['articles' => $response->values()->toArray()];
        return ResponseBuilder::asSuccess()->withData($data)->build();
    }

    /**
     * update the resource and return it into an array.
     *
     * @return array
     */
    public function standardizeNYTimesResponse($response)
    {
        $responseCollection = collect($response);
        $updatedResponseCollection = $responseCollection->map(function ($data) {
            return [
                'id' => $data['_id'],
                'category' => $data['section_name'] ?? null,
                'source' => $data['source'] ?? null,
                'author' => substr($data['byline']['original'], 3) ?? null,
                'date' => $data['pub_date'] ?? null,
                'description' => $data['snippet'] ?? null,
                'url' => $data['web_url'] ?? null,
                'image' => null,
                'title' => $data['headline']['main'] ?? null,
            ];
        });
        return $updatedResponseCollection->toArray();
    }

    /**
     * update the resource and return it into an array.
     *
     * @return array
     */
    public function standardizeGuardianResponse($response)
    {
        $responseCollection = collect($response);
        $updatedResponseCollection = $responseCollection->map(function ($data) {
            return [
                'id' => $data['id'],
                'category' => $data['sectionName'] ?? null,
                'source' => 'The guardian',
                'author' => $data['fields']['byline'] ?? null,
                'date' => $data['webPublicationDate'] ?? null,
                'description' => $data['fields']['bodyText'] ?? null,
                'url' => $data['webUrl'] ?? null,
                'image' => $data['fields']['thumbnail'] ?? null,
                'webTitle' => $data['webTitle'] ?? null,
            ];
        });
        return $updatedResponseCollection->toArray();
    }

    /**
     * update the resource and return it into an array.
     *
     * @return array
     */
    public function standardizeNewsApiResponse($response)
    {
        $responseCollection = collect($response);
        $updatedResponseCollection = $responseCollection->map(function ($data) {
            return [
                'id' => Str::slug($data['title'].$data['author']),
                'category' => null,
                'source' => $data['source']['name'] ?? null,
                'author' => $data['author'] ?? null,
                'date' => $data['publishedAt'] ?? null,
                'description' => $data['description'] ?? null,
                'url' => $data['url'] ?? null,
                'image' => $data['urlToImage'] ?? null,
                'title' => $data['title'] ?? null,
            ];
        });
        return $updatedResponseCollection->toArray();
    }
}
