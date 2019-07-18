<?php

namespace App\Repositories;

use App\Models\Candidate;
use App\Repositories\BaseRepository;

/**
 * Class CandidateRepository
 * @package App\Repositories
 * @version July 18, 2019, 3:26 pm UTC
*/

class CandidateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'location',
        'dob',
        'sex',
        'citizenship',
        'experience',
        'vacancy_id',
        'file',
        'job_title'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Candidate::class;
    }
}
