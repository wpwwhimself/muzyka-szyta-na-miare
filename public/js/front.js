$(function(){
    /* animacja home na dzień dobry */
    setTimeout(function(){$("#home li:nth-child(1)").removeClass("disguised1");},500);
    setTimeout(function(){$("#home li:nth-child(2)").removeClass("disguised1");},1000);
    setTimeout(function(){$("#home li:nth-child(3)").removeClass("disguised1");},1500);
    setTimeout(function(){$("#home li").removeClass("disguised2");},2000);
    setTimeout(function(){$("#home .title").removeClass("disguised3");},2500);
    setTimeout(function(){$("#home img").removeClass("disguised3");},3000);

    /* scroll na intro */
    var $animation_elements = $('.spreadable');
    var $animation_elements2 = $('.appearable');
    var $animation_elements3 = $('#scrolldown');
    var $animation_elements4 = $('section>h2, section>.flexright h2');
    var $window = $(window);

    function check_if_in_view() {
      var window_height = $window.height();
      var window_top_position = $window.scrollTop();
      var window_bottom_position = (window_top_position + window_height);

      $.each($animation_elements, function() {
        var $element = $(this);
        var element_height = $element.outerHeight();
        var element_top_position = $element.offset().top;
        var element_bottom_position = (element_top_position + element_height);

        //check to see if this current container is within viewport
        if ((element_bottom_position >= window_top_position+200) &&
            (element_top_position <= window_bottom_position-200)) {
          $element.addClass('spread');
        } else {
          $element.removeClass('spread');
        }
      });

      $.each($animation_elements2, function() {
        var $element = $(this);
        var element_height = $element.outerHeight();
        var element_top_position = $element.offset().top;
        var element_bottom_position = (element_top_position + element_height);

        //check to see if this current container is within viewport
        if (element_top_position <= window_bottom_position-100) {
          $element.addClass('appeared');
        } else {
          $element.removeClass('appeared');
        }
      });

      $.each($animation_elements3, function() {
        var $element = $(this);
        var element_height = $element.outerHeight();
        var element_top_position = $element.offset().top;
        var element_bottom_position = (element_top_position + element_height);

        //check to see if this current container is within viewport
        if (element_bottom_position >= window_top_position+200) {
          $element.removeClass('appeared');
        }
      });

      $.each($animation_elements4, function() {
        var $element = $(this);
        var element_height = $element.outerHeight();
        var element_top_position = $element.offset().top;
        var element_bottom_position = (element_top_position + element_height);

        //check to see if this current container is within viewport
        if ((element_bottom_position >= window_top_position+200) &&
            (element_top_position <= window_bottom_position-200)) {
          $element.addClass('inview');
        } else {
          $element.removeClass('inview');
        }
      });
    }

    $window.on('scroll resize', check_if_in_view);
    $window.trigger('scroll');

	/* najazd na #services */
	$(".dividebyfour>li").hover(
	    function(){$("ul", this).css("opacity","1"); $("h3", this).css("opacity","0");},
	    function(){$("ul", this).css("opacity","0"); $("h3", this).css("opacity","1");}
	);

	/* czarne płachty */
	$("#recomms .dummyanchor").click(function(){$("#alltherecomms").css("display", "flex");});
	$("#alltherecomms").click(function(){$("#alltherecomms").css("display", "none");});
	$("#services .dummyanchor").click(function(){$("#alltheservices").css("display", "flex");});
	$("#alltheservices").click(function(){$("#alltheservices").css("display", "none");});
});

$(window).bind('scroll', function() {
    var currentTop = $(window).scrollTop();
    var elems = $('section');
    elems.each(function(index){
      var elemTop 	= $(this).offset().top;
      var elemBottom 	= elemTop + $(this).height();
      if(currentTop >= elemTop -200 && currentTop <= elemBottom -200){
        var id 		= $(this).attr('id');
        var navElem = $('a[href="#' + id+ '"]');
        if(["home", "intro"].includes(id)) $("nav a").removeClass("active");
        else navElem.addClass('active').siblings().removeClass( 'active' );
      }
    })
});
