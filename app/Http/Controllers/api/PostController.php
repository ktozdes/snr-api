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
        $result = $parserInterface->post('api/post.get_posts', (string)json_encode($filter));
        Cache::forget('posts');
        $posts = Cache::remember('posts', config('app.day_in_seconds', (60 * 60 * 24)), function () use ($result) {
            return Post::hydrate($result->list);
        });
        return response()->json([
            'items' => $posts,
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
        $posts = Cache::get('posts');
        foreach ($posts as $post) {
            if (isset($post['id']) && $post['id'] == $postID) {
                return response()->json([
                    'post' => $post,
                ]);
            }
        }

        return response()->json([
            'warning_message' => [
                __('No record found')
            ],
        ]);
    }
}
