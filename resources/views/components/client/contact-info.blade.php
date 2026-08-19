@props([
    "data"
])

@isset($data["client_name"])
<span><strong>{{ $data["client_name"] }}</strong></span>
<br />
@endisset

@isset($data["email"])
<span><strong>Mail</strong>: <a href="mailto:{{ $data["email"] }}">{{ $data["email"] }}</a></span>
<br />
@endisset
@isset($data["phone"])
<span><strong>Telefon</strong>: <a href="tel:{{ $data["phone"] }}">{{ $data["phone"] }}</a></span>
<br />
@endisset
@isset($data["other_medium"])
<span><strong>Inne medium</strong>: {{ $data["other_medium"] }}</span>
<br />
@endisset
