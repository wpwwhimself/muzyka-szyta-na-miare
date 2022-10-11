<header>
    <x-logo />
    <div>
        <h1>{!! $title == null ? config("app.name") : $title."<small>".config("app.name")."</small>" !!}</h1>
        <x-nav :for-whom="$forWhom" />
    </div>
</header>
