<div class="contact-info">
    <a href="mailto:{{ env("MAIL_MAIN_ADDRESS") }}">{{ env("MAIL_MAIN_ADDRESS") }}</a>
    •
    <a href="callto:+48530268000">+48 530 268 000</a>
    
    @foreach (\App\Models\ShowcasePlatform::all() as $platform)
    •
    <a href="{{ $platform->msznm_url }}">
        {{ $platform->name }}
    </a>
    @endforeach
</div>
