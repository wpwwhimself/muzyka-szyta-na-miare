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
                    list.append(`<li>${song.title ?? 'utwór bez tytułu'} <span class='ghost'>${song.artist ?? ''}</span></li>`);
                }
                list.after($(`<p>Razem: ${res.length}</p>`));
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
