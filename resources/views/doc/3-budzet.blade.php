<h1>Zarządzanie budżetem</h1>

<h2>Co to jest budżet?</h2>
<p>
    Klient może wpłacić środki na poczet budżetu w dwóch sytuacjach:
    <ol>
        <li>wpłacił za dużo na poczet zlecenia</li>
        <li>zapłacił za zlecenie, odrzucił je, ale nie chce zwrotu</li>
    </ol>
    Wówczas rzeczone środki są podliczane w ramach <b>budżetu</b>.
    Ten może zostać ponownie wykorzystany do opłacenia przyszłych zleceń.
</p>

<p>
    Wszystkie zmiany w budżecie są odzwierciedlone za pomocą specjalnych statusów wpłat
    <x-phase-indicator-mini :status="\App\Models\Status::find(32)" />,
    w których nie określono zlecenia. Wobec tego takie wpłaty widnieją również na
    <a href="{{ route('finance-summary') }}">bilansie konta</a>.
</p>

<h2>Jak można zmienić budżet?</h2>
<ul>
    <li>
        <b>ręcznie, w edycji klienta</b>:
        w polu <i>budżet</i> można podać kwotę, jaką obecnie Klient dysponuje.
        Wprowadzenie zmian poskutkuje dodaniem zmiany statusu budżetu
        <x-phase-indicator-mini :status="\App\Models\Status::find(32)" />
        z datą obecną.
    </li>
    <li>
        <b>automatycznie, przy okazji wpłaty na poczet zlecenia</b>:
        jeśli kwota wpłaty jest wyższa niż kwota brakująca,
        nadpłata zostanie wpisana na poczet budżetu.
        Dodana zostanie również zmiana statusu budżetu
        <x-phase-indicator-mini :status="\App\Models\Status::find(32)" />
        z datą obecną.
    </li>
</ul>

<h2>Jak można spożytkować budżet?</h2>
<p>
    Zapytania w fazie <x-phase-indicator-mini :status="\App\Models\Status::find(5)" />
    wskazują, czy budżet klienta może pokryć kwotę zlecenia (częściowo/w całości).
    Jeśli tak jest, a Klient zaakceptuje wycenę, to przy okazji tworzenia zlecenia wpisywane są 2 statusy wpłat
    <x-phase-indicator-mini :status="\App\Models\Status::find(32)" />:
    <ol>
        <li>ujemny, odejmujący kwotę od budżetu</li>
        <li>dodatni, dodający kwotę do zlecenia</li>
    </ol>
</p>
