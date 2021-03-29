<?php

namespace App\Http\Controllers\api;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ParserInterface;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @param int $postID
     * @param Request $request
     * @return JsonResponse
     */
    public function index(ParserInterface $parserInterface, int $postID, Request $request)
    {
        if (is_numeric($postID) && $postID > 0) {
            $filter = [
                'post_id' => $postID,
                'page' => (int)$request->page > 0 ? (int)$request->page : 1,
                'count' => $this->perPage,
            ];
            $filter['sort'][] = $this->sortBy($request);
            $result = $parserInterface->post('api/post.get_comments', (string)json_encode($filter));
            $comments = Comment::hydrate($result->list);
            return response()->json([
                'items' => $comments,
                'filter'=> $filter,
                'pagination' => [
                    'page_count' => $result->page_count
                ]
            ]);
        }
        return response()->json([
            'error_message' => [__('Invalid post id')]
        ], 422);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param ParserInterface $parserInterface
     * @param Int $commentID
     * @return JsonResponse
     */
    public function show(ParserInterface $parserInterface, int $commentID)
    {
        $filter = [
            'id' => $commentID
        ];
        $result = $parserInterface->post('api/post.get_comment', (string)json_encode($filter));
        if (isset($result->info)) {
            return response()->json([
                'comment' => $result->info,
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Comment $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ParserInterface $parserInterface
     * @param Request $request
     * @param $postID
     * @return JsonResponse
     */
    public function updateStats(ParserInterface $parserInterface, Request $request, $commentID)
    {
        $request->validate([
            'positive' => 'nullable|integer',
            'neutral' => 'nullable|integer',
            'negative' => 'nullable|integer',
        ]);

        $filter = [
            'created_by' => auth()->user()->id,
            'id' => $commentID,
            'positive' => $request->positive,
            'neutral' => $request->neutral,
            'negative' => $request->negative,
        ];

        $parserInterface->post('api/post.update_comment_statistics', (string)json_encode($filter));
        return response()->json([
            'success_message' => [
                __('Comment rating updated'),
            ]
        ]);
    }
}
