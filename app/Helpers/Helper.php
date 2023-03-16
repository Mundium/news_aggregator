<?php

namespace App\Helpers;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Http;

class Helper
{
    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function apiCall($keyword = null, $date = null, $page, $offset)
    {
        $guardianUrlParams = '';
        $nyUrlParams = '';
        $newUrlParams = '';
        if (!is_null($keyword)) {
            $guardianUrlParams .= '&q=' . $keyword;
            $nyUrlParams .= '&q='.$keyword;
            $newUrlParams .= '&q='.$keyword;
        } else {
            $newUrlParams .= '&q=*';
        }
        if (!is_null($date)) {
            $dateNy = Carbon::createFromFormat('Y-m-d', $date)
                ->format('Ymd');
            $guardianUrlParams .= '&from-date=' . $date . '&to-date=' . $date;
            $newUrlParams .= '&from=' . $date . '&to=' . $date;
            $nyUrlParams .= '&begin_date=' . $dateNy . '&begin_date=' . $dateNy;
        }

        $endpoint1 = config('app.ny_times_url') . 'articlesearch.json?api-key=' . config('app.ny_times_key') . '&page=' . $page . $nyUrlParams;
        $endpoint2 = config('app.news_api_url') . 'everything?apiKey=' . config('app.news_api_key') . '&pageSize=' . $offset . '&page=' . $page . $newUrlParams;
        $endpoint3 = config('app.guardian_url') . '?api-key=' . config('app.guardian_key'). '&page-size=' . $offset . '&page=' . $page . '&show-fields=byline,thumbnail,bodyText&' . $guardianUrlParams;

        if (Cache::has('endpoints' . $page)) {
            $results = Cache::get('endpoints' . $page);
        } else {
            try {
                $promises = [];
                $promises[] = Http::async()->get($endpoint1);
                $promises[] = Http::async()->get($endpoint2);
                $promises[] = Http::async()->get($endpoint3);

                $responses = Utils::unwrap($promises);
                foreach ($responses as $response){
                    $response = json_decode($response->getBody(), true);
                }

                $results_ny = $responses[0]['response']['docs'] ?? [];
                $results_news = $responses[1]['articles'] ?? [];
                $results_guardian = $responses[2]['response']['results'] ?? [];

                $results = ['guardian' => $results_guardian, 'ny_times' => $results_ny, 'news_api' => $results_news];

            } catch (RequestException $e){
                $results = ['guardian' => [], 'ny_times' => [], 'news_api' => []];
            }
            Cache::put('endpoints' . $page, $results, 10);
        }
        return $results;
    }

    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getSourcesApiCall()
    {
        $endpoint = config('app.news_api_url') . 'sources?&apiKey=' . config('app.news_api_key');

        if (Cache::has('sources')) {
            $results = Cache::get('sources');
        } else {
            try {
                $response[] = Http::async()->get( $endpoint);
                $data = Utils::unwrap($response);
                $data = json_decode($data[0]->getBody(), true);
                $results = $data;
            } catch (RequestException $e){
                $results = [];
            }
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
            try {
                $promises = [];
                $promises[] = Http::async()->get($ny_times);
                $promises[] = Http::async()->get($news_api);
                $promises[] = Http::async()->get($guardian);

                $responses = Utils::unwrap($promises);
                foreach ($responses as $response){
                    $response = json_decode($response->getBody(), true);
                }

                $ny_times_results = collect($responses[0]['response']['docs'])->map(function ($data){
                    $tmp_data = collect($data['byline']['person'])->map(function ($author){
                        return [ 'id' => mb_strtolower($author['firstname'].'_'.$author['lastname']), 'name' => $author['firstname'].' '.$author['lastname']];
                    })->toArray();
                    return $tmp_data ? $tmp_data[0] : null;
                })->filter();

                $news_api_results = collect($responses[1]['articles'])->map(function ($data){
                    return [ 'id' => mb_strtolower(str_replace(' ', '_', $data['author'])), 'name' => $data['author']];
                })->filter();

                $guardian_results = collect($responses[2]['response']['results'])->map(function ($data){
                    $tmp_data = collect($data['tags'])->map(function ($author){
                        return [ 'id' => mb_strtolower(str_replace(' ', '_', $author['webTitle'])), 'name' => $author['webTitle']];
                    })->toArray();
                    return $tmp_data ? $tmp_data[0] : null;
                })->filter();

            } catch (RequestException $e){
                $news_api_results = collect();
                $guardian_results = collect();
                $ny_times_results = collect();
            }

            $results = $news_api_results->merge($guardian_results->merge($ny_times_results));

            Cache::put('authors', $results, 10);
        }

        return $results->unique()->values()->toArray();
    }

}
