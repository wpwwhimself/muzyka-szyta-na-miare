@props([
    "data",
])

@php
$fors = [
    "Showcase" => "podklady",
    "OrganShowcase" => "organista",
    "DjShowcase" => "dj",
];
$for = $fors[Str::of($data::class)->classBasename()->__toString()];

$title_holder = '$tytuł';
$artist_holder = '$wykonawca';
$openings = [
    "podklady" => $title_holder.' ('.$artist_holder.') | Jak się szyje muzykę

Krótkie demo, jak powstała partia $partia do mojego podkładu. $flavortext',
    "organista" => '$tytuł | Shorty organisty

Zagrane na mszy ślubnej w ...

Nuty: https://sz3.wpww.pl/...',
    "dj" => '$tytuł | Shorty ze sceny',
];
$hashtags = [
    "podklady" => '#muzykaszytanamiarę #podkład #karaoke #cover #$autor #$gatunek #$instrument',
    "organista" => '#muzykaszytanamiarę #organy #msza #$pieśńczypsalm #ślub?',
    "dj" => '#muzykaszytanamiarę #dj #muzykalive #cover #$autor #$gatunek #$instrument',
];
@endphp

<pre onclick="copyDesc(this)">
{{ $openings[$for] }}

Zobacz więcej rolek:
🎵 https://www.tiktok.com/@muzykaszytanamiarepl
▶️ https://www.youtube.com/@muzykaszytanamiarepl
📷 https://www.instagram.com/muzykaszytanamiarepl

Moje usługi:
✂️ https://muzykaszytanamiare.pl/

{{ $hashtags[$for] }}
</pre>

<script>
function copyDesc(el) {
    navigator.clipboard.writeText(el.textContent.trim())
    alert("Opis skopiowany")
}
</script>
