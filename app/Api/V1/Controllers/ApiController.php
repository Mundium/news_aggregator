<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api;
use App\Models\UserAuthor;
use App\Models\UserCategory;
use App\Models\UserSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use MarcinOrlowski\ResponseBuilder\ResponseBuilder;

class ApiController extends Controller
{
    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $offset = $request->query('offset', 4);
        $user = Auth::guard()->user();
        if (!$user) {
            return ResponseBuilder::asError(404)->withMessage('user_not_found')->build();
        }
        $userAuthor = UserAuthor::where([
            ['user_id', $user->id],
        ])->get(['author_name'])->unique('author_id')->map(function($author) {
            return $author->author_name;
        })->toArray();
        $userCategory = UserCategory::where([
            ['user_id', $user->id],
        ])->get(['category_name'])->unique('category_id')->map(function($category) {
            return $category->category_name;
        })->toArray();
        $userSource = UserSource::where([
            ['user_id', $user->id],
        ])->get(['source_name'])->unique('source_id')->map(function($source) {
            return $source->source_name;
        })->toArray();

        $keyword = null;
        $date = null;

        if ($request->has('keyword') && $request->filled('keyword')) {
            $keyword = $request->keyword;
        }

        if ($request->has('date') && $request->filled('date')) {
            $date = $request->date;
        }

        $apiModel = new Api();
        $response = $apiModel->fetchApi($keyword, $date, $page, $offset);

        $response_ny_time = $this->standardizeNYTimesResponse($response['ny_times']);
        $response_guardian = $this->standardizeGuardianResponse($response['guardian']);
        $response_news_api = $this->standardizeNewsApiResponse($response['news_api']);

        $response = array_merge($response_ny_time, $response_guardian, $response_news_api);

        $response = collect($response);
        if (($request->has('author') && $request->filled('author')) || $userAuthor) {
            $userAuthor[] = $request->author;
            $response = $response->whereIn('author', $userAuthor);
        }

        if (($request->has('category') && $request->filled('category')) || $userCategory) {
            $userCategory[] = $request->category;
            $response = $response->whereIn('category', $userCategory);
        }

        if (($request->has('source') && $request->filled('source')) || $userSource) {
            $userSource[] = $request->source;
            $response = $response->whereIn('source', $userSource);
        }

        $data = ['articles' => $response->values()->toArray(), "current_page" => $page];
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
