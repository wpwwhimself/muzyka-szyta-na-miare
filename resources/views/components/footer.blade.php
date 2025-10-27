<footer>
  <x-logo />
  <div>
    <h2>{{ config("app.name") }}</h2>
    <p>Stronę własnoręcznie zbudował <a href="http://wpww.pl">Wojciech Przybyła</a></p>
    <p><a href="https://creativecommons.org/licenses/by-sa/3.0/pl/">&copy; CC BY-SA 3.0</a> 2019 – {{ date("Y") }}</p>
    <p class="yellowed-out"><i class="fas fa-bug"></i> Widzisz gdzieś błąd? Napisz!</p>
  </div>

  @unless($stripped)

@endunless
</footer>
