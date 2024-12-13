function filterSongs(tag_id = undefined) {
    let visible_songs = 0;
    document.querySelectorAll("#songs li").forEach(song => {
        const tags = song.dataset.songTags.split(",").map(parseInt)
        if (tag_id !== undefined && !tags.includes(tag_id)) {
            song.classList.add("gone")
        } else {
            song.classList.remove("gone")
            visible_songs++
        }
    })
    document.querySelector("#songs-count").innerHTML = visible_songs
}

$(document).ready(function(){
    /**
     * load and display songs
     * it's set here to accelerate loading speed
     */
    $.get({
        url: "/api/songs/info",
        success: function (res) {
            if(res.length > 0){
                const list = $("#songs ul");
                $("#songs .grayed-out").remove();
                for(song of res) {
                    const tags = song.tags.map(tag => tag.id).join(",");

                    list.append(`<li data-song-tags="${tags}">
                        <span>${song.title ?? 'utwór bez tytułu'}</span>
                        <span class='ghost'>${song.artist ?? ''}</span>
                        ${song.has_showcase_file ? `<span title="Posłuchaj próbki mojego wykonania"
                            class="clickable"
                            data-song-id="${song.id}"
                            data-song-title="${song.full_title}"
                            data-song-desc="${song.notes?.replace(/\n/g, '<br>') || ''}"
                        >💽</span>` : ``}
                    </li>`);
                }
                list.after($(`<p>Razem: <b id="songs-count">${res.length}</b></p>`));

                $("#song-loader").hide();

                const player = document.querySelector("#songs audio");
                $("#songs .clickable").click(function(){
                    $("#songs .popup").addClass("open");

                    const song_id = $(this).attr("data-song-id");
                    const song_title = $(this).attr("data-song-title");
                    const song_desc = $(this).attr("data-song-desc");

                    $("#songs .popup .song-full-title").text(song_title)
                    $("#songs .popup .song-desc").html(song_desc)

                    player.src = `showcase/show/${song_id}`;
                    player.load();
                    player.addEventListener("canplay", () => {
                        startFilePlayer("")
                    })
                });
                $("#songs #popup-close").click(function() {
                    $("#songs .popup").removeClass("open");
                    pauseFilePlayer("")
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
