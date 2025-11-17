<x-shipyard.app.phase-bar
    total="9"
    :current="$statusId % 10"
    :color="$statusColor($statusId)"
>
    @if ($small)
    <p>
        <x-shipyard.app.icon :name="$statusSymbol($statusId)" />
        {{ $statusName($statusId) }}
    </p>
    @else
    <span>Status:</span>
    <h3>
        <x-shipyard.app.icon :name="$statusSymbol($statusId)" />
        {{ $statusName($statusId) }}
    </h3>
    <x-tutorial>
        {{ [
            1 => "Twoje zapytanie zostało wysłane. W&nbsp;najbliższym czasie (może nawet jutro) odniosę się do niego i&nbsp;przygotuję odpowiednią wycenę. Zostaniesz o&nbsp;tym poinformowany w&nbsp;wybrany przez Ciebie sposób.",
            4 => "Nie podejmę się wykonania tego zlecenia. Prawdopodobnie jest ono dla mnie niewykonalne.",
            5 => "Wyceniłem Twoje zapytanie. Możesz potwierdzić przedstawione warunki lub – jeśli się z nimi nie zgadzasz – przesłać mi do ponownej wyceny z opisem, co się nie zgadza. Ostatecznie możesz zupełnie odrzucić warunki.",
            6 => "Twoje poprawki zostały przekazane. Odniosę się do nich i przedstawię poprawioną wycenę.",
            7 => "Termin ważności wyceny minął. Jeśli nadal chcesz zrealizować to zlecenie, kliknij przycisk poniżej.",
            8 => "Ta wycena została przez Ciebie odrzucona. Coś musiało pójść nie tak lub coś Ci się nie spodobało.",
            9 => "Zapytanie zostało przyjęte. Utworzyłem zlecenie, do którego link znajdziesz poniżej.",
            11 => "Twoje zlecenie zostało przyjęte. Wkrótce rozpocznę nad nim pracę.",
            12 => "Dobre wyczucie, właśnie prowadzę prace nad Twoim zleceniem. W ciągu kolejnych godzin możesz spodziewać się wiadomości na temat postępów.",
            13 => "Prace nad zleceniem zostały zawieszone. Nadal mogę do niego wrócić, ale na razie leży odłożony i czeka na swój czas.",
            14 => "Obecny etap prac został przez Ciebie przyjęty. Wkrótce dostarczę dalszą część materiałów.",
            15 => "Do Twojego zlecenia zostały dodane nowe pliki. Poniżej możesz je przeglądać i wyrazić swoją opinię na ich temat.",
            16 => "Twoje uwagi zostały przekazane. Odniosę się do nich i przygotuję coś nowego wkrótce.",
            17 => "Twoje zlecenie wygasło z powodu zbyt powolnych postępów.",
            18 => "Zlecenie zostało przez Ciebie odrzucone. Coś musiało pójść nie tak lub coś Ci się nie spodobało.",
            19 => "Zlecenie zostało przez Ciebie przyjęte bez zarzutów. Cieszę się, że mogłem coś dla Ciebie przygotować i polecam się do dalszych zleceń.",
            21 => "W tym zleceniu została zgłoszona chęć wprowadzenia zmian. Wkrótce je zweryfikuję i wprowadzę odpowiednie poprawki.",
            26 => "Twoje zlecenie zostało przywrócone – w najbliższym czasie skontaktuję się z Tobą z nowymi plikami lub też zmianami w wycenie.",
            31 => "Wycena dla tego zlecenia musiała zostać zmieniona. Aby prace mogły postępować dalej, musisz je zaakceptować.",
            95 => "Potrzebuję dodatkowych informacji. Odpowiedz na moje pytania (zawarte w historii) za pomocą przycisku poniżej.",
            96 => "Odpowiedź została wysłana. Odniosę się do nich i wrócę z odpowiedzią.",
        ][$statusId] }}
    </x-tutorial>
    @endif
</x-shipyard.app.phase-bar>