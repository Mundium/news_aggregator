<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserCategory
 * @package App
 * @OA\Schema(
 *     schema="UserCategoryRequest",
 *     type="object",
 *     title="User Category Request",
 *     required={"user_id","category_name","category_id"},
 *     properties={
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="category_name", type="string"),
 *         @OA\Property(property="category_id", type="string"),
 *     }
 * )
 */
class UserCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'category_name', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
