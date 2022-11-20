@props(['isVeteran'])

<script>
$(document).ready(function(){
  if({{ intval($isVeteran) }}){
    $(".tutorial").hide();
  }
});
</script>