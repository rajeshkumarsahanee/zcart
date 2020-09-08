//MAGNIFIC POPUP
$(document).ready(function() {
  $('.images-block').magnificPopup({
    delegate: 'a', 
    type: 'image',
    gallery: {
      enabled: true
    }
  });
});

(function($) {

  "use strict";
	
  // TOOLTIP	
  $(".header-links .fa, .tool-tip").tooltip({
	placement: "bottom"
  });
  $(".btn-wishlist, .btn-compare, .display .fa").tooltip('hide');

  // TABS
  $('.nav-tabs a').click(function (e) {
    e.preventDefault();
	$(this).tab('show');
  });	
	
})(jQuery);