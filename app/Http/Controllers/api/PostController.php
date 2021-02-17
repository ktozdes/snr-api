<?php

namespace App\Http\Controllers\api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Interfaces\ParserInterface;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ParserInterface $parserInterface)
    {
        $filter = [
            'page' => 1,
            'count' => 3,
            'author_username'=> "news.kg"
        ];
        $result = $parserInterface->post('api/post.get_posts', (string)json_encode($filter));
        $posts = Post::hydrate($result->list);
        return response()->json([
            'items' => $posts,
            'pagination' => [
                'total' => $result->total,
                'page_count' => $result->page_count
            ]
        ]);

//        $result = $parserInterface->post('api/post.get_posts', '{"count": 50,"page": 1,"author_username": "news.kg"}');
//        $posts = Post::hydrate( $result->list );
//        return response()->json([
//            'items' => $posts
//        ]);
    }
}
