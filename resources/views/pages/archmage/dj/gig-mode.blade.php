@extends("layouts.shipyard.admin")
@section("title", "Panel DJa")
@section("subtitle", "Koncert")

@section("content")

<x-shipyard.app.section id="gig-song"
    :icon="model_icon('compositions')"
    title="—"
    subtitle="—"
    class="hidden"
>
    <x-slot:actions>
        <x-shipyard.ui.button action="none" class="tertiary" id="set-prev-btn"
            icon="step-backward"
            onclick="songPrev();"
        />
        <x-shipyard.ui.button action="none" class="tertiary" id="set-next-btn"
            icon="step-forward"
            onclick="songNext();"
        />
        <x-shipyard.ui.button action="none" class="tertiary" id="mark-set-btn"
            label="Zagrane"
            icon="check"
            onclick="markCurrentSet();"
        />
    </x-slot:actions>
</x-shipyard.app.section>

<div class="flex down" id="gig-nav">
    <x-shipyard.app.loader />
    <div class="grid but-mobile-down hidden" style="--col-count: 2;">
        <x-shipyard.app.card
            title="Zestawy"
            :icon="model_icon('dj-sets')"
        >
            <div class="flex down center middle">
                <strong class="accent primary" id="set-name">—</strong>
                <small id="set-pool">—</small>
            </div>

            <x-slot:actions>
                <x-shipyard.ui.button action="none" class="tertiary"
                    pop="Lista"
                    icon="format-list-bulleted"
                    onclick="toggleSetList();"
                />
                <x-shipyard.ui.button action="none" class="tertiary" id="roll-set-btn"
                    pop="Losuj"
                    icon="dice-3"
                    onclick="roll('set');"
                />
            </x-slot:actions>
        </x-shipyard.app.card>
    </div>

    <div class="flex right spread and-cover">
        <x-shipyard.ui.button action="none" id="pick-btn" class="primary hidden"
            label="Wybierz"
            icon="arrow-up"
            onclick="pickSet(data.rolled.set);"
        />
    </div>

    <div id="sets-list" class="hidden">
    </div>
</div>

@endsection

@section("prepends")
<script>
let data = {
    sets: [],
    rolled: {
        set: null,
    },
    picked: null,
    currentSong: null,
    excludedSets: [],
};

// ⚓ functions ⚓ //
function init() {
    fetchComponent(
        `#gig-nav .loader`,
        `/api/dj/gig-mode`,
        {},
        [
            [`#set-pool`, `setSummary`],
            [`#sets-list`, `setsList`],
        ],
        (res) => {
            data = { ...data, ...res.data };
            document.querySelector(`#gig-nav div.hidden`).classList.remove("hidden");
        },
    )
}

function roll(type) {
    const data_available = data[type + "s"];
    const length = typeof data_available == "object"
        ? Object.keys(data_available).length
        : data_available.length;
    let rolled_id = null;

    do {
        rolled_id = Math.floor(Math.random() * length);
    } while (
        data_available[rolled_id] === undefined
        || rolled_id == data.rolled[type]
        || type == "set" && (
            data.excludedSets.includes(rolled_id)
        )
    );

    const rolled = data_available[rolled_id];
    data.rolled[type] = rolled_id;
    document.querySelector(`#${type}-name`).innerHTML = typeof rolled == "object"
        ? `${rolled.id} ${rolled.name}`
        : rolled;

    document.querySelector(`#pick-btn`).classList.toggle(
        "hidden",
        !(data.rolled.composition !== null),
    );
}

function fillSong(title, set_name, hide_prev, hide_next, hide_mark, preview) {
    document.querySelector(`#gig-song .header .titles [role="texts"]`).innerHTML = `<h2 role="section-title">—</h2>${set_name}`;
    document.querySelector(`#gig-song .header .titles [role="texts"] [role="section-title"]`).innerHTML = title;
    document.querySelector(`#gig-song .header #set-prev-btn`).classList.toggle("disabled", hide_prev);
    document.querySelector(`#gig-song .header #set-next-btn`).classList.toggle("disabled", hide_next);
    document.querySelector(`#gig-song .header #mark-set-btn`).classList.toggle("hidden", hide_mark);
    document.querySelector(`#gig-song .contents`).innerHTML = preview;

    document.querySelectorAll(`[name^="melody_preview_"]`).forEach(el => abcPreview(el.name));
}

function pickSet(index) {
    const picked = data.sets[index];
    console.log(picked);
    const firstSong = picked.compositions[0];

    fillSong(firstSong.full_title, `${picked.id} ${picked.name}`, true, false, data.excludedSets.includes(index), firstSong.dj_preview);
    data.picked = index;
    data.currentSong = 0;
    document.querySelector(`#gig-song`).classList.remove("hidden");
}

function songPrev() {
    const picked = data.sets[data.picked];
    const prevSong = picked.compositions[data.currentSong - 1];
    const prevSongIsFirst = data.currentSong == 1;

    if (!prevSong) {
        console.error("No prev song available");
        return;
    }

    fillSong(prevSong.full_title, `${picked.id} ${picked.name}`, prevSongIsFirst, false, data.excludedSets.includes(data.picked), prevSong.dj_preview);
    data.currentSong -= 1;
}

function songNext() {
    const picked = data.sets[data.picked];
    const nextSong = picked.compositions[data.currentSong + 1];
    const nextSongIsLast = data.currentSong == picked.compositions.length - 2;

    if (!nextSong) {
        console.error("No next song available");
        return;
    }

    fillSong(nextSong.full_title, `${picked.id} ${picked.name}`, false, nextSongIsLast, data.excludedSets.includes(data.picked), nextSong.dj_preview);
    data.currentSong += 1;
}

function markCurrentSet() {
    data.excludedSets.push(data.picked);
    document.querySelector(`#sets-list`).children[data.picked].classList.add("ghost");
    data.picked = null;

    const new_count = data.sets.length - data.excludedSets.length;
    document.querySelector(`#set-pool`).innerHTML = `Dostępne: ${new_count}`;
    if (new_count < 2) {
        document.querySelector(`#roll-set-btn`).classList.add("hidden");
    }

    document.querySelector(`#gig-song`).classList.add("hidden");
    roll("set");
}

function toggleSetList() {
    document.querySelector(`#sets-list`).classList.toggle("hidden");
}
// ⚓ functions ⚓ //
</script>

<style>
.lyrics{
	display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    column-gap: 2em;
}
.lyrics li{
	width: -moz-fit-content;
	width: fit-content;
	margin-bottom: 10px;

    &.lettered {
        list-style-type: none;

        & > .letter {
            position: absolute;
            margin-left: -1em;
        }
    }
}
.chorus{
    font-weight: bold;
}
.tabbed{
    display: block;
    margin-left: 1em;
}
</style>
@endsection

@section("appends")
<script defer>
init();
</script>
@endsection
