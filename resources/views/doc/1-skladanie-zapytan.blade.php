<h1>Składanie zapytań</h1>

<h2>Skąd wpływają zapytania</h2>

<p>
    Klient może złożyć zapytanie na trzy sposoby:
</p>
<ol>
    <li>
        <a href="{{ route('home') }}#contact">formularz kontaktowy na stronie domowej</a>
        – dostępny dla Klienta z kontem i bez niego,
    </li>
    <li>
        <a href="{{ route('add-request') }}">formularz nowego zapytania</a>
        – dostępny dla zalogowanego Klienta,
    </li>
    <li>
        poza formularzami: mailowo, telefonicznie lub inną drogą
        – wtedy Arcymag wpisuje zamówienie ręcznie przez
        <a href="{{ route('add-request') }}">formularz nowego zapytania</a>.
    </li>
</ol>

<h2>Proces zapytania</h2>

<ol>
    <li>
        <x-phase-indicator-mini :status="\App\Models\Status::find(1)" />
        <b>Nowe zapytanie składane przez Klienta lub Arcymaga w jego imieniu.</b>
    </li>
    <li>
        <x-phase-indicator-mini :status="\App\Models\Status::find(5)" />
        <b>Arcymag dokonuje wyceny zapytania.</b>
        Określa wszystkie dane dotyczące powiązanego utworu, uzupełnia dane klienta i podaje kwotę oraz pierwszy termin wykonania zlecenia.
        <ul>
            <li>
                <x-phase-indicator-mini :status="\App\Models\Status::find(4)" />
                Jeśli Arcymag uzna, że zlecenie nie jest przez niego wykonalne, może je odrzucić.
            </li>
        </ul>
    </li>
    <li>
        <b>Klient ma wybór:</b>
        <ul>
            <li>
                <x-phase-indicator-mini :status="\App\Models\Status::find(8)" />
                całkowite odrzucenie wyceny,
            </li>
            <li>
                <x-phase-indicator-mini :status="\App\Models\Status::find(6)" />
                prośba o ponowną wycenę: jeśli Klient ma uwagi do wyceny, może ją zakwestionować i prosić o ponowną.
                Wówczas proces wraca do punktu 2.
            </li>
            <li>
                <x-phase-indicator-mini :status="\App\Models\Status::find(9)" />
                akceptacja: jeśli Klient nie ma uwag, zatwierdza wycenę i rozpoczyna się <a href="{{ route('ppp', ['page' => '2-zlecenia']) }}">proces zlecenia</a>.
            </li>
        </ul>
    </li>
</ol>

<h2>Założenie konta klienta</h2>

<p>
    Po zaakceptowaniu wyceny zapytania przez niezarejestrowanego Klienta, zostaje dla niego utworzone konto w systemie.
    Na ekranie potwierdzającym przyjęcie zlecenia wyświetla się wygenerowane hasło, za pomocą którego Klient może się zalogować.
    Hasło to widnieje też w każdym mailu wysyłanym przez system do Klienta.
</p>

<p>
    Hasło jest unikalne i niezmienialne.
</p>

