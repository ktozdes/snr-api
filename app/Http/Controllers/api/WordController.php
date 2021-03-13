<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Interfaces\ParserInterface;
use App\Models\Word;
use Illuminate\Http\Request;

class WordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ParserInterface $parserInterface, Request $request)
    {
        $filter = [
            'page' => (int)$request->page > 0 ? (int)$request->page : 1,
            'count' => 10,
        ];
        $result = $parserInterface->post('api/word.get_list', (string)json_encode($filter));
        $words = Word::hydrate($result->list);
        return response()->json([
            'items' => $words,
            'pagination' => [
                'page_count' => $result->page_count
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ParserInterface $parserInterface
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, ParserInterface $parserInterface)
    {
        $request->validate([
            'word' => 'required',
            'neutral_total' => 'nullable|integer',
            'negative_total' => 'nullable|integer',
            'positive_total' => 'nullable|integer',
        ]);
        $result = $parserInterface->post('api/word.create', json_encode([
            'word' => $request->word,
            'positive' => $request->positive_total,
            'neutral' => $request->neutral_total,
            'negative' => $request->negative_total,
            'amount' => ($request->positive_total + $request->neutral_total + $request->negative_total),
        ]));
        return (isset($result->id))
            ? response()->json([
                'result' => $result,
                'success_message' => [
                    __('Word created'),
                ]
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ParserInterface $parserInterface
     * @param int $commentID
     * @return \Illuminate\Http\JsonResponse
     */
    public function massStore(Request $request, ParserInterface $parserInterface, int $commentID)
    {
        $result = false;

        $filter = [
            'id' => $commentID
        ];
        $comment = $parserInterface->post('api/post.get_comment', (string)json_encode($filter));
        $array = json_decode(json_encode($comment->info->words), true);
        $newWords = $request->words;

        //syncing old words
        foreach ($array as $oldWord) {
            $found = false;
            foreach ($newWords as $key => $newWord) {
                if (
                    (isset($newWord['index']) && isset($oldWord['index']) && $newWord['index'] == $oldWord['index'])
                    && (isset($newWord['word']) && isset($oldWord['word']) && $newWord['word'] == $oldWord['word'])
                    && (isset($newWord['type']) && isset($oldWord['type']) && $newWord['type'] == $oldWord['type'])) {
                    $found = true;
                    unset($newWords[$key]);
                    break;
                } else if (
                    (isset($newWord['index']) && isset($oldWord['index']) && $newWord['index'] == $oldWord['index'])
                    && (isset($newWord['word']) && isset($oldWord['word']) && $newWord['word'] == $oldWord['word'])
                    && (isset($newWord['type']) && isset($oldWord['type']) && $newWord['type'] != $oldWord['type'])) {
                    $found = true;
                    $parserInterface->post('api/post.del_word', json_encode([
                        'word' => $oldWord['word'],
                        'comment_id' => $commentID,
                        'index' => $oldWord['index'],
                    ]));
                    break;
                }
            }
            if ($found === false) {
                $parserInterface->post('api/post.del_word', json_encode([
                    'word' => $oldWord['word'],
                    'comment_id' => $commentID,
                    'index' => $oldWord['index'],
                ]));
            }
        }

        //saving new words
        if (isset($newWords) && count($newWords) > 0) {
            foreach ($newWords as $word) {
                $result = $parserInterface->post('api/post.add_word', json_encode([
                    'word' => $word['word'],
                    'comment_id' => $commentID,
                    'type' => $word['type'],
                    'index' => $word['index'],
                ]));
            }
        }
        return
            response()->json([
                'result' => $result,
                'success_message' => [
                    __('Words synced'),
                ]
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param ParserInterface $parserInterface
     * @param int $commentID
     * @return \Illuminate\Http\JsonResponse
     */
    public function massStorePost(Request $request, ParserInterface $parserInterface, int $postID)
    {
        $result = false;

        $filter = [
            'id' => $postID
        ];
        $newWords = $request->words;
//        $comment = $parserInterface->post('api/post.get_comment', (string)json_encode($filter));
//        $array = json_decode(json_encode($comment->info->words), true);
//
//        //syncing old words
//        foreach ($array as $oldWord) {
//            $found = false;
//            foreach ($newWords as $key => $newWord) {
//                if (
//                    (isset($newWord['index']) && isset($oldWord['index']) && $newWord['index'] == $oldWord['index'])
//                    && (isset($newWord['word']) && isset($oldWord['word']) && $newWord['word'] == $oldWord['word'])
//                    && (isset($newWord['type']) && isset($oldWord['type']) && $newWord['type'] == $oldWord['type'])) {
//                    $found = true;
//                    unset($newWords[$key]);
//                    break;
//                }
//                else if (
//                    (isset($newWord['index']) && isset($oldWord['index']) && $newWord['index'] == $oldWord['index'])
//                    && (isset($newWord['word']) && isset($oldWord['word']) && $newWord['word'] == $oldWord['word'])
//                    && (isset($newWord['type']) && isset($oldWord['type']) && $newWord['type'] != $oldWord['type'])) {
//                    $found = true;
//                    $parserInterface->post('api/post.del_word', json_encode([
//                        'word' => $oldWord['word'],
//                        'comment_id' => $postID,
//                        'index' => $oldWord['index'],
//                    ]));
//                    break;
//                }
//            }
//            if ($found === false) {
//                $parserInterface->post('api/post.del_word', json_encode([
//                    'word' => $oldWord['word'],
//                    'comment_id' => $postID,
//                    'index' => $oldWord['index'],
//                ]));
//            }
//        }

        //saving new words
        if (isset($newWords) && count($newWords) > 0) {
            foreach ($newWords as $word) {
                $result = $parserInterface->post('api/post.add_word_to_post', json_encode([
                    'word' => $word['word'],
                    'post_id' => $postID,
                    'type' => $word['type'],
                    'index' => $word['index'],
                ]));
            }
        }
        return
            response()->json([
                'result' => $result,
                'success_message' => [
                    __('Words synced'),
                ]
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param ParserInterface $parserInterface
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id, ParserInterface $parserInterface)
    {
        $result = $parserInterface->post('api/word.delete', json_encode([
            'id' => $id,]));
        return (isset($result->id))
            ? response()->json([
                'result' => $result,
                'success_message' => [
                    __('Word deleted'),
                ]
            ])
            : response()->json([
                'result' => $result,
                'error_message' => [
                    __('Something went wrong')
                ]
            ]);
    }
}
