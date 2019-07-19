<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Comment
 * @package App\Models
 * @version July 19, 2019, 4:37 am UTC
 *
 * @property string text
 * @property integer vacancy_id
 * @property integer user_id
 */
class Comment extends Model
{

    public $table = 'comments';
    


    public $fillable = [
        'text',
        'vacancy_id',
        'user_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'text' => 'string',
        'vacancy_id' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'text' => 'required',
        'vacancy_id' => 'required'
    ];

    
}
