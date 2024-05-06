<h1>Filtrowanie list</h1>

<p>
    Listy <a href="{{ route('quests') }}">zleceń</a> oraz <a href="{{ route('requests') }}">zapytań</a> można filtrować,
    aby wyciągnąć z nich konkretne wpisy.
    Do tego celu służą dodatkowe parametry dopisywane w adresie strony.
</p>

<h2>Rozpoznawane parametry</h2>

<ul>
    <li>
        <code>client</code>
        - wyszukiwanie po ID klienta
    </li>
    <li>
        <code>client_name</code>
        - wyszukiwanie po nazwisku klienta <span class="yellowed-out">tylko dla zapytań</span>
    </li>
    <li>
        <code>status</code>
        - wyszukiwanie po ID fazy
    </li>
</ul>
