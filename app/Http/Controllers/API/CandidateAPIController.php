<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCandidateAPIRequest;
use App\Http\Requests\API\UpdateCandidateAPIRequest;
use App\Models\Candidate;
use App\Repositories\CandidateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class CandidateController
 * @package App\Http\Controllers\API
 */

class CandidateAPIController extends AppBaseController
{
    /** @var  CandidateRepository */
    private $candidateRepository;

    public function __construct(CandidateRepository $candidateRepo)
    {
        $this->candidateRepository = $candidateRepo;
        $this->middleware('auth:api', ['except' => ['']]);
    }

    /**
     * Display a listing of the Candidate.
     * GET|HEAD /candidates
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $candidates = $this->candidateRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($candidates->toArray(), 'Candidates retrieved successfully');
    }

    /**
     * Store a newly created Candidate in storage.
     * POST /candidates
     *
     * @param CreateCandidateAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCandidateAPIRequest $request)
    {
        $input = $request->all();

        $candidate = $this->candidateRepository->create($input);

        return $this->sendResponse($candidate->toArray(), 'Candidate saved successfully');
    }

    /**
     * Display the specified Candidate.
     * GET|HEAD /candidates/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Candidate $candidate */
        $candidate = $this->candidateRepository->find($id);

        if (empty($candidate)) {
            return $this->sendError('Candidate not found');
        }

        return $this->sendResponse($candidate->toArray(), 'Candidate retrieved successfully');
    }

    /**
     * Update the specified Candidate in storage.
     * PUT/PATCH /candidates/{id}
     *
     * @param int $id
     * @param UpdateCandidateAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCandidateAPIRequest $request)
    {
        $input = $request->all();

        /** @var Candidate $candidate */
        $candidate = $this->candidateRepository->find($id);

        if (empty($candidate)) {
            return $this->sendError('Candidate not found');
        }

        $candidate = $this->candidateRepository->update($input, $id);

        return $this->sendResponse($candidate->toArray(), 'Candidate updated successfully');
    }



    public function deactivate($id)
    {
        /** @var Candidate $vacancy */
        $candidate = $this->candidateRepository->find($id);

        if (empty($candidate)) {
            return $this->sendError('Candidate not found');
        }

        $candidate->status = 2;
        $candidate->save();

//        $candidate->delete();

        return $this->sendResponse($id, 'Candidate deactivated successfully');
    }

    /**
     * Remove the specified Candidate from storage.
     * DELETE /candidates/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Candidate $candidate */
        $candidate = $this->candidateRepository->find($id);

        if (empty($candidate)) {
            return $this->sendError('Candidate not found');
        }

        $candidate->status = 0;
        $candidate->save();

//        $candidate->delete();

        return $this->sendResponse($id, 'Candidate deleted successfully');
    }








}
