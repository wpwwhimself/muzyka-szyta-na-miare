<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;

class FileController extends Controller
{
    public function fileUpload(Request $rq){
        $file = $rq->file("file");
        $filename = $file->getClientOriginalName();
        $file->storeAs("safe/$rq->quest_id", $filename);

        return back()->with("success", "Plik wgrany");
    }

    public function show($id, $filename)
    {
        $path = storage_path("safe/$id/$filename");
        dd($path);

        if(!File::exists($path)) abort(404);

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function fileDownload($id, $filename){
        return Storage::download("safe/$id/$filename");
    }
}
