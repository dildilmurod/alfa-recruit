<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Candidate
 * @package App\Models
 * @version July 18, 2019, 3:26 pm UTC
 *
 * @property string name
 * @property string location
 * @property string dob
 * @property string sex
 * @property string citizenship
 * @property integer experience
 * @property integer vacancy_id
 * @property string file
 * @property string job_title
 */
class Candidate extends Model
{

    public $table = 'candidates';
    


    public $fillable = [
        'name',
        'location',
        'dob',
        'sex',
        'citizenship',
        'experience',
        'vacancy_id',
        'file',
        'phone',
        'email',
        'status',
        'job_title'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'location' => 'string',
        'name' => 'string',
        'dob' => 'string',
        'sex' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'citizenship' => 'string',
        'experience' => 'integer',
        'vacancy_id' => 'integer',
        'status' => 'integer',
        'file' => 'string',
        'job_title' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'dob' => 'required',
        'sex' => 'required',
        'citizenship' => 'required',
        'file' => 'required|mimes:docx,pdf,doc,zip|max:100000',
    ];

    public function vacancy(){
        return $this->belongsTo('App\Models\Vacancy');
    }

    public function tags(){
        return $this->belongsToMany('App\Models\Tag', 'candidate_tag', 'candidate_id', 'tag_id');
    }

    public function comment(){
        return $this->hasMany('App\Models\Comment');
    }

//    public function user()
//    {
//        return $this->belongsTo('App\User', 'customer_id', 'id');
//    }
















    
}
