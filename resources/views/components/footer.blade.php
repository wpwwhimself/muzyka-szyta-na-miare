<footer>
  <x-logo />
  <div>
    <h2>{{ config("app.name") }}</h2>
    <p>Stronę własnoręcznie zbudował <a href="http://wpww.pl">Wojciech Przybyła</a></p>
    <p><a href="https://creativecommons.org/licenses/by-sa/3.0/pl/">&copy; CC BY-SA 3.0</a> 2019 – {{ date("Y") }}</p>
    <p class="yellowed-out"><i class="fas fa-bug"></i> Widzisz gdzieś błąd? Napisz!</p>
  </div>
  <div class="contact-info">
    <a href="mailto:{{ env("MAIL_MAIN_ADDRESS") }}">
        <i class="fa-solid fa-envelope"></i>
        {{ env("MAIL_MAIN_ADDRESS") }}
    </a>
    <a href="callto:+48530268000">
        <i class="fa-solid fa-phone"></i>
        <i class="fa-brands fa-whatsapp"></i>
        +48 530 268 000
    </a>

    <span>
        @foreach ($socials as [$icon, $link])
        <a href="{{ $link }}">
            <i class="fa-brands fa-{{ $icon }}"></i>
        </a>
        @endforeach
        muzykaszytanamiarepl
    </span>
</div>
</footer>
