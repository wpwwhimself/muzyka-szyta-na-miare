<h1>Supermoce Arcymaga</h1>
<p>
    Administrator w razie potrzeby może nadpisać parametry zleceń niestandardową drogą,
    na przykład w sytuacji błędnego wpisania danych przez Klienta lub potrzeby ich usunięcia.
    Do tego celu służą specjalne adresy URL wpisywane na zleceniach/zapytaniach:
</p>

<h2>Dla zapytań</h2>
<p>
    <code>obliterate</code> --
    Usuwa zapytanie i jego historię. Przydatne, jeśli Klient złoży dodatkowe zapytanie przez przypadek.
</p>

<h2>Dla zleceń</h2>
<p>
    <code>restatus/{status_id}</code> --
    Zmienia status zlecenia wraz z charakterem ostatniego komentarza. Przydatne, jeśli Klient odrzuci zlecenie zamiast go akceptować.
</p>
<p>
    <code>phantompay/{paid?}</code> --
    Zmienia status opłacenia zlecenia. Przydatne, jeśli klient jednak nie opłacił zlecenia lub coś pójdzie nie tak w systemie i trzeba odebrać prawo do pobierania.
</p>
