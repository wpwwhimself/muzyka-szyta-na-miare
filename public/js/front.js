$(document).ready(function(){
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