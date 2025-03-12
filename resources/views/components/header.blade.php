<header class="{{ $stripped ? "stripped" : "" }}">
    <div class="flex-right keep-for-mobile">
        <x-logo />

        @unless ($stripped)
        <div class="flex-right">
            <h1>{{  $title == null ? config("app.name") : $title }}</h1>
            <h2>{{ $title != null ? config("app.name") : "" }}</h2>
        </div>
        @endunless
    </div>

    @unless ($stripped)
    <x-nav />
    @endunless
</header>
