/**
 * Opens/closes sections
 */
function openExtendoBlock(btn, key) {
    document.querySelector(`[data-ebid='${key}'] .body`).classList.toggle("hidden");
    btn.parentElement.querySelectorAll(`.toggles`).forEach(b => b.classList.toggle("hidden"));
}

/**
 * runs price calculation
 */
function reQuestCalcPrice(labels, client_id) {
    const loader = document.querySelector("#price-summary .loader");

    loader.classList.remove("hidden");
    fetch(`/api/price_calc`, {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}',
            labels: labels,
            client_id: client_id,
            quoting: true
        }),
    })
        .then(res => res.json())
        .then(({data, table}) => {
            document.querySelector("#price-summary").replaceWith(fromHTML(table));
            checkMonthlyPaymentLimit(data.price);
        });
}

/**
 * checks monthly payment limit
 */
function checkMonthlyPaymentLimit(price) {
    document.querySelector("#delayed-payments-summary .loader").classList.remove("hidden");

    fetch(`/api/monthly_payment_limit`, {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            _token: '{{ csrf_token() }}',
            amount: price,
        }),
    })
        .then(res => res.json())
        .then(({data, table}) => {
            document.querySelector("#delayed-payments-summary").replaceWith(fromHTML(table));

            let delayed_payment;
            if(data.when_to_ask == 0){
                delayed_payment = undefined;
            }else{
                let today = new Date();
                delayed_payment = (new Date(today.getFullYear(), today.getMonth() + data.when_to_ask, 1));
                delayed_payment = `${delayed_payment.getFullYear()}-${(delayed_payment.getMonth() + 1).toString().padStart(2, 0)}-${delayed_payment.getDate().toString().padStart(2, 0)}`;
            }
            if (document.getElementById("delayed_payment")) {
                document.getElementById("delayed_payment").value = delayed_payment;
            }
        });
}

/**
 * set up page reload
 */
function primeReload() {
    window.onfocus = function () { location.reload(true) }
}

/**
 * File player
 */
const changeFilePlayerButton = (filename, icon) => {
    document.querySelectorAll(`.file-player[data-file-name="${filename}"] [role="btn"]`)
        .forEach(cntnr => {
            cntnr.classList.toggle("hidden", cntnr.children[0].id != `mdi-${icon}`);
        });
}
const disableFilePlayer = (filename) => {
    changeFilePlayerButton(filename, "loading")
    showSeeker(filename, false)
}
const enableFilePlayer = (filename) => {
    changeFilePlayerButton(filename, "play")
    showSeeker(filename)
}
const startFilePlayer = (filename) => {
    document.querySelector(`.file-player[data-file-name="${filename}"] audio`).play()
    changeFilePlayerButton(filename, "pause")
}
const pauseFilePlayer = (filename) => {
    document.querySelector(`.file-player[data-file-name="${filename}"] audio`).pause()
    changeFilePlayerButton(filename, "play")
}

const durToTime = (duration) => {
    const minutes = Math.floor(duration / 60)
    const seconds = Math.floor(duration % 60)
    return `${minutes}:${seconds < 10 ? "0" + seconds : seconds}`
}

const showSeeker = (filename, show = true) => {
    const seeker = document.querySelector(`.file-player[data-file-name="${filename}"] .seeker`)
    if (show) {
        updateSeeker(filename)
        seeker.classList.remove("hidden")
    } else {
        seeker.classList.add("hidden")
    }
}

const updateSeeker = (filename) => {
    const seeker = document.querySelector(`.file-player[data-file-name="${filename}"] .seeker`)
    const audio = document.querySelector(`.file-player[data-file-name="${filename}"] audio`)

    seeker.innerHTML = `${durToTime(audio.currentTime)} / ${durToTime(audio.duration)}`
    seeker.style.setProperty("--progress", `${(audio.currentTime / audio.duration) * 100}%`)
}

const seekFilePlayer = (filename, event) => {
    const audio = document.querySelector(`.file-player[data-file-name="${filename}"] audio`)
    audio.currentTime = (event.offsetX / event.target.offsetWidth) * audio.duration
    updateSeeker(filename)
}

//#region song files
function loadFileList(container_uuid) {
    const container = document.querySelector(`.files-container[data-uuid="${container_uuid}"]`);
    const loader = container.querySelector(".loader");
    const meta = container.querySelector(".meta");
    const contents = container.querySelector(".contents");

    const song_id = meta.dataset.songId;
    const whoAmI = meta.dataset.whoAmI;
    const canDownloadFiles = meta.dataset.canDownloadFiles;
    const editable = meta.dataset.editable;
    const highlightForClientId = meta.dataset.highlightForClientId;
    
    loader.classList.remove("hidden");
    contents.innerHTML = "";

    fetch(`/api/songs/${song_id}/files?` + new URLSearchParams({
        whoAmI,
        canDownloadFiles,
        editable,
        highlightForClientId,
    }))
        .then(res => res.json())
        .then(({data, table}) => {
            contents.innerHTML = table;
        })
        .catch(err => console.error(err))
        .finally(() => loader.classList.add("hidden"));
}

function primeReloadFileList(btn) {
    window.onfocus = function () { loadFileList(btn.closest(".files-container").dataset.uuid); }
}
//#endregion

function printInvoice() {
    const tags_to_hide = [
        "header",
        "footer [role='top-part']",
        "footer [role='middle-part']",
    ];
    tags_to_hide.forEach(selector => {
        document.querySelector(selector).classList.add("hide-for-print");
    });
    window.print();
}

//#region showcase song list
function filterSongs(domain, criterion = undefined, id = undefined) {
    const filterFunction = {
        genre: (song, needle, haystack) => haystack === needle,
        tag: (song, needle, haystack) => haystack.includes(needle)
    }

    // mark buttons as selected
    const filters = document.querySelector(`[role='${domain}-filters']`)
    if (criterion && id) {
        filters.querySelectorAll(`.button[onclick^='filterSongs']`).forEach(btn => { btn.classList.add("ghost"); btn.classList.remove("active"); });
        filters.querySelector(`.button[onclick="filterSongs(\`${domain}\`, '${criterion}', ${id})"]`).classList.remove("ghost");
        filters.querySelector(`.button[onclick="filterSongs(\`${domain}\`, '${criterion}', ${id})"]`).classList.add("active");
    } else {
        filters.querySelectorAll(`.button[onclick^='filterSongs']`).forEach(btn => { btn.classList.remove("ghost"); btn.classList.remove("active"); });
    }

    // filter objects
    // let visible_songs = 0;
    document.querySelectorAll(`ul#${domain}-song-list li`).forEach(song => {
        const haystacks = {
            genre: Number(song.getAttribute("data-song-genre")),
            tag: song.getAttribute("data-song-tags").split(",").map(Number),
        }

        if (criterion === undefined) {
            song.classList.remove("hidden")
            // visible_songs++
            return
        }

        if (!filterFunction[criterion](song, parseInt(id), haystacks[criterion])) {
            song.classList.add("hidden")
        } else {
            song.classList.remove("hidden")
            // visible_songs++
        }
    })
    // document.querySelector(`#${domain}-songs-count`).innerHTML = visible_songs
}

function filterShowcases(mode) {
    document.querySelectorAll("#showcases .showcase-section").forEach(section => {
        section.classList.toggle("hidden", section.getAttribute("data-mode") !== mode);
        section.closest(`.section`).querySelectorAll(`.header .actions .button.toggle`).forEach(btn => {
            btn.classList.toggle("active", btn.getAttribute("onclick") === `filterShowcases('${mode}')`);
        });
    })
}

/**
 * load and display songs
 * it's set here to accelerate loading speed
 */
function getSongList(domain = undefined) {
    const list = document.querySelector(`ul#${domain}-song-list`);
    const params = new URLSearchParams(window.location.search);

    list.querySelector(".loader").classList.remove("hidden");

    if (params.has("composition")) {
        openCompositionDemos(params.get("composition"));
    }

    fetch("/api/songs/info" + (domain ? "?for=" + domain : ""))
        .then(res => res.json())
        .then(({data, table}) => {
            list.replaceWith(fromHTML(table));

            if (params.has("tag")) {
                filterSongs(domain, "tag", params.get("tag"));
            } else if (params.has("genre")) {
                filterSongs(domain, "genre", params.get("genre"));
            }
        });
}

function startDemo(song_id) {
    const popup = document.querySelector("#song-demo-popup");
    const player = popup.querySelector("audio");

    player.src = `showcase/show/${song_id}`;
    player.parentElement.classList.remove("hidden");
    player.load();
    player.addEventListener("canplay", () => {
        startFilePlayer("");
    });
}

function openSongDemo(song_id = undefined, song_title = undefined, song_desc = undefined) {
    const popup = document.querySelector("#song-demo-popup");
    const player = popup.querySelector("audio");

    popup.classList.toggle("open", song_id !== undefined);

    if (song_id == undefined) {
        pauseFilePlayer("");
    }

    popup.querySelector(".song-full-title").innerHTML = song_title;

    if (song_id !== undefined) {
        startDemo(song_id);
    }
}

function openCompositionDemos(composition_id = undefined) {
    const popup = document.querySelector("#song-demo-popup");
    const player = popup.querySelector("audio");
    const loader = popup.querySelector(".loader");

    popup.classList.toggle("open", composition_id !== undefined);

    popup.querySelector(".song-full-title").innerHTML = "";
    popup.querySelector(".song-list").innerHTML = "";

    if (composition_id == undefined) {
        pauseFilePlayer("");
    } else {
        loader.classList.remove("hidden");
        player.parentElement.classList.add("hidden");

        fetch(`/api/songs/compositions/${composition_id}`)
            .then(res => res.json())
            .then(({composition, songs}) => {
                popup.querySelector(".song-full-title").innerHTML = composition.full_title;
                songs.forEach(song => {
                    popup.querySelector(".song-list").innerHTML += `<li>
                        ${song.full_title}
                        ${song.has_showcase_file
                            ? song.play_demo_button
                            : ""
                        }
                    </li>`;
                });
            })
            .finally(() => {
                loader.classList.add("hidden");
            });
    }
}
//#endregion
