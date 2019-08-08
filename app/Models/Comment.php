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
        'candidate_id',
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
        'candidate_id' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'text' => 'required',
        'candidate_id' => 'required'
    ];

    public function candidate(){
        return $this->belongsTo('App\Models\Candidate');
    }

    public function user(){
        return $this->belongsTo('App\User');
    }












    
}
