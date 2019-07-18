<?php

namespace App\Repositories;

use App\Models\Vacancy;
use App\Repositories\BaseRepository;

/**
 * Class VacancyRepository
 * @package App\Repositories
 * @version July 18, 2019, 2:47 pm UTC
*/

class VacancyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'title',
        'description',
        'location',
        'experience',
        'email',
        'phone',
        'user_id'
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
        return Vacancy::class;
    }
}
