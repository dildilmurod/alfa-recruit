<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Vacancy
 * @package App\Models
 * @version July 18, 2019, 2:47 pm UTC
 *
 * @property string title
 * @property string description
 * @property string location
 * @property integer experience
 * @property string email
 * @property string phone
 * @property integer user_id
 */
class Vacancy extends Model
{

    public $table = 'vacancies';
    


    public $fillable = [
        'title',
        'description',
        'location',
        'experience',
        'email',
        'phone',
        'user_id',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'location' => 'string',
        'experience' => 'integer',
        'email' => 'string',
        'phone' => 'string',
        'status' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'required',
        'description' => 'required'
    ];

    
}
