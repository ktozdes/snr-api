<?php

namespace App\Http\Controllers\api;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ParserInterface;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ParserInterface $parserInterface, int $postID)
    {
        if (is_numeric($postID) && $postID > 0){
            $filter = [
                'post_id' => $postID,
                'page' => 1,
                'count' => $this->perPage,
            ];
            $result = $parserInterface->post('api/post.get_comments', (string) json_encode($filter));
            $comments = Comment::hydrate( $result->list );
            return response()->json([
                'items' => $comments
            ]);
        }
        return response()->json([
                'error_message' => [__('Invalid post id')]
            ],422);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
