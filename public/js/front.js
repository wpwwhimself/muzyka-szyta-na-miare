$(document).ready(function(){
    /**
     * load and display songs
     * it's set here to accelerate loading speed
     */
    $.ajax({
        type: "GET",
        url: "/songs_info",
        success: function (res) {
            if(res.length > 0){
                const list = $("#songs ul");
                $("#songs .grayed-out").remove();
                for(song of res){
                    list.append(`<li>
                        <span>${song.title ?? 'utwór bez tytułu'}</span>
                        <span class='ghost'>${song.artist ?? ''}</span>
                        ${song.has_showcase_file ? `<span title="Posłuchaj próbki mojego wykonania" class="clickable" data-song-id="${song.id}">💽</span>` : ``}
                    </li>`);
                }
                list.after($(`<p>Razem: ${res.length}</p>`));

                $("#songs .clickable").click(function(){
                    const player = document.querySelector("#songs audio");
                    const song_id = $(this).attr("data-song-id");

                    if(player.currentTime && !player.paused){
                        player.pause();
                    }else{
                        player.src = `/showcase/show/${song_id}`;
                        player.load();
                        player.play();
                    }

                });
            }
        }
    });

    /**
     * scrollspy -- nav highlights
     */
    scrollSpy("nav", {
        activeClass: "active",
        offset: 500
    });

    /**
     * animate on scroll
     */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if(entry.isIntersecting){
                entry.target.classList.remove("hidden");
            }else{
                // entry.target.classList.add("hidden");
            }
        });
    });

    const hiddenElements = document.querySelectorAll(".hidden");
    hiddenElements.forEach((el) => observer.observe(el));
});
