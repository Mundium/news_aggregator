<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserAuthor
 * @package App
 * @OA\Schema(
 *     schema="UserAuthorRequest",
 *     type="object",
 *     title="User Author Request",
 *     required={"user_id","author_name","author_id"},
 *     properties={
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="author_name", type="string"),
 *         @OA\Property(property="author_id", type="string"),
 *     }
 * )
 */
class UserAuthor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id', 'author_name', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
