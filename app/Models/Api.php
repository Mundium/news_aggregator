<?php

namespace App\Models;

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
    public function fetchApi($keyword = null, $date = null, $page, $offset)
    {
        return (new Helper)->apiCall($keyword, $date, $page, $offset);
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
