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
        'file' => 'required'
    ];

    public function vacancy(){
        return $this->belongsTo('App\Models\Vacancy');
    }

















    
}
