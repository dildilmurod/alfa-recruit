<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCandidateAPIRequest;
use App\Http\Requests\API\UpdateCandidateAPIRequest;
use App\Models\Candidate;
use App\Models\Tag;
use App\Notifications\CandidateShared;
use App\Repositories\CandidateRepository;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
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
        $this->middleware('admin', ['only' => ['destroy', 'deactivate']]);

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
        ])->orderBy('id', 'desc')->paginate(10);

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

    public function my_notifications()
    {
        $notifications = auth('api')->user()->notifications;
        return $this->sendResponse($notifications, 'Notifications retrieved successfully');

    }

    public function share_candidates($id, Request $request)
    {


        $user = User::find($id);
        $candidates = $request->except(['']);

        $can_list = $candidates['candidate'];


        if (!empty($can_list)) {
            Notification::send($user, new CandidateShared($can_list));
            return response()->json(
                [
                    'success' => true,
                    'data' => [],
                    'message' => 'Notification sent successfully'
                ],
                201);
        } else {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => 'Notification sent failed'
                ]);
        }


    }

    public function notification($id)
    {
        $notification = DB::table('notifications')
            ->where('id', $id)
            ->first();
        if (empty($notification)) {
            return $this->sendError('Notification is not found');
        }
        if ($notification->notifiable_id == auth('api')->user()->id) {
            auth('api')->user()->unreadNotifications->markAsRead();
        } else {
            return response()->json(
                [
                    'success' => false,
                    'data' => [],
                    'message' => 'You can not access this, so it is not sent to you'
                ]);
        }

        $can_list = json_decode($notification->data, TRUE);
        $candidates = Candidate::whereIn('id', $can_list['candidates'])->get();

        return response()->json(
            [
                'success' => true,
                'data' => $candidates,
                'message' => 'Candidates retrieved successfully'
            ]);


    }

    //function sends email by gmail service
    private function sendgmail($name, $title, $file, $vacancy)
    {
        $data = ['name' => $name, "title" => $title, "file" => $file, "vacancy" => $vacancy];

        $users = User::where('role_id', 0)->get();
        foreach ($users as $user) {
            Mail::send('gmail', $data, function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('From Alfa-talent With Gmail');
                $message->from('studentblog98@gmail.com', ' New candidate');
                usleep(200000); //wait for 0.2 sec between mails
            });
        }

        if (Mail::failures()) {
            return false;//response()->Fail('Sorry! Please try again latter');
        } else {
            return true;//response()->json('Yes, You have sent email to GMAIL from LARAVEL !!');
        }
    }


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

        $vacancy = $candidate->vacancy;
        $vacancy = $vacancy->title;
        //if vacancy is not set for candidate sends empty vacancy title
        if (empty($vacancy) || is_null($vacancy)) {
            $vacancy = '';
        }

        $this->sendgmail(
            $input['name'], $input['job_title'],
            'http://alfa-talent.000webhostapp.com/candidate_files/' . $input['file'],
            $vacancy);


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

        $candidate->is_read = 1;

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


    //setting tags for specific candidate
    public function set_tags($id, Request $request)
    {
        /** @var Candidate $candidate */
        $candidate = $this->candidateRepository->find($id);

        if (empty($candidate)) {
            return $this->sendError('Candidate not found');
        }
        $tags = $request->except(['']);

        $tag_list = [];
        foreach ($tags['tag'] as $tag) {
            $db = Tag::where('text', $tag)->first();
            //if tag does not exist in DB, creates new one and appends
            //otherwise appends existing one
            if (empty($db)) {
                $model = Tag::firstOrNew(['text' => $tag]);
                $model->text = $tag;
                $model->save();
            } else {
                $model = $db;
            }
            array_push($tag_list, $model->id);
        }
        if (!empty($tag_list)) {
            $candidate->tags()->detach();
            $candidate->tags()->attach($tag_list);

        }


        return $this->sendResponse($candidate->toArray(), 'Candidate updated successfully');
    }

    //searchs candidates by tag
    public function search_tag(Request $request)
    {
        $input = $request->except(['']);
        $tag = Tag::where('text', $request['tag'])->first();
        if (!empty($tag)) {
            $tag->candidates;
            return response()->json(
                [
                    'success' => true,
                    'data' => $tag,
                    'message' => 'Candidates retrieved successfully'
                ],
                201);
        }
        return $this->sendError('Nothing found');


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
