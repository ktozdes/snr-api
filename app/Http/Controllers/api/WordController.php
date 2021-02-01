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
    public function index(ParserInterface $parserInterface)
    {
        $result = $parserInterface->post('api/word.get_list');
        $words = Word::hydrate( $result->list );
        return response()->json([
            'items' => $words
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
            'word'=> $request->word,
            'positive'=> $request->positive_total,
            'neutral'=> $request->neutral_total,
            'negative'=> $request->negative_total,
            'amount'=> ($request->positive_total + $request->neutral_total + $request->negative_total),
        ]) );
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
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
            'id'=> $id,]) );
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
