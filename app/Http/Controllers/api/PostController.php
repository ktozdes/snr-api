<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ParserInterface;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize('post', 'view');
        $filter = [
            'page' => (int)$request->page ?? 1,
            'count' => $this->perPage,
            'hashtags' => []
        ];
        if (is_array($request->keywords) && count($request->keywords) > 0) {
            $filter['keywords'] = $request->keywords;
        }
        if (isset($request->author_username)) {
            $filter['author_username'] = $request->author_username;
        }
        if (isset($request->sort_by) && in_array( $request->sort_by,['id_desc', 'id_asc', 'created_date_asc', 'created_date_desc',  'updated_date_asc', 'updated_date_desc']) ) {
            $field = 'id';
            $sortOption = strpos($request->sort_by, 'desc') !== false ? 'desc' : 'asc';
            if ( in_array( $request->sort_by, ['created_date_asc', 'created_date_desc']) ) {
                $field = 'date';
            }
            else if ( in_array( $request->sort_by, ['updated_date_asc', 'updated_date_desc']) ) {
                $field = 'date_update';
            }
            $filter['sort'][] = [
                'field'=> $field,
                'value'=> $sortOption,
            ];
        }
        $result = $parserInterface->post('api/post.get_posts', (string)json_encode($filter, JSON_UNESCAPED_UNICODE));

        $posts = Post::hydrate($result->list);

        return response()->json([
            'items' => $posts,
            'filter' => (string)json_encode($filter, JSON_UNESCAPED_UNICODE),
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
        Gate::authorize('post', 'view');
        $filter = [
            'id' => $postID
        ];
        $result = $parserInterface->post('api/post.get_post', (string)json_encode($filter));
        $postArray = (array) $result->info;
        $post = new Post( $postArray );
        $post->date_update = $postArray['date_update'];
        $post->id = $postArray['id'];
        $post->words = isset($postArray['words']) ? $postArray['words']: [];
        return response()->json([
            'post' => $post,
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
        Gate::authorize('post', 'edit');
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
