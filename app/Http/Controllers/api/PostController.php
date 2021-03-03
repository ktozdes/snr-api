<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ParserInterface;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @param Request $request
     * @return JsonResponse
     */
    public function index(ParserInterface $parserInterface, Request $request)
    {
        $filter = [
            'page' => (int)$request->page ?? 1,
            'count' => $this->perPage,
        ];
        if (is_array($request->keywords) && count($request->keywords) > 0) {
            $filter['hashtags'] = $request->keywords;
        }
        $result = $parserInterface->post('api/post.get_posts', (string)json_encode($filter));

        $posts = Post::hydrate($result->list);
        
        return response()->json([
            'items' => $posts,
            'filter' => $filter,
            'pagination' => [
                'page_count' => $result->page_count
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @param int $postID
     * @return JsonResponse
     */
    public function show(ParserInterface $parserInterface, int $postID)
    {
        $filter = [
            'id' => $postID
        ];
        $result = $parserInterface->post('api/post.get_post', (string)json_encode($filter));
        return response()->json([
            'post' => $result->info,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ParserInterface $parserInterface
     * @param Request $request
     * @param $postID
     * @return JsonResponse
     */
    public function updateStats(ParserInterface $parserInterface, Request $request, $postID)
    {
        $request->validate([
            'positive' => 'nullable|integer',
            'neutral' => 'nullable|integer',
            'negative' => 'nullable|integer',
        ]);

        $filter = [
            'created_by' => auth()->user()->id,
            'id' => $postID,
            'positive' => $request->positive,
            'neutral' => $request->neutral,
            'negative' => $request->negative,
        ];

        $parserInterface->post('api/post.update_statistics', (string)json_encode($filter));
        return response()->json([
            'success_message' => [
                __('Post rating updated'),
            ]
        ]);
    }
}
