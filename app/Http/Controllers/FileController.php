<?php

namespace App\Http\Controllers;

use App\Models\Quest;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    public function fileUpload(Request $rq){
        foreach($rq->files as $file){
            $file = $rq->file("file");
            $filename = $file->getClientOriginalName();
            $file->storeAs("safe/$rq->quest_id", $filename);
        }

        return back()->with("success", "Pliki wgrane");
    }

    public function show($id, $filename)
    {
        $path = storage_path("app/safe/$id/$filename");

        //if(!quest_paid($id, Quest::find($id)->price)) abort(403, "Projekt nie został opłacony");
        if(!File::exists($path)) abort(404,"Plik nie istnieje");
        
        $file = Storage::get("safe/$id/$filename");
        $type = File::mimeType($path);
        $filesize = Storage::size("safe/$id/$filename");   
        
        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => $type,
            'Content-Disposition' => "attachment; filename=$filename",
            'Content-Length' => $filesize,
            'Pragma' => 'public',
            'Cache-Control' => 'must-revalidate',
            'Expires' => '0',
        ];

        return (new Response($file, 200, $headers));
    }

    public function fileDownload($id, $filename){
        return Storage::download("safe/$id/$filename");
    }
}
