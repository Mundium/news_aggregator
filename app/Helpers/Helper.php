<?php

namespace App\Helpers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

class Helper
{
    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeApiCalls($url_params = null)
    {
        try {
            $endpoints = [
                "guardian" => config('app.guardian_url') . '?' . '&api-key=' . config('app.guardian_key'),
                "news_api" => config('app.news_api_url') . 'everything?q=bitcoin&apiKey=' . config('app.news_api_key'),
                "ny_times" => config('app.ny_times_url') . 'articlesearch.json?&api-key=' . config('app.ny_times_key'),
            ];

            $results = [];
            $client = new Client();
            foreach ($endpoints as $key => $endpoint) {
                if (Cache::has($key)) {
                    $data = Cache::get($key);
                } else {
                    $response = $client->request('GET', $endpoint);
                    $data = json_decode($response->getBody(), true);
                    Cache::put($key, $data, 10);
                }

                $results[$key] = $data;
            }
            return $results;
        } catch (RequestException $e) {
            return $e->getResponse();
        }
    }

    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function guardianApiCall($url_params = null)
    {
        $endpoint = config('app.guardian_url') . '?api-key=' . config('app.guardian_key'). '&show-tags=contributor';
        $results = [];
        $listResults = [];

        $client = new Client();
        if (Cache::has('guardian')) {
            $results = Cache::get('guardian');
        } else {
            $response = $client->request('GET', $endpoint);
            $data = json_decode($response->getBody(), true);
            $results = $data['response']['results'] ?? [];
            Cache::put('guardian', $results, 10);
        }

        foreach ($results as $key => $result){
            $listResults[$key] = $this->guardianToArray($result);
        }

        return $listResults;
    }

    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSourcesApiCall()
    {
        $endpoint = config('app.news_api_url') . 'sources?&apiKey=' . config('app.news_api_key');

        $client = new Client();
        if (Cache::has('sources')) {
            $results = Cache::get('sources');
        } else {
            $response = $client->request('GET', $endpoint);
            $data = json_decode($response->getBody(), true);
            $results = $data;
            $results["sources"][] = ['id' => 'guardian', 'name' => 'The guardian'];
            $results["sources"][] = ['id' => 'ny_times', 'name' => 'The New York Times'];
            Cache::put('sources', $results, 10);
        }

        return $results["sources"];
    }

    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAuthorsApiCall()
    {
        $guardian = config('app.guardian_url') . '?api-key=' . config('app.guardian_key') . '&show-tags=contributor&page-size=200';
        $news_api = config('app.news_api_url') . 'everything?apiKey=' . config('app.news_api_key') . '&q=*';
        $ny_times = config('app.ny_times_url') . 'articlesearch.json?api-key=' . config('app.ny_times_key');

        $client = new Client();
        if (Cache::has('authors')) {
            $results = Cache::get('authors');
        } else {

            $news_api_response = $client->request('GET', $news_api);
            $news_api_data = json_decode($news_api_response->getBody(), true);
            $news_api_results = collect($news_api_data['articles'])->map(function ($data){
                return [ strtolower(str_replace(' ', '_', $data['author'])) => $data['author']];

            })->filter();

            $guardian_response = $client->request('GET', $guardian);
            $guardian_data = json_decode($guardian_response->getBody(), true);
            $guardian_results = collect($guardian_data['response']['results'])->map(function ($data){
                $tmp_data = collect($data['tags'])->map(function ($author){
                    return [ strtolower(str_replace(' ', '_', $author['webTitle'])) => $author['webTitle']];
                })->toArray();
                return $tmp_data ? $tmp_data[0] : null;
            })->filter();

            $ny_times_response = $client->request('GET', $ny_times);
            $ny_times_data = json_decode($ny_times_response->getBody(), true);
            $ny_times_results = collect($ny_times_data['response']['docs'])->map(function ($data){
                $tmp_data = collect($data['byline']['person'])->map(function ($author){
                    return [ strtolower($author['firstname'].'_'.$author['lastname']) => $author['firstname'].' '.$author['lastname']];
                })->toArray();
                return $tmp_data ? $tmp_data[0] : null;
            })->filter();

            $results = $news_api_results->merge($guardian_results->merge($ny_times_results))->toArray();

            Cache::put('authors', $results, 10);
        }

        return $results;
    }

}