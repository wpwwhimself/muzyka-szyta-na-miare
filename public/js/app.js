/**
 * Ukrywanie alertów
 */
const TOAST_TIMEOUT = 4000;
if($(".alert").length){ //if exists
    //appear
    setTimeout(() => {
        $(".alert").addClass("in");
    }, 1);
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