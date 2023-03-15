<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserSource
 * @package App
 * @OA\Schema(
 *     schema="UserSourceRequest",
 *     type="object",
 *     title="User Source Request",
 *     required={"user_id","source_name","source_id"},
 *     properties={
 *         @OA\Property(property="user_id", type="integer"),
 *         @OA\Property(property="source_name", type="string"),
 *         @OA\Property(property="source_id", type="string"),
 *     }
 * )
 */
class UserSource extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_id', 'source_name', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
