<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'password_resets';


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['email','token'];
}
