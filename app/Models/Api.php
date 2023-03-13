<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;

class Api extends Model
{
    use HasFactory;

    /**
     * @param $urlParams
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchNewsFromNytimes($keyword = null, $date = null, $page, $offset)
    {
        if (!is_null($date)) {
            $date = Carbon::createFromFormat('Y-m-d', $date)
                ->format('Ymd');
        }
        return (new Helper)->nyTimesApiCall($keyword, $date, $page, $offset);
    }

    /**
     * @param $urlParams
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchNewsFromGuardian($keyword = null, $date = null, $page, $offset)
    {
        return (new Helper)->guardianApiCall($keyword, $date, $page, $offset);
    }

    /**
     * @param $urlParams
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchNewsFromNewsApi($keyword = null, $date = null, $page, $offset)
    {
        return (new Helper)->newsApiApiCall($keyword, $date, $page, $offset);
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllSources()
    {
        return (new Helper)->getSourcesApiCall();
    }

    /**
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllAuthors()
    {
        return (new Helper)->getAuthorsApiCall();
    }

}
