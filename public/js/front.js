$(document).ready(function(){
    /**
     * load and display songs
     * it's set here to accelerate loading speed
     */
    $.ajax({
        type: "GET",
        url: "/api/songs_info",
        success: function (res) {
            if(res.length > 0){
                const list = $("#songs ul");
                $("#songs .grayed-out").remove();
                for(song of res){
                    list.append(`<li>
                        <span>${song.title ?? 'utwór bez tytułu'}</span>
                        <span class='ghost'>${song.artist ?? ''}</span>
                        ${song.has_showcase_file ? `<span title="Posłuchaj próbki mojego wykonania"
                            class="clickable"
                            data-song-id="${song.id}"
                            data-song-title="${song.full_title}"
                            data-song-desc="${song.notes || ''}"
                        >💽</span>` : ``}
                    </li>`);
                }
                list.after($(`<p>Razem: ${res.length}</p>`));

                $("#song-loader").hide();

                const player = document.querySelector("#songs audio");
                $("#songs .clickable").click(function(){
                    $("#songs .popup").addClass("open");

                    const song_id = $(this).attr("data-song-id");
                    const song_title = $(this).attr("data-song-title");
                    const song_desc = $(this).attr("data-song-desc");

                    $("#songs .popup .song-full-title").text(song_title)
                    $("#songs .popup .song-desc").text(song_desc)

                    $("#song-loader").show();
                    player.src = `showcase/show/${song_id}`;
                    player.load();
                    $("#song-loader").hide();
                    player.play()
                });
                $("#songs #popup-close").click(function() {
                    $("#songs .popup").removeClass("open");
                    player.pause()
                })
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
