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

<h1>Nazwy plików</h1>

<p>
  Aby odpowiednio wyświetlić pliki w zleceniu,
  system korzysta ze schematu nazewnictwa plików,
  który jest wykorzystywany do ich dzielenia i grupowania.
  Pliki na serwerze powinny być wgrywane z nazwami zgodnymi z poniższym schematem:
</p>
<code>
  {nazwa główna}={nazwa wariantu}_{nazwa wersji}[{tagi}].{rozserzenie}
</code>
<ul>
  <li>
    nazwa główna
    -- wymagana, zazwyczaj ID utworu. W przypadku wielu nazw głównych w Sejfie pojawiają się nagłówki
  </li>
  <li>
    nazwa wariantu
    -- stawiana po znaku <code>=</code>, wydziela większe grupy plików,
    zazwyczaj różniących się w znacznym stopniu (np. aranżacja, tempo).
    Brak nazwy wariantu jest wyświetlany jako <strong>wariant podstawowy</strong>
  </li>
  <li>
    nazwa wersji
    -- stawiana po znaku <code>_</code>, wydziela osobne wersje plików,
    różniące się w mniejszym stopniu (np. inne pojedyncze dźwięki, wyciszone partie).
    Brak nazwy wersji jest wyświetlany jako <strong>wersja główna</strong>
  </li>
  <li>
    tagi
    -- umieszczane w nazwie wersji w nawiasach kwadratowych,
    wskazują na ważne modyfikacje plików. Obecnie wyróżniane są następujące tagi:
    <ul>
      <li><x-file-tag tag="c" /> c -- wersja z metronomem (click track)</li>
      <li><x-file-tag tag="d" /> d -- wersja demonstracyjna, np. z atrapą wokalu</li>
      <li><x-file-tag tag="m" /> m -- wersja z linią melodyczną</li>
      <li><x-file-tag tag="v" /> v -- wersja z linią wokalną</li>
      <li><x-file-tag tag="t+1" /> t -- transpozycja względem oryginału -- należy podać kierunek i liczbę półtonów, np. t-3 albo t+2</li>
    </ul>
  </li>
</ul>
