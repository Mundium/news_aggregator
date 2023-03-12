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
    public function fetchNewsFromNytimes($keyword = null, $date = null, $category = null, $author = null)
    {
        return (new Helper)->nyTimesApiCall($keyword, $date, $category, $author);
    }

    /**
     * @param $urlParams
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchNewsFromGuardian($keyword = null, $date = null, $category = null, $author = null)
    {
        return (new Helper)->guardianApiCall($keyword, $date, $category, $author);
    }

    /**
     * @param $urlParams
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchNewsFromNewsApi($keyword = null, $date = null, $category = null, $author = null, $source = null)
    {
        return (new Helper)->newsApiApiCall($keyword, $date, $category, $author, $source);
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
