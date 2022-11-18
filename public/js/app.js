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
