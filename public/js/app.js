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
    input.classList.add("highlighted");
    input.nextElementSibling.classList.add("accent");
}
const clearHighlightInput = (input) => {
    input.classList.remove("highlighted");
    input.nextElementSibling.classList.remove("accent");
}
