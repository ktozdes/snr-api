<?php

namespace App\Http\Controllers\api;

use App\Interfaces\ParserInterface;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

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

    public function uploadLogo($request, $model)
    {
        if (isset($request->image) && strpos($request->image, 'image/') !== false) {
            $image_type = explode("image/", explode(";base64", $request->image)[0]);
            $mime_type = '.' . $image_type[1];
            $filename = auth()->user()->id . '_' . time() . $mime_type;
            if (!in_array( strtolower($mime_type) ,['.jpeg', '.png', '.jpg'])) {
                return false;
            }
            if ($model->logo()->first()) {
                if (File::exists(public_path() . '/images/' . $model->logo()->first()->name)) {
                    File::delete(public_path() . '/images/' . $model->logo()->first()->name);
                }
                if (File::exists(public_path() . '/images/thumbnails/' . $model->logo()->first()->name)) {
                    File::delete(public_path() . '/images/thumbnails/' . $model->logo()->first()->name);
                }
                $model->logo()->delete();
            }

            $logo = Image::make(file_get_contents($request->image))->save(public_path() . '/images/' . $filename);
            $thumbnailLogo = $logo->resize(null, 150, function ($constraint) {
                $constraint->aspectRatio();
            });
            $thumbnailLogo->save(public_path() . '/images/thumbnails/' . $filename);
            if ($logo) {
                $result = $model->logo()->create([
                    'url' => asset('images/' . $filename),
                    'thumbnail_url' => asset('images/thumbnails/' . $filename),
                    'name' => $filename
                ]);
                return $result ? __('Image uploaded') : __('Image not uploaded');
            }
        }
        return false;
    }
}
