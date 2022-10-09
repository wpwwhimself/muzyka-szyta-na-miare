<header>
    <x-logo />
    <div>
        <h1>{{ $title }} <small>{{ config("app.name") }}</small></h1>
        <x-nav :for-whom="$forWhom" />
    </div>
</header>
