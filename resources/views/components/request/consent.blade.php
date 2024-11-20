@props([
    "request",
])

<p>
    Przejrzyj poniższe dane dotyczące zlecenia.
    Jeśli chcesz coś <strong>dodać lub poprawić</strong>, kliknij odpowiedni przycisk.
    W przeciwnym razie zaznacz wszystkie zgody.
</p>

<div id="opinion-stage-1">
    <table>
        <thead>
            <tr>
                <th></th>
                <th>Akceptuję</th>
                <th>Zmieniam</th>
                <th>Zobacz</th>
            </tr>
        </thead>
        <tbody>
            @foreach (array_filter([
                $request->link ? ['link', 'Link do nagrania'] : null,
                ['wishes', 'Życzenia'],
                ['deadline', 'Termin wykonania'],
                $request->delayed_payment ? ['delayed_payment', 'Opóźnienie wpłaty'] : null
            ]) as [$name, $label])
            <tr>
                <td>{{ $label }}</td>
                <td><x-input type="radio" name="consent_{{ $name }}" value="1" label="" /></td>
                <td><x-input type="radio" name="consent_{{ $name }}" value="0" label="" :disabled="$name == 'delayed_payment'" /></td>
                <td><x-button action="#/" class="consent_jump" data-field="{{ $name }}" icon="angles-up" label="" /></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <x-button label="Potwierdź" icon="check" action="#/" />
</div>
<div id="opinion-stage-2" class="flex-down spaced gone">
    <input type="hidden" name="optbc">

    <div id="opinion-inputs">
        <x-input type="text" name="opinion_link" label="Podaj nowy link do nagrania" :value="$request->link" />
    
        <x-input type="TEXT" name="opinion_wishes" label="Podaj nowe życzenia" :value="$request->wishes" />
    
        <div class="priority" for="opinion_deadline">
            <p>W trybie priorytetowym jestem w stanie wykonać zlecenie poza kolejnością; wiąże się to jednak z podwyższoną ceną.</p>
            <div class="flex-right center">
                <x-input type="date" name="new-deadline-date" label="Nowy termin, do kiedy (włącznie) oddam pliki" :value="get_next_working_day()->format('Y-m-d')" :disabled="true" />
                <x-input type="text" name="new-deadline-price" label="Nowa cena zlecenia" :value="as_pln(price_calc($request->price_code.'z', $request->client_id, true)['price'])" :disabled="true" />
            </div>
        </div>
    </div>

    <x-input for="opinion_link opinion_wishes opinion_nothing" type="TEXT" name="comment" label="Komentarz (opcjonalne)" />

    <div id="opinion-submit-buttons" class="flex-right center">
        <x-button action="#/" icon="angle-left" label="Wróć" onclick="revealOpinionStage(1)" />
        <x-button for="opinion_link opinion_wishes" action="submit" icon="6" name="new_status" value="6" label="Oddaj do ponownej wyceny" />
        <x-button for="opinion_nothing" action="submit" icon="8" name="new_status" value="8" label="Zrezygnuj z zapytania" />
        <x-button for="opinion_deadline" action="{{ route('request-final', ['id' => $request->id, 'status' => 9, 'with_priority' => true]) }}" icon="9" label="Zaakceptuj nową wycenę" :danger="true" />
    </div>
</div>

<style>
#phases table {
    width: fit-content;
    margin: auto;
}
</style>

<script defer>
document.querySelectorAll(".consent_jump").forEach((btn) => {
    btn.addEventListener("click", (el) => {
        const input = document.querySelector(`#${el.currentTarget.getAttribute("data-field")}`)
        input.scrollIntoView({ behavior: "smooth", block: "center" })
        highlightInput(input)
    })
})

const revealOpinionStage = (number) => {
    document.querySelectorAll(`[id^=opinion-stage-]`).forEach(el => el.classList.add("gone"));
    document.querySelector(`#opinion-stage-${number}`).classList.remove("gone");
}
const revealOpinionInput = (name) => {
    document.querySelectorAll(`#opinion-inputs [for^=opinion_]`).forEach(el => el.classList.add("gone"));
    document.querySelector(`#opinion-inputs [for=opinion_${name}]`).classList.remove("gone");
    document.querySelector("#opinion-inputs").classList.remove("gone");
}

document.querySelector("#opinion-stage-1 > a:last-of-type").addEventListener("click", (el) => {
    let all_good = true

    document.querySelectorAll("input[name^='consent_']").forEach((consent) => {
        if (consent.checked) {
            if (consent.value == 1) return
            
            revealOpinionInput(consent.id.replace("consent_", ""))
            all_good = false
        }
    })

    if (all_good) {
        window.location.href = "{{ route('request-final', ['id' => $request->id, 'status' => 5]) }}"
        return
    }

    revealOpinionStage(2)
});
</script>