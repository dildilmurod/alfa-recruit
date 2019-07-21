<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCandidateAPIRequest;
use App\Http\Requests\API\UpdateCandidateAPIRequest;
use App\Models\Candidate;
use App\Models\Tag;
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
        $this->middleware('auth:api', ['except' => ['store']]);
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
//        $candidates = $this->candidateRepository->all(
//            $request->except(['skip', 'limit']),
//            $request->get('skip'),
//            $request->get('limit')
//        );
        $candidates = Candidate::where([
            ['status', '<>', 0],
        ])->orderBy('id', 'desc')->get();

        return $this->sendResponse($candidates->toArray(), 'Candidates retrieved successfully');
    }

    public function gen_name($file)
    {
        //creates unique file name
        $fileName = $file->getClientOriginalName();
        $fileName = pathinfo($fileName, PATHINFO_FILENAME);
        //just takes file extension
        $ext = $file->getClientOriginalExtension();
        //filename to store
        $fileToStore = md5(uniqid($fileName)) . '.' . $ext;

        return $fileToStore;
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
        $input = $request->except(['file']);//$request->all();

        $file = $request->file('file');

        if ($file) {
            //$input['filesize'] = $file->getSize();
            $fileToStore = $this->gen_name($file);

            $file->move('candidate_files', $fileToStore);
            $input['file'] = $fileToStore;
        }

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

        $candidate->comment;
        $candidate->tags;

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

    public function set_tags($id, Request $request)
    {
//        $input = $request->all();

        /** @var Candidate $candidate */
        $candidate = $this->candidateRepository->find($id);

        if (empty($candidate)) {
            return $this->sendError('Candidate not found');
        }
        $tags = $request->except(['']);

//        return $tags['tag'][0];
        $tag_list = [];
        foreach ($tags['tag'] as $tag) {
            $db = Tag::where('text', $tag)->first();
            if (empty($db)) {
                $model = Tag::firstOrNew(['text' => $tag]);
                $model->text = $tag;
                $model->save();
            }
            else{
                $model = $db;
            }
            array_push($tag_list, $model->id);
        }
//        return $tag_list;
        if (!empty($tag_list)) {
            $candidate->tags()->detach();
            $candidate->tags()->attach($tag_list);

        }

//        $candidate = $this->candidateRepository->update($input, $id);

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
