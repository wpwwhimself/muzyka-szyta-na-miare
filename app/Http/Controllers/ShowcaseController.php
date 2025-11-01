<?php

namespace App\Http\Controllers;

use App\Models\ClientShowcase;
use App\Models\DjShowcase;
use App\Models\OrganShowcase;
use App\Models\Showcase;
use App\Models\ShowcasePlatform;
use App\Models\Song;
use App\Models\StatusChange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowcaseController extends Controller
{
    public function list(){
        $showcases = Showcase::orderBy("updated_at", "desc")->paginate(5);
        $organ_showcases = OrganShowcase::orderBy("updated_at", "desc")->paginate(5);
        $dj_showcases = DjShowcase::orderBy("updated_at", "desc")->paginate(5);
        $client_showcases = ClientShowcase::orderBy("updated_at", "desc")->paginate(5);

        $all_songs = Song::orderBy("title")
            ->orderBy("artist")
            ->orderBy("id")
            ->get()
            ->mapWithKeys(fn($s) => [$s["id"] => "$s[title] ($s[artist]) [$s[id]]"]);

        return view("pages.".user_role().".showcases", array_merge(
            ["title" => "Lista reklam"],
            compact("showcases", "organ_showcases", "dj_showcases", "client_showcases", "all_songs")
        ));
    }

    public function add(Request $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        Showcase::create([
            "song_id" => $rq->song_id,
            "link_fb" => (filter_var($rq->link_fb, FILTER_VALIDATE_URL)) ?
                "<a target='_blank' href='$rq->link_fb'>$rq->link_fb</a>" : $rq->link_fb,
            "link_ig" => $rq->link_ig,
        ]);

        return back()->with("toast", ["success", "Dodano pozycję"]);
    }

    public function addFromClient(Request $rq){
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);
        ClientShowcase::create([
            "song_id" => $rq->song_id,
            "embed" => $rq->embed,
        ]);

        return back()->with("toast", ["success", "Dodano pozycję"]);
    }

    public function pinComment(int $comment_id, int $client_id)
    {
        if(Auth::id() === 0) return back()->with("toast", ["error", OBSERVER_ERROR()]);

        StatusChange::where("changed_by", $client_id)->update(["pinned" => false]);
        StatusChange::find($comment_id)->update(["pinned" => true]);

        return back()->with("toast", ["success", "Zmieniono przypięcie"]);
    }

    #region platforms
    public function listPlatforms()
    {
        $platforms = ShowcasePlatform::orderBy("ordering")->get();

        return view("pages.".user_role().".showcases.platforms.list", compact(
            "platforms"
        ));
    }

    public function editPlatform($id = null)
    {
        $platform = ($id)
            ? ShowcasePlatform::find($id)
            : null;

        return view("pages.".user_role().".showcases.platforms.edit", compact(
            "platform"
        ));
    }

    public function processPlatform(Request $rq)
    {
        if ($rq->action == "save") {
            ShowcasePlatform::updateOrCreate(["code" => $rq->code], $rq->except(["_token", "action"]));
        } else if ($rq->action == "delete") {
            ShowcasePlatform::find($rq->code)->delete();
        }
        return redirect()->route("showcase-platform-edit", ["id" => $rq->code])->with("toast", ["success", "Platforma poprawiona"]);

    }
    #endregion

    #region organ
    public function editOrgan(?OrganShowcase $showcase = null)
    {
        $showcase_platforms = ShowcasePlatform::orderBy("ordering")->get()
            ->map(fn ($p) => ["value" => $p->code, "label" => $p->name]);

        $platform_suggestion = ShowcasePlatform::suggest(true);
        if (!$showcase && $platform_suggestion) {
            $showcase_platforms = $showcase_platforms->map(fn ($p) =>
                $p["value"] == $platform_suggestion["code"]
                    ? [...$p, "label" => ($p["label"] . " (sugerowana)")]
                    : $p
            );
        }

        return view("pages.".user_role().".showcases.organ.edit", compact(
            "showcase",
            "showcase_platforms",
            "platform_suggestion",
        ));
    }

    public function processOrgan(Request $rq)
    {
        if ($rq->action == "save") {
            $showcase = OrganShowcase::updateOrCreate(
                ["id" => $rq->id],
                $rq->except(["_token", "action"])
            );
            return redirect()->route("organ-showcase-edit", ["showcase" => $showcase])->with("toast", ["success", "Rolka poprawiona"]);
        } else if ($rq->action == "delete") {
            OrganShowcase::find($rq->id)->delete();
            return redirect()->route("showcases")->with("toast", ["success", "Rolka usunięta"]);
        }
    }
    #endregion

    #region dj
    public function editDj(?DjShowcase $showcase = null)
    {
        $showcase_platforms = ShowcasePlatform::orderBy("ordering")->get()
            ->map(fn ($p) => ["value" => $p->code, "label" => $p->name]);

        $platform_suggestion = ShowcasePlatform::suggest(true);
        if (!$showcase && $platform_suggestion) {
            $showcase_platforms = $showcase_platforms->map(fn ($p) =>
                $p["value"] == $platform_suggestion["code"]
                    ? [...$p, "label" => ($p["label"] . " (sugerowana)")]
                    : $p
            );
        }

        return view("pages.".user_role().".showcases.dj.edit", compact(
            "showcase",
            "showcase_platforms",
            "platform_suggestion",
        ));
    }

    public function processDj(Request $rq)
    {
        if ($rq->action == "save") {
            $showcase = DjShowcase::updateOrCreate(
                ["id" => $rq->id],
                $rq->except(["_token", "action"])
            );
            return redirect()->route("dj-showcase-edit", ["showcase" => $showcase])->with("toast", ["success", "Rolka poprawiona"]);
        } else if ($rq->action == "delete") {
            DjShowcase::find($rq->id)->delete();
            return redirect()->route("showcases")->with("toast", ["success", "Rolka usunięta"]);
        }
    }
    #endregion
}
