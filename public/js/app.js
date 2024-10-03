/**
 * Ukrywanie alertów
 */
const TOAST_TIMEOUT = 4000;
$(document).ready(() => {
    if (!$(".alert").length) return

    //appear
    setTimeout(() => {
        $(".alert").addClass("in");
    }, 1);

    //allow dismissal
    $(".alert").click(() => $(".alert").removeClass("in"));

    //disappear
    setTimeout(() => {
        $(".alert").removeClass("in");
    }, TOAST_TIMEOUT);
})
/**
 * Niebezpieczne guziki
 */
function dangerConfirm(){
    let x = confirm("Ostrożnie! Czy na pewno chcesz to zrobić?");
    if(!x){
        event.preventDefault();
    }
}
/**
 * Wyłacznik tutoriali
 */
$(document).ready(()=>{
    if(IS_VETERAN){
        $(".tutorial").hide();
    }
});

/**
 * Podświetlanie inputów
 */
const highlightInput = (input) => {
    document.querySelectorAll(`.input-container`).forEach(el => el.classList.add("ghost"))
    input.closest(".input-container").classList.remove("ghost");
    input.nextElementSibling.classList.add("accent", "bigger");
}
const clearHighlightInput = (input) => {
    document.querySelectorAll(`.input-container`).forEach(el => el.classList.remove("ghost"))
    input.nextElementSibling.classList.remove("accent", "bigger");
}

/**
 * File player
 */
const changeFilePlayerButton = (filename, icon) => {
    document.querySelectorAll(`.file-player[data-file-name="${filename}"] .fa-solid`)
        .forEach(icon => icon.classList.add("hidden"))
    document.querySelector(`.file-player[data-file-name="${filename}"] .fa-solid.fa-${icon}`)
        .classList.remove("hidden")
}
const enableFilePlayer = (filename) => {
    changeFilePlayerButton(filename, "play")
    showLoader(filename, false)
}
const startFilePlayer = (filename) => {
    document.querySelector(`.file-player[data-file-name="${filename}"] audio`).play()
    changeFilePlayerButton(filename, "pause")
}
const pauseFilePlayer = (filename) => {
    document.querySelector(`.file-player[data-file-name="${filename}"] audio`).pause()
    changeFilePlayerButton(filename, "play")
}

const showLoader = (filename, reveal = true) => {
    const loader = document.querySelector(`.file-player[data-file-name="${filename}"] .loader`)
    const audio = document.querySelector(`.file-player[data-file-name="${filename}"] audio`)

    loader.innerHTML = (audio.buffered.length)
        ? Math.round(audio.buffered.end(0) / audio.duration * 100) + "%"
        : "..."

    (reveal) ? loader.classList.remove("hidden") : loader.classList.add("hidden")
}
