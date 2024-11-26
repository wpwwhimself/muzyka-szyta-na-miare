<?php

namespace App\Http\Controllers;

use App\Models\FileTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class FileController extends Controller
{
    #region dashboard
    public function dashboard()
    {
        $tags = FileTag::orderBy("name")->get();

        return view(user_role().'.files.dashboard', array_merge(
            ["title" => "Pliki"],
            compact("tags"),
        ));
    }

    public function editTag(int $id = null): View
    {
        $tag = FileTag::find($id);

        return view(user_role().'.files.edit-tag', array_merge(
            ["title" => ($tag) ? "$tag->name | Edytuj tag" : "Dodaj tag"],
            compact("tag"),
        ));
    }

    public function processTag(Request $rq): RedirectResponse
    {
        if ($rq->action == "save") {
            FileTag::updateOrCreate(["id" => $rq->id], $rq->except("_token"));
        } else if ($rq->action == "delete") {
            FileTag::find($rq->id)->delete();
        }
        return redirect()->route("files-dashboard")->with("success", "Tag poprawiony");
    }
    #endregion

    // https://gist.github.com/zahidhasanemon/afbbf65918703f0e897db518dd77f2ce, modified
    public function fileUpload(Request $rq, $id){
        foreach ($rq->file('file') as $key => $value) {
            $filename = $value->getClientOriginalName();
            $name[] = $filename;
            $value->storeAs("safe/$id", $filename);
        }

        return response()->json([
            'name' => $name,
        ]);
    }

    public function fileStore(Request $rq){
        return back()->with('success', 'Pliki wgrane');
    }

    public function show($id, $filename)
    {
        $path = storage_path("app/safe/$id/$filename");

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

    public function verDescGet(Request $rq)
    {
        return Storage::get($rq->path) ?? "";
    }

    public function verDescMod(Request $rq){
        $path = "/safe/$rq->ver.md";
        if($rq->desc == ""){
            Storage::delete($path);
            return back()->with("success", "Opis usunięty");
        }

        Storage::put($path, $rq->desc);
        return back()->with("success", "Opis dodany");
    }

    public function showcaseFileUpload(Request $rq){
        $path = $rq->file("showcase_file")
            ->storeAs("showcases", $rq->id.".ogg")
        ;
    
        return back()->with("success", "Showcase dodany");
    }

    public function showcaseFileShow($id){

        $path = storage_path("app/showcases/$id.ogg");

        if(!File::exists($path)) abort(404,"Plik nie istnieje");

        $file = Storage::get("showcases/$id.ogg");
        $type = File::mimeType($path);
        $filesize = Storage::size("showcases/$id.ogg");

        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => $type,
            'Content-Disposition' => "attachment; filename=$id.ogg",
            'Content-Length' => $filesize,
            'Pragma' => 'public',
            'Cache-Control' => 'must-revalidate',
            'Expires' => '0',
        ];

        return (new Response($file, 200, $headers));
    }
}
