@extends("layouts.app")
@section("title", "Loteria koncertowa")
@section("subtitle", "Panel DJa")

@section("content")

<x-shipyard.app.section id="lottery-song"
    :icon="model_icon('compositions')"
    title="—"
    class="hidden"
>
    <x-slot:actions>
        <x-shipyard.ui.button action="none" class="tertiary" id="mark-composition-btn"
            label="Zagrane"
            icon="check"
            onclick="markCurrentComposition();"
        />
    </x-slot:actions>
</x-shipyard.app.section>

<div class="flex down" id="lottery-nav">
    <x-shipyard.app.loader />
    <div class="grid but-mobile-down hidden" style="--col-count: 2;">
        <x-shipyard.app.card
            title="Utwory"
            :icon="model_icon('compositions')"
        >
            <div class="flex down center middle">
                <strong class="accent primary" id="composition-name">—</strong>
                <small id="composition-pool">—</small>
            </div>

            <x-slot:actions>
                <x-shipyard.ui.button action="none" class="tertiary"
                    pop="Lista"
                    icon="format-list-bulleted"
                    onclick="toggleCompositionList();"
                />
                <x-shipyard.ui.button action="none" class="tertiary" id="roll-composition-btn"
                    pop="Losuj"
                    icon="dice-3"
                    onclick="roll('composition');"
                />
            </x-slot:actions>
        </x-shipyard.app.card>

        <x-shipyard.app.card
            title="Gatunki"
            :icon="model_icon('genres')"
        >
            <div class="flex down center middle">
                <strong class="accent primary" id="genre-name">—</strong>
                <small id="genre-pool">—</small>
            </div>

            <x-slot:actions>
                <x-shipyard.ui.button action="none" class="tertiary"
                    pop="Losuj"
                    icon="dice-3"
                    onclick="roll('genre');"
                />
            </x-slot:actions>
        </x-shipyard.app.card>
    </div>

    <div class="flex right spread and-cover">
        <x-shipyard.ui.button action="none" id="pick-btn" class="primary hidden"
            label="Wybierz"
            icon="arrow-up"
            onclick="pickComposition(data.rolled.composition);"
        />
    </div>

    <div id="compositions-list" class="hidden">
    </div>
</div>

@endsection

@section("prepends")
<script>
let data = {
    compositions: [],
    genres: [],
    rolled: {
        composition: null,
        genre: null,
    },
    picked: null,
    excludedCompositions: [],
};

// ⚓ functions ⚓ //
function init() {
    fetchComponent(
        `#lottery-nav .loader`,
        `/api/dj/lottery-mode`,
        {},
        [
            [`#composition-pool`, `compositionSummary`],
            [`#genre-pool`, `genreSummary`],
            [`#compositions-list`, `compositionsList`],
        ],
        (res) => {
            data = { ...data, ...res.data };
            document.querySelector(`#lottery-nav div.hidden`).classList.remove("hidden");
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
        || type == "composition" && (
            data.excludedCompositions.includes(rolled_id)
        )
    );

    const rolled = data_available[rolled_id];
    data.rolled[type] = rolled_id;
    document.querySelector(`#${type}-name`).innerHTML = typeof rolled == "object"
        ? rolled.full_title
        : rolled;

    document.querySelector(`#pick-btn`).classList.toggle(
        "hidden",
        !(data.rolled.composition !== null),
    );
}

function pickComposition(index) {
    const picked = data.compositions[index];

    document.querySelector(`#lottery-song .header .titles [role="texts"] [role="section-title"]`).innerHTML = picked.full_title;
    document.querySelector(`#lottery-song .contents`).innerHTML = picked.dj_preview;
    document.querySelector(`#lottery-song .header #mark-composition-btn`).classList.toggle("hidden", data.excludedCompositions.includes(index));

    data.picked = index;
    document.querySelector(`#lottery-song`).classList.remove("hidden");
    abcPreview("melody_preview");
}

function markCurrentComposition() {
    data.excludedCompositions.push(data.picked);
    document.querySelector(`#compositions-list`).children[data.picked].classList.add("ghost");
    data.picked = null;

    const new_count = data.compositions.length - data.excludedCompositions.length;
    document.querySelector(`#composition-pool`).innerHTML = `Dostępne: ${new_count}`;
    if (new_count < 2) {
        document.querySelector(`#roll-composition-btn`).classList.add("hidden");
    }

    document.querySelector(`#lottery-song`).classList.add("hidden");
    roll("composition");
}

function toggleCompositionList() {
    document.querySelector(`#compositions-list`).classList.toggle("hidden");
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
