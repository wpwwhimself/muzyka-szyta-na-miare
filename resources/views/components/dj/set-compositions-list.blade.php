@props([
    "data", // set
])

@if ($data->id)
<input type="hidden" name="compositions" value="{{ $data->compositions->pluck("id")->join(",") }}">

<ol id="cp-list"></ol>

<x-shipyard.ui.input type="text"
    name="cp-search"
    placeholder="Szukaj..."
    icon="magnify"
    oninput="cpFilter(event.target.value)"
/>

<ul class="hints">
    @foreach (\App\Models\Composition::getDjReady() as $composition)
    <li class="hidden interactive highlight"
        data-id="{{ $composition->id }}"
        data-q="{{ $composition->full_title }}"
        onclick="cpAddComposition({{ $composition->id }})"
    >
        {{ $composition->title }}
        <small class="ghost">{{ $composition->composer }}</small>
    </li>
    @endforeach
</ul>

<script>
const field = document.querySelector("[name=compositions]");
const list = document.getElementById("cp-list");
const hints = Array.from(document.querySelectorAll(".hints > *"));

function cpAddToList(id) {
    const composition = hints.find(hint => hint.dataset.id == id);
    if (!composition) {
        console.error(`⚠️ Zestaw próbuje wywołać kompozycję, która nie jest gotowa na DJa: ${id}`);
        return;
    }

    const li = document.createElement("li");
    li.classList.add("interactive", "highlight");
    li.innerHTML = composition.dataset.q;
    li.dataset.id = id;
    li.onclick = function (ev) { cpRemoveComposition(this); };
    list.appendChild(li);
}

function cpRemoveFromList(el) {
    el.remove();
}

function cpUpdateList() {
    field.value = Array.from(list.children).map(el => el.dataset.id).join(",");
}

function cpInitList() {
    field.value.split(",").forEach(id => { cpAddToList(id); });
}

function cpAddComposition(id) {
    cpAddToList(id);
    cpFilter("");
    cpUpdateList();
}

function cpRemoveComposition(el) {
    cpRemoveFromList(el);
    cpUpdateList();
}

function cpFilter(query) {
    hints.forEach(hint => {
        hint.classList.toggle(
            "hidden",
            !(new RegExp(query, "i").test(hint.dataset.q))
                || query === ""
        );
    });

    if (query === "") {
        document.querySelector("[name='cp-search']").value = "";
    }
}

cpInitList();
</script>

@else
<span class="accent danger">
    Zapisz zestaw, aby wypełnić listę utworów.
</span>

@endif
