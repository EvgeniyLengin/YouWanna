$(function() {
	if($(window).outerWidth() < 1025) {
		$('.yw-topmenuitem:last-of-type > a').on('click', function(e) {
			e.preventDefault();
			var parent = $(this).parent();
		    if(parent.find('ul').length > 0) {
		        parent.toggleClass('open');
		        parent.siblings().removeClass('open');
		    }

		    return false;
		})
	} else {
		$('.yw-topmenusubitem[data-bg] a').on('mouseenter', function() {
			var bg = $(this).closest('.yw-topmenusubitem[data-bg]').data('bg');
			if(bg != '' && !!bg != false) {
				$(this).closest('.yw-topmenusub').css('background-image', 'url(' + bg + ')')
			}
		})
	}
})