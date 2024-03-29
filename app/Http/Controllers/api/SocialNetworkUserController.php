<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\SocialNetworkUser;
use Illuminate\Http\Request;
use App\Interfaces\ParserInterface;
use Illuminate\Support\Facades\Gate;

class SocialNetworkUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ParserInterface $parserInterface, Request $request)
    {
        Gate::authorize('social network user', 'view');
        $filter = [
            'page' => (int)$request->page > 0 ? (int)$request->page : 1,
            'count' => 10,
        ];

        if (isset($request->sort_by) && in_array($request->sort_by, ['id_desc', 'id_asc', 'post_count_asc', 'post_count_desc', 'comment_count_asc', 'comment_count_desc'])) {
            $field = str_replace(['_desc', '_asc'], '', $request->sort_by);
            $sortOption = strpos($request->sort_by, 'desc') !== false ? 'desc' : 'asc';

            $filter['sort'][] = [
                'field' => $field,
                'value' => $sortOption,
            ];
        }

        $result = $parserInterface->post('api/user.get_list', (string)json_encode($filter));
        $users = SocialNetworkUser::hydrate($result->list);
        return response()->json([
            'items' => $users,
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function comments(ParserInterface $parserInterface, int $userID, Request $request)
    {
        Gate::authorize('social network user', 'view');

        if (is_numeric($userID) && $userID > 0) {
            $filter = [
                'user_id' => $userID,
                'page' => (int)$request->page > 0 ? (int)$request->page : 1,
                'count' => $this->perPage,
            ];

            $result = $parserInterface->post('api/user.get_comments', (string)json_encode($filter));
            $comments = Comment::hydrate($result->list);
            return response()->json([
                'items' => $comments,
                'user' => [
                    'id' => $userID,
                    'author_username' => $comments[0]['author_username'],
                ],
                'pagination' => [
                    'page_count' => $result->page_count
                ]
            ]);
        }
        return response()->json([
            'error_message' => [__('Invalid user id')]
        ], 422);
    }
}
