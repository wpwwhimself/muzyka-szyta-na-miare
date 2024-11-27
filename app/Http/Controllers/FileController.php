<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\File as ModelsFile;
use App\Models\FileTag;
use App\Models\Quest;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

    #region upload
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

    public function uploadForQuest(string $quest_id)
    {
        $quest = Quest::find($quest_id);
        $tags = FileTag::orderBy("name")->get();
        $clients = Client::orderBy("client_name")->get()->pluck("client_name", "id");

        return view(user_role().'.files.upload', compact(
            "quest",
            "tags",
            "clients",
        ));
    }

    public function processUpload(Request $rq)
    {
        $quest = Quest::find($rq->quest_id);
        $song = $quest?->song ?? Song::find($rq->song_id);

        $uploaded_files = [];

        if ($rq->action == "save") {
            // upload files
            foreach ($rq->file("files") as $file) {
                $uploaded_files[$file->getClientOriginalExtension()] = $file->storeAs(
                    "safe/$song->id",
                    Str::random(8).".".$file->getClientOriginalExtension()
                );
            }

            // upsert database entry
            $file = ModelsFile::updateOrCreate([
                "song_id" => $song->id,
                "variant_name" => $rq->variant_name ?? "podstawowy",
                "version_name" => $rq->version_name ?? "wersja główna",
            ], [
                "transposition" => $rq->transposition,
                "only_for_client_id" => $rq->only_for_client_id,
                "description" => $rq->description,
                "file_paths" => $uploaded_files,
            ]);
            $file->tags()->sync(array_keys($rq->tags ?? []));
        }

        return redirect()->route("quest", ["id" => $quest->id])->with('success', 'Pliki wgrane');
    }
    #endregion

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
