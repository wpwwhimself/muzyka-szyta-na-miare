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
    @foreach (\App\Models\Composition::forConnection()->get() as $composition)
    <li class="hidden interactive highlight"
        data-id="{{ $composition->id }}"
        data-title="{{ $composition->title ?? 'Bez tytułu' }}"
        data-composer="{{ $composition->composer }}"
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
        console.error(`⚠️ Zestaw próbuje wywołać kompozycję, która nie istnieje: ${id}`);
        return;
    }

    const li = document.createElement("li");
    li.classList.add("interactive", "highlight");
    li.innerHTML = `
        <a href="/admin/models/compositions/edit/${id}" target="_blank">
            ${composition.dataset.title} <small class="ghost">${composition.dataset.composer}</small>
        </a>
        <span class="interactive accent danger" onclick="cpRemoveComposition(this.closest('li'))">×</span>
    `;
    li.dataset.id = id;
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
            !(new RegExp(query, "i").test(`${hint.dataset.title} ${hint.dataset.composer}`))
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
