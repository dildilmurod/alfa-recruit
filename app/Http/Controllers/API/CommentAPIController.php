<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCommentAPIRequest;
use App\Http\Requests\API\UpdateCommentAPIRequest;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class CommentController
 * @package App\Http\Controllers\API
 */

class CommentAPIController extends AppBaseController
{
    /** @var  CommentRepository */
    private $commentRepository;

    public function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepository = $commentRepo;
    }

    /**
     * Display a listing of the Comment.
     * GET|HEAD /comments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $comments = $this->commentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($comments->toArray(), 'Comments retrieved successfully');
    }

    /**
     * Store a newly created Comment in storage.
     * POST /comments
     *
     * @param CreateCommentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCommentAPIRequest $request)
    {
        $input = $request->except(['user_id']);

        $input['user_id'] = auth('api')->user()->id;

        $comment = $this->commentRepository->create($input);

        return $this->sendResponse($comment->toArray(), 'Comment saved successfully');
    }

    /**
     * Display the specified Comment.
     * GET|HEAD /comments/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Comment $comment */
        $comment = $this->commentRepository->find($id);

        if (empty($comment)) {
            return $this->sendError('Comment not found');
        }

        return $this->sendResponse($comment->toArray(), 'Comment retrieved successfully');
    }

    /**
     * Update the specified Comment in storage.
     * PUT/PATCH /comments/{id}
     *
     * @param int $id
     * @param UpdateCommentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCommentAPIRequest $request)
    {
        $input = $request->all();

        /** @var Comment $comment */
        $comment = $this->commentRepository->find($id);

        if (empty($comment)) {
            return $this->sendError('Comment not found');
        }

        $comment = $this->commentRepository->update($input, $id);

        return $this->sendResponse($comment->toArray(), 'Comment updated successfully');
    }

    /**
     * Remove the specified Comment from storage.
     * DELETE /comments/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Comment $comment */
        $comment = $this->commentRepository->find($id);

        if (empty($comment)) {
            return $this->sendError('Comment not found');
        }

        $comment->delete();

        return $this->sendResponse($id, 'Comment deleted successfully');
    }
}
