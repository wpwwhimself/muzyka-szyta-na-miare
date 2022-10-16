<header>
    <x-logo />
    <div>
        <div class="flex-right">
            <h1>{{  $title == null ? config("app.name") : $title }}</h1>
            <h2>{{ $title != null ? config("app.name") : "" }}</h2>
        </div>
        <x-nav />
    </div>
</header>
