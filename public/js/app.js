/**
 * Ukrywanie alertów
 */
const TOAST_TIMEOUT = 4000;
if($(".alert").length){ //if exists
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
}
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
