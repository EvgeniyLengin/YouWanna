function footerColorFunc (leavingSectionIndex) {
    var footerColor = $('.fp-section').eq(leavingSectionIndex-1).attr('data-color');
    if(footerColor === 'Y') {
        $('.copyright, #fp-nav').addClass('white');
    } else {
        $('.copyright, #fp-nav').removeClass('white');
    }
}
$(document).ready(function() {

    var outerWidth = $(window).outerWidth();

    /* высота блока из инсташопа на главной - "смотреть все" */
    function heightLastInstaItemWrapper() {
        var heightLastInstashop = $('.item-wrapper').width()-3;
        $('.insta-wrapper .item-wrapper:last-child').height(heightLastInstashop);
    }

    heightLastInstaItemWrapper();

    $(window).resize(function() {
        heightLastInstaItemWrapper();
    });
    /* высота блока из инсташопа на главной - "смотреть все" */

	// $('.full-page').fullpage({
	// 	verticalCentered: false,
	// 	onLeave: function(leavingSection, leavingSectionIndex, sectionIndex, yMovement) {
	// 		footerColorFunc(leavingSectionIndex);
	// 		$('#fp-nav ul li a').removeClass('active');
	// 		$('#fp-nav ul li').eq(leavingSectionIndex-1).find('a').addClass('active');
	// 	},
	// 	afterRender: function () {
	// 		footerColorFunc(1);
	// 		$('#fp-nav ul li').eq(0).find('a').addClass('active');
	// 	}
	// });

	if ($(window).width() < 769) {
		$('.insta-wrapper .instashop__grid').addClass('owl-carousel');
		$('.popular-items .popular-item.hide1500').remove();
		$('.section.popular .popular-items').addClass('owl-carousel');
		$('.owl-carousel').owlCarousel({
			loop: true,
			margin: 20,
			responsive: {
				0: {
					items: 1
				},
				550: {
					items: 1
				}
			}
		});
	}

	$(window).on('resize', function() {
		if ($(window).width() < 769) {
			$('.insta-wrapper .instashop__grid').addClass('owl-carousel');
			$('.popular-items .popular-item.hide1500').remove();
			$('.section.popular .popular-items').addClass('owl-carousel');
			$('.owl-carousel').owlCarousel({
				loop: true,
				margin: 20,
				responsive: {
					0: {
						items: 1
					},
					768: {
						items: 1
					}
				}
			});
		} else {
			$('.owl-carousel').trigger('destroy.owl.carousel').removeClass('owl-carousel owl-loaded');
			$('.owl-carousel').find('.owl-stage-outer').children().unwrap();
        }
	});


});