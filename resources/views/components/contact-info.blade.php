<div class="contact-info flex down no-gap">
    <a class="flex right no-wrap middle" href="mailto:{{ env("MAIL_MAIN_ADDRESS") }}">
        <x-shipyard.app.icon name="email" />
        {{ env("MAIL_MAIN_ADDRESS") }}
    </a>
    <a class="flex right no-wrap middle" href="callto:+48530268000">
        <x-shipyard.app.icon name="phone" />
        <x-shipyard.app.icon name="whatsapp" />
        +48 530 268 000
    </a>

    <span class="flex right no-wrap middle">
        @foreach (\App\Models\ShowcasePlatform::all() as $platform)
        {{-- <a href="{{ $link }}"> --}}
            {{-- <i class="fa-brands fa-{{ $icon }}"></i> --}}
        {{ $platform->name }}
        {{-- </a> --}}
        @endforeach
        muzykaszytanamiarepl
    </span>
</div>
