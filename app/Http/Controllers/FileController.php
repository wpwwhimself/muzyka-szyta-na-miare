<?php

namespace App\Http\Controllers;

use App\Models\File as ModelsFile;
use App\Models\FileTag;
use App\Models\Quest;
use App\Models\Song;
use App\Models\User;
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
    public function listTags()
    {
        $tags = FileTag::orderBy("name")->get();

        return view(user_role().'.files.tags.list', array_merge(
            ["title" => "Pliki"],
            compact("tags"),
        ));
    }

    public function editTag(int $id = null): View
    {
        $tag = FileTag::find($id);

        return view(user_role().'.files.tags.edit', array_merge(
            ["title" => ($tag) ? "$tag->name | Edytuj tag" : "Dodaj tag"],
            compact("tag"),
        ));
    }

    public function processTag(Request $rq): RedirectResponse
    {
        if ($rq->action == "save") {
            $tag = FileTag::updateOrCreate(["id" => $rq->id], $rq->except("_token"));
            return redirect()->route("file-tag-edit", ["id" => $tag->id])->with("success", "Tag poprawiony");
        } else if ($rq->action == "delete") {
            FileTag::find($rq->id)->delete();
            return redirect()->route("file-tags")->with("success", "Tag usunięty");
        }
    }
    #endregion

    #region upload
    public function uploadForQuest(string $quest_id)
    {
        $song = Quest::find($quest_id)->song;
        $tags = FileTag::orderBy("name")->get();
        $clients = User::clients()->get()
            ->mapWithKeys(fn ($c) => [$c->id => _ct_("$c->client_name «$c[id]»")])
            ->toArray();
        $file = null;
        $existing_files = ModelsFile::where("song_id", $song->id)->get();

        return view(user_role().'.files.edit', compact(
            "song",
            "file",
            "tags",
            "clients",
            "existing_files",
        ));
    }

    public function edit(int $id): View
    {
        $file = ModelsFile::findOrFail($id);
        $tags = FileTag::orderBy("name")->get();
        $clients = User::clients()->get()
            ->mapWithKeys(fn ($c) => [$c->id => _ct_("$c->client_name «$c[id]»")])
            ->toArray();
        $song = null;
        $existing_files = ModelsFile::where("song_id", $file->song_id)->get();

        return view(user_role().'.files.edit', compact(
            "song",
            "file",
            "tags",
            "clients",
            "existing_files",
        ));
    }

    public function process(Request $rq)
    {
        $song = Song::find($rq->song_id);
        $file = ModelsFile::find($rq->id);

        $uploaded_files = [];

        if ($rq->action == "save") {
            if ($file) {
                $uploaded_files = $file->file_paths;

                // delete existing chosen files
                foreach ($rq->delete_files ?? [] as $extension => $path) {
                    Storage::delete($path);
                    unset($uploaded_files[$extension]);
                }
            }

            // upload files
            foreach ($rq->file("files") ?? [] as $file) {
                $uploaded_files[$file->getClientOriginalExtension()] = $file->storeAs(
                    "safe/$song->id",
                    Str::random(8).".".$file->getClientOriginalExtension()
                );
            }

            // upsert database entry
            $file = ModelsFile::updateOrCreate([
                "id" => $rq->id,
            ], [
                "song_id" => $song->id,
                "variant_name" => $rq->variant_name ?? "podstawowy",
                "version_name" => $rq->version_name ?? "wersja główna",
                "transposition" => $rq->transposition,
                "only_for_client_id" => $rq->only_for_client_id,
                "description" => $rq->description,
                "file_paths" => $uploaded_files,
            ]);
            $file->tags()->sync(array_keys($rq->tags ?? []));
        } else if ($rq->action == "delete") {
            foreach ($rq->delete_files ?? [] as $extension => $path) {
                Storage::delete($path);
            }
            $file->delete();
            return redirect()->route("songs", ["search" => $song->id])->with('success', 'Pliki usunięte');
        }

        return redirect()->route("files-edit", ["id" => $file->id])->with('success', 'Pliki wgrane');
    }

    public function addFromExisingSafe(string $song_id)
    {
        $song = Song::find($song_id);
        $files = Storage::allFiles("safe/$song_id");
        $tags = FileTag::orderBy("name")->get();
        $clients = User::clients()->get()
            ->mapWithKeys(fn ($c) => [$c->id => _ct_("$c->client_name «$c[id]»")])
            ->toArray();
        $existing_files = ModelsFile::where("song_id", $song_id)->get();

        return view(user_role().'.files.add-from-existing-safe', compact(
            "song",
            "files",
            "clients",
            "tags",
            "existing_files",
        ));
    }

    public function addFromExistingSafeProcess(Request $rq)
    {
        $song = Song::find($rq->song_id);

        if ($rq->action == "save") {
            $file = ModelsFile::updateOrCreate([
                "id" => $rq->existing_file_id,
            ], [
                "song_id" => $song->id,
                "variant_name" => $rq->variant_name ?? "podstawowy",
                "version_name" => $rq->version_name ?? "wersja główna",
                "transposition" => $rq->transposition,
                "only_for_client_id" => $rq->only_for_client_id,
                "description" => $rq->description,
                "file_paths" => collect(array_keys($rq->file_to_recycle ?? []))
                    ->mapWithKeys(fn($path) => [pathinfo($path, PATHINFO_EXTENSION) => Str::replaceArray("$", ["[", "]"], $path)])
                    ->toArray(),
            ]);
            $file->tags()->sync(array_keys($rq->tags ?? []));
        }

        return back()->with("success", "Wpis dodany");
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
