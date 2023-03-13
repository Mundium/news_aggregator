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
    public function guardianApiCall($keyword = null, $date = null, $page, $offset)
    {
        $urlParams = '';
        if (!is_null($keyword)) {
            $urlParams .= '&q=' . $keyword;
        }
        if (!is_null($date)) $urlParams .= '&from-date=' . $date . '&to-date=' . $date;
        $endpoint = config('app.guardian_url') . '?api-key=' . config('app.guardian_key'). '&page-size=' . $offset . '&page=' . $page . '&show-fields=byline,thumbnail,bodyText&' . $urlParams;

        $client = new Client();
        if (Cache::has('guardian')) {
            $results = Cache::get('guardian');
        } else {
            try {
                $response = $client->request('GET', $endpoint);
                $data = json_decode($response->getBody(), true);
                $results = $data['response']['results'] ?? [];
            } catch (RequestException $e){
                $results = [];
            }
            Cache::put('guardian', $results, 10);
        }
        return $results;
    }

    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function newsApiApiCall($keyword = null, $date = null, $page, $offset)
    {
        $urlParams = '';
        if (!is_null($keyword)) {
            $urlParams .= '&q='.$keyword;
        } else {
            $urlParams .= '&q=*';
        }
        if (!is_null($date)) $urlParams .= '&from=' . $date . '&to=' . $date;
        $endpoint = config('app.news_api_url') . 'everything?apiKey=' . config('app.news_api_key') . '&pageSize=' . $offset . '&page=' . $page . $urlParams;

        $client = new Client();
        if (Cache::has('news_api')) {
            $results = Cache::get('news_api');
        } else {
            try {
                $response = $client->request('GET', $endpoint);
                $data = json_decode($response->getBody(), true);
                $results = $data['articles'] ?? [];
            } catch (RequestException $e){
                $results = [];
            }
            Cache::put('news_api', $results, 10);
        }
        return $results;
    }

    /**
     * @param $url_params
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function nyTimesApiCall($keyword = null, $date = null, $page, $offset)
    {
        $urlParams = '';
        if (!is_null($keyword)) {
            $urlParams .= '&q='.$keyword;
        }
        if (!is_null($date)) $urlParams .= '&begin_date=' . $date . '&begin_date=' . $date;
        $endpoint = config('app.ny_times_url') . 'articlesearch.json?api-key=' . config('app.ny_times_key') . '&page=' . $page . $urlParams;

        $client = new Client();
        if (Cache::has('ny_times')) {
            $results = Cache::get('ny_times');
        } else {
            try {
                $response = $client->request('GET', $endpoint);
                $data = json_decode($response->getBody(), true);
                $results = $data['response']['docs'] ?? [];
            } catch (RequestException $e){
                $results = [];
            }
            Cache::put('ny_times', $results, 10);
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

        $client = new Client();
        if (Cache::has('sources')) {
            $results = Cache::get('sources');
        } else {
            try {
                $response = $client->request('GET', $endpoint);
                $data = json_decode($response->getBody(), true);
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
                $news_api_response = $client->request('GET', $news_api);
                $news_api_data = json_decode($news_api_response->getBody(), true);
                $news_api_results = collect($news_api_data['articles'])->map(function ($data){
                    return [ 'id' => strtolower(str_replace(' ', '_', $data['author'])), 'name' => $data['author']];
                })->filter();
            } catch (RequestException $e){
                $news_api_results = collect();
            }

            try {
                $guardian_response = $client->request('GET', $guardian);
                $guardian_data = json_decode($guardian_response->getBody(), true);
                $guardian_results = collect($guardian_data['response']['results'])->map(function ($data){
                    $tmp_data = collect($data['tags'])->map(function ($author){
                        return [ 'id' => strtolower(str_replace(' ', '_', $author['webTitle'])), 'name' => $author['webTitle']];
                    })->toArray();
                    return $tmp_data ? $tmp_data[0] : null;
                })->filter();
            } catch (RequestException $e){
                $guardian_results = collect();
            }

            try {
                $ny_times_response = $client->request('GET', $ny_times);
                $ny_times_data = json_decode($ny_times_response->getBody(), true);
                $ny_times_results = collect($ny_times_data['response']['docs'])->map(function ($data){
                    $tmp_data = collect($data['byline']['person'])->map(function ($author){
                        return [ 'id' => strtolower($author['firstname'].'_'.$author['lastname']), 'name' => $author['firstname'].' '.$author['lastname']];
                    })->toArray();
                    return $tmp_data ? $tmp_data[0] : null;
                })->filter();
            } catch (RequestException $e){
                $ny_times_results = collect();
            }

            $results = $news_api_results->merge($guardian_results->merge($ny_times_results))->toArray();

            Cache::put('authors', $results, 10);
        }

        return $results;
    }

}
