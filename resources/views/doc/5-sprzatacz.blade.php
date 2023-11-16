<h1>Sprzątacz</h1>

<p>
    Sprzątacz jest skryptem zajmującym się porządkowaniem zapytań i zleceń,
    o których Klienci lub Arcymag mogli zapomnieć.
    Potrafi manipulować zleceniami i zapytaniami, czyścić stare Sejfy,
    a także rozsyła maile do Klientów.
</p>
<p>
    Całość logiki zawarta jest w kontrolerze <code>JanitorController.php</code>.
</p>

<h2>Wykonywanie</h2>
<p>
    Procedura Sprzątacza jest wywoływana za pomocą zadania cron dostępnych w cPanelu
    (obecnie ustawionego na codziennie na godzinę 1:00)
    lub może zostać wywołana ręcznie przez przejście pod adres <code>/janitor</code>.
    Po jej wykonaniu, następuje przekierowanie na kokpit, gdzie dostępny jest raport wykonanych akcji.
</p>

<h2>Kryteria</h2>

<h3>Operacje związane ze zleceniami i zapytaniami</h3>
<p>
    Po wywołaniu, Sprzątacz zbiera informacje na temat wartych uwagi zleceń i zapytań
    oraz wykonuje powiązane z nimi akcje.
    Logika mechanizmu obejmuje:
</p>
<ul>
    <li>wygaszanie zleceń i zapytań, pod którymi Klient nie podjął żadnych działań w określonym oknie czasu,</li>
    <li>przypomnienia o podjęciu działań dotyczących zlecenia (opinia, opłata).</li>
</ul>
<ol class="ghost">
    <li>Wszystkie akcje wysyłające powiadomienia sprawdzają, czy Klient posiada maila.</li>
    <li>W przypadku zleceń z określoną datą opóźnionej wpłaty, akcje dotyczące zleceń sprawdzają, czy minął miesiąc od tej daty.</li>
</ol>

<h3>Operacje związane z Sejfami</h3>
<p>
    Z uwagi na ograniczoną ilość miejsca na serwerze, procedura obejmuje czyszczenie starych Sejfów.
    Po wywołaniu, Sprzątacz sprawdza wielkość plików we wszystkich Sejfach
    oraz usuwa katalogi starsze niż {{ setting("safe_old_enough") }} dni.
</p>