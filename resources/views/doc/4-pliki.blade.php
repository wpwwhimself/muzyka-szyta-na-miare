<p>
  Zasadą jest, że Klient ma podgląd do plików, które widnieją w jego zleceniu.
  Technicznie wiąże się to z pokazywaniem plików utworu powiązanego z danym zleceniem.
  Pliki te są dodawane przez Administratora <a href="{{ route('quests') }}">w jego widoku zlecenia</a> i trafiają do Sejfu – folderu o nazwie równej ID utworu.
</p>

<h1>Możliwość pobierania plików</h1>

<p>
  Funkcja pomocnicza <code>can_download_files($client_id, $quest_id)</code> definiuje dostęp do pobierania plików przez Klienta.
  Warunki, jakie ten musi spełnić, to:
</p>
<ul>
  <li>Klient nie jest krętaczem <i class="fas fa-user-ninja error"></i></li>
  <li>
    jeden z poniższych:
    <ul>
      <li>Klient jest weteranem <i class="fas fa-user-shield"></i></li>
      <li>Klient ma ponadprzeciętne zaufanie <i class="fas fa-hand-holding-heart success"></i></li>
      <li>dla zlecenia jest określona data opóźnionej płatności, która jest w przyszłości, a zlecenie jest w statusie zakończone <x-phase-indicator-mini :status="\App\Models\Status::find(19)" /></li>
    </ul>
  </li>
</ul>
<p>
  Wskaźnik, czy Klient może pobierać pliki, jest widoczny dla Administratora <a href="{{ route('quests') }}">w jego widoku zlecenia</a>.
</p>
<p>
  Do pobrania udostępniane są wszystkie pliki znajdujące się w Sejfie, z wykluczeniem notatek w formacie <code>.md</code>.
</p>

<h1>Widoczność plików</h1>

<p>
  Warunkiem koniecznym podglądu zawartości Sejfu jest brak statusu krętacza <i class="fas fa-user-ninja error"></i> dla Klienta.
  Podglądy plików są generowane dla formatów <code>.mp3</code> i <code>.mp4</code>.
</p>
<p>
  Dodatkowo, ponieważ w Sejfie danego utworu mogą znajdować się pliki przeznaczone dla konkretnego Klienta, ukryte są pliki, których wariant (określony w nazwie pliku po znaku <code>=</code>) zawiera ID Klienta różne od ID przeglądającego.
</p>
<p class="grayed-out">
  Wsparcie dla podglądu plików <code>.pdf</code> zostało wyłączone ze względu na możliwość obejścia blokady pobierania.
</p>