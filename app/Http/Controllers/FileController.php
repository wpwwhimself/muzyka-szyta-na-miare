<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function fileUpload(Request $rq){
        $file = $rq->file("file");
        $filename = $file->getClientOriginalName();
        $file->storeAs("safe/$rq->quest_id", $filename);

        return back()->with("success", "Plik wgrany");
    }

    public function fileDownload($name){
        $file = Storage::download($name);
    }
}
