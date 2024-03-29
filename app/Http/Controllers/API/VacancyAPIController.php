<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateVacancyAPIRequest;
use App\Http\Requests\API\UpdateVacancyAPIRequest;
use App\Models\Candidate;
use App\Models\Vacancy;
use App\Repositories\VacancyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class VacancyController
 * @package App\Http\Controllers\API
 */

class VacancyAPIController extends AppBaseController
{
    /** @var  VacancyRepository */
    private $vacancyRepository;

    public function __construct(VacancyRepository $vacancyRepo)
    {
        $this->vacancyRepository = $vacancyRepo;
        $this->middleware('auth:api', ['except' => ['']]);
        $this->middleware('admin', ['only' => ['destroy', 'deactivate']]);
    }

    /**
     * Display a listing of the Vacancy.
     * GET|HEAD /vacancies
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
//        $vacancies = $this->vacancyRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        );
        //if user is not admin shows only vacancies created by him/her
        //shows active and not active (but not destroyed) vacancies
        if (auth('api')->user()->role_id != 0) {
            $vacancies = Vacancy::where([
                ['status', '<>', 0],
                ['user_id', auth('api')->user()->id]
            ])->orderBy('id', 'desc')->paginate(10);
        }
        else{
            $vacancies = Vacancy::where([
                ['status', '<>', 0],
            ])->orderBy('id', 'desc')->paginate(10);
        }

        //for every vacancy counts number of unread and total candidates
        foreach ($vacancies as $vacancy){
            $read_num = DB::table('candidates')
                ->where([
                    ['is_read', 1],
                    ['vacancy_id', $vacancy->id]
                ])->count();

            $user = $vacancy->user;
            $vacancy->candidate_count = $vacancy->candidate()->count();
            $vacancy->candidate_read_count = $read_num;
        }


        return $this->sendResponse($vacancies->toArray(), 'Vacancies retrieved successfully');
    }

    /**
     * Store a newly created Vacancy in storage.
     * POST /vacancies
     *
     * @param CreateVacancyAPIRequest $request
     *
     * @return Response
     */

    public function store(CreateVacancyAPIRequest $request)
    {
        $input = $request->except([]);
            //$request->all();
        //if user_id is not sent gets id of current user
        if(!$request->user_id && empty($request->user_id)) {
            $input['user_id'] = auth('api')->user()->id;
        }

        $vacancy = $this->vacancyRepository->create($input);

        return $this->sendResponse($vacancy->toArray(), 'Vacancy saved successfully');
    }

    /**
     * Display the specified Vacancy.
     * GET|HEAD /vacancies/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->vacancyRepository->find($id);

        if (empty($vacancy)) {
            return $this->sendError('Vacancy not found');
        }
        if (auth('api')->user()->role_id != 0 && $vacancy->user_id != auth('api')->user()->id) {
            return $this->sendError('Vacancy is not yours');
        }

        $vacancy->candidate;
        $vacancy->user;

        return $this->sendResponse($vacancy->toArray(), 'Vacancy retrieved successfully');
    }

    /**
     * Update the specified Vacancy in storage.
     * PUT/PATCH /vacancies/{id}
     *
     * @param int $id
     * @param UpdateVacancyAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateVacancyAPIRequest $request)
    {
        $input = $request->all();

        /** @var Vacancy $vacancy */
        $vacancy = $this->vacancyRepository->find($id);

        if (empty($vacancy)) {
            return $this->sendError('Vacancy not found');
        }
        //if user is not admin and vacancy is not created by him/her sends error
        if(auth('api')->user()->role_id != 0 && $vacancy->user_id != auth('api')->user()->id){
            return $this->sendError('Vacancy is not yours or you should be admin');
        }
        $input['user_id'] = auth('api')->user()->id;

        $vacancy = $this->vacancyRepository->update($input, $id);

        return $this->sendResponse($vacancy->toArray(), 'Vacancy updated successfully');
    }

    /**
     * Remove the specified Vacancy from storage.
     * DELETE /vacancies/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */

    public function deactivate($id)
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->vacancyRepository->find($id);

        if (empty($vacancy)) {
            return $this->sendError('Vacancy not found');
        }

        $vacancy->status = 2;
        $vacancy->save();

//        $vacancy->delete();

        return $this->sendResponse($id, 'Vacancy deactivated successfully');
    }


    public function destroy($id)
    {
        /** @var Vacancy $vacancy */
        $vacancy = $this->vacancyRepository->find($id);

        if (empty($vacancy)) {
            return $this->sendError('Vacancy not found');
        }

        $vacancy->status = 0;
        $vacancy->save();

//        $vacancy->delete();

        return $this->sendResponse($id, 'Vacancy deleted successfully');
    }
}
