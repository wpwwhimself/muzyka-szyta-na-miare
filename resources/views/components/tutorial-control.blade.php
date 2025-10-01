@props(['isVeteran'])

<script>
if({{ intval($isVeteran) }}){
    document.querySelectorAll(".tutorial").forEach(el => el.classList.add("hidden"));
}
</script>
