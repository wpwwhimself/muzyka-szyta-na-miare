<header>
    <div class="flex-right keep-for-mobile">
        <x-logo />
        <div class="flex-right">
            <h1>{{  $title == null ? config("app.name") : $title }}</h1>
            <h2>{{ $title != null ? config("app.name") : "" }}</h2>
        </div>
    </div>
    <x-nav />
</header>
