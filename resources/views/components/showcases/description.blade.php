@props([
    "for",
])

<code onclick="copyDesc(this)">
@switch ($for)
@case ("podklady")
$tytuł$ ($wykonawca$) | Jak się szyje muzykę

Krótkie demo, jak powstała partia $partia$ do mojego podkładu. $flavor text$

Zobacz więcej rolek:
🎵 https://www.tiktok.com/@muzykaszytanamiarepl
▶️ https://www.youtube.com/@muzykaszytanamiarepl
📷 https://www.instagram.com/muzykaszytanamiarepl

Moje usługi:
✂️ https://muzykaszytanamiare.pl/

#muzykaszytanamiarę #podkład #karaoke #cover
#$tytuł$ #$autor$ #$gatunek$ #$instrument$
@break

@case ("organista")
$tytuł$ | Shorty organisty

Zobacz więcej rolek:
🎵 https://www.tiktok.com/@muzykaszytanamiarepl
▶️ https://www.youtube.com/@muzykaszytanamiarepl
📷 https://www.instagram.com/muzykaszytanamiarepl

Moje usługi:
✂️ https://muzykaszytanamiare.pl/

#muzykaszytanamiarę #organy #msza
#$pieśńczypsalm$
@break
@endswitch
</code>

<script>
function copyDesc(el) {
    navigator.clipboard.writeText(el.textContent)
    alert("Opis skopiowany")
}
</script>
