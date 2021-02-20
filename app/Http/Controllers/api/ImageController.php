<?php

namespace App\Http\Controllers\api;

use App\Interfaces\ParserInterface;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ParserInterface $parserInterface
     * @param string $imageName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function proxyImage(ParserInterface $parserInterface, string $imageName)
    {
        return $parserInterface->getFile('api/images/' . $imageName);
//        return Response::stream(function() use($imageName){
//        echo config('app.python.dev_url') . ;
//        },200, ["Content-type: image/png"]);;
//        return response()->file(
//            config('app.python.dev_url') . '/api/images/' . $imageName ,
//            ["Content-type: image/png"]
//        );
    }
}
