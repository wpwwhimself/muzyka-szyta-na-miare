@props([
    "for",
])

@php
$openings = [
    "podklady" => '$tytuł ($wykonawca) | Jak się szyje muzykę

Krótkie demo, jak powstała partia $partia$ do mojego podkładu. $flavortext',
    "organista" => '$tytuł | Shorty organisty',
];
$hashtags = [
    "podklady" => '#muzykaszytanamiarę #podkład #karaoke #cover #$tytuł #$autor #$gatunek #$instrument',
    "organista" => '#muzykaszytanamiarę #organy #msza #$pieśńczypsalm',
];
@endphp

<code onclick="copyDesc(this)">
<pre>
{{ $openings[$for] }}

Zobacz więcej rolek:
🎵 https://www.tiktok.com/@muzykaszytanamiarepl
▶️ https://www.youtube.com/@muzykaszytanamiarepl
📷 https://www.instagram.com/muzykaszytanamiarepl

Moje usługi:
✂️ https://muzykaszytanamiare.pl/

{{ $hashtags[$for] }}
</pre>
</code>

<script>
function copyDesc(el) {
    navigator.clipboard.writeText(el.textContent)
    alert("Opis skopiowany")
}
</script>
