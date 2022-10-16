/**
 * Ukrywanie alertów
 */
if($(".alert").length){ //if exists
    //appear
    setTimeout(() => {
        $(".alert").addClass("in");
    }, 1);
    //disappear
    setTimeout(() => {
        $(".alert").removeClass("in");
    }, 2000);
}
