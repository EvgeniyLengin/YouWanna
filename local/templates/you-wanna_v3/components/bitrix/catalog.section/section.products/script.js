$(document).ready(function () {
    initListOwl();
    initDetailCatalogTrigger();
});

function setSortBlock () {
    var element = $('.catalog-list-sort');
    if ($('.js-sort-click.active', element).length > 0) {
        $('.filter-select-text', element).text($('.js-sort-click.active', element).text());
    }
}

$(document).ready(function () {
    setSortBlock();
    $(".catalog-list-sort .js-sort-click").on("click", function () {
        $(".catalog-list-sort .js-sort-click").removeClass('active');
        $(this).addClass('active');
        var sort = $(this).data('value');
        setSortBlock();
        $('.catalog-list-sort .checkbox-block').hide();
        window.location.href = window.location.origin + window.location.pathname + '?sort=' + sort;
    });
    // if($(window).width() < 768) {
    //     var maxHeight = 0;
    //     $('.element-list-item').each(function () {
    //         if ($(this).height() > maxHeight) {
    //             maxHeight = $(this).height();
    //         }
    //     });
    //     $('.element-list-item').each(function () {
    //         $(this).height(maxHeight);
    //     });
    // }
    // $(window).resize(function(){
    //     if($(window).width() < 768) {
    //         var maxHeight = 0;
    //         $('.element-list-item').each(function () {
    //             if ($(this).height() > maxHeight) {
    //                 maxHeight = $(this).height();
    //             }
    //         });
    //         $('.element-list-item').each(function () {
    //             $(this).height(maxHeight);
    //         });
    //     } else {
    //         $('.element-list-item').each(function () {
    //             $(this).height('auto');
    //         });
    //     }
    // })
	
	$(".offer-change span").on("click", function () {		
		element = $(this).parents('.element-inner');
	
		element.find('.element-list-name a').text($(this).attr('data-offer-name'));
		if($(this).attr('data-offer-img')) {
			element.find('.element-image').css('background-image', 'url('+$(this).attr('data-offer-img')+')');
			element.find('.element-image').attr('data-next', $(this).attr('data-offer-img2'));
		}
		
		price = $(this).attr('data-offer-value');
		price_discount = $(this).attr('data-offer-discount-value');
		discount_percent = $(this).attr('data-offer-discount-percent');
		
		element.find('.element-list-price .price-value').text(price);
		element.find('.element-list-price .new-price').text(price_discount);
		element.find('.element-list-price .discount-price').text(price);
		element.find('.element-list-price .discount-percent').text('-' + discount_percent + '%');
		
		if(price_discount) {
			element.find('.element-list-price price').removeClass('.hidden-price');	
			element.find('.element-list-price price.price-value').addClass('.hidden-price');	
		} else {
			element.find('.element-list-price price').addClass('.hidden-price');	
			element.find('.element-list-price price.price-value').removeClass('.hidden-price');	
		}
	});
	
});

/**
 * Ajax ?????????????????? ??????????????, ???????????? ??????????????????
 */
function ajaxLoadCatalogSectionElements(element) {

    var itemLink = '';
    var container = $('#js-ajax-nav-section-catalog');
    var itemNextPage = parseInt(container.attr('data-next-page'));
    var itemNavNum = parseInt(container.attr('data-nav-num'));
    if (!itemNextPage) {
        itemNextPage = 2;
    }
    var itemLastPage = container.attr('data-last-page');
    var favoriteUrl = $(element).attr('data-ajax-url');
    var favoriteArray = $(element).attr('data-favorites-list');
    if (favoriteUrl !== '' && favoriteArray !== '') {
        itemLink = favoriteUrl + '?lang=' + LANGUAGE_ID
            + '&json_favorites=' + favoriteArray
            + '&ajax_page=Y&PAGEN_' + itemNavNum + '=' + itemNextPage
            + '&items=Y';
    } else {
        if (document.location.search) {
            itemLink = document.location.pathname + document.location.search;
            itemLink += "&ajax_page=Y&PAGEN_" + itemNavNum + "=" + itemNextPage + '&items=Y';
        } else {
            itemLink = document.location.pathname;
            itemLink += "?ajax_page=Y&PAGEN_" + itemNavNum + "=" + itemNextPage + '&items=Y';
        }
    }
    // console.log(favoriteArray);
    // console.log(itemLink);

    if (itemNextPage <= itemLastPage) {
        $('.js-ajax-load-items-button').css('opacity', '0.5');
        $.get(
            itemLink,
            {},
            function (data) {
                container.attr('data-next-page', itemNextPage+1);
                container.append(data);
                $('.js-ajax-load-items-button').eq(0).remove();
                initListOwl();
                // ???????????????????? ?????????????????? ?????????????? ???? ajax ?????? ????????????
                if (container.hasClass('ajaxAutoLoadActive')) {
                    setTimeout(function () {
                        //$(window).on('scroll', scrollingTotalLooksAjaxLoad);
                        $(window).on('scroll', scrollingAjaxLoad);
						// ???????????????????? ?????????? ?????? ????????????
						$('#js-sticker').sticky({
							topSpacing: 0
						});
						var tempScrollTop = 0;
						var currentScrollTop = 0;
						var scrollFlag = false;
						$(window).scroll(function (e) {
							if (scrollFlag === true) {
								return false;
							}
							scrollFlag = true;
							setTimeout(function () {
								e.stopPropagation();
								currentScrollTop = $(window).scrollTop();
								if (tempScrollTop < (currentScrollTop - 30)) { //scrolling down
									headerSkickAnimation('down');
									tempScrollTop = currentScrollTop;
								} else if ((tempScrollTop > (currentScrollTop + 120)) || (currentScrollTop < 10)) { //scrolling up
									headerSkickAnimation('up');
									tempScrollTop = currentScrollTop;
								}
								scrollFlag = false;
							}, 200);
						});
                    }, 500);
                }
            }
        );
    }

    return false;
}



/**
 * ?????? ???????????? ?????????? ???? ???????????? ????????, ???????????????????? ???????????????????????????? ?????????????????? ??????????????
 */
$(window).on('scroll', scrollingAjaxLoad);

function scrollingAjaxLoad() {
    if ($('#js-ajax-nav-section-catalog').hasClass('ajaxAutoLoadActive') && $('.js-ajax-load-items-button').length > 0) {
		var currentHeight = $('body').height();
		if ( $(window).scrollTop() >= $('.js-ajax-load-items-button').offset().top - $(window).height() ) {
			// ???????????????????? ???????????? ?????????????? ?????????????????? ???? ?????????????????? ???????????????????????????? ???????????? ??????????????
			$(this).unbind('scroll');
			// ???????????????????? ???????????? ???? ajax
			$('#js-ajax-nav-section-catalog').find('.js-ajax-load-items-button > a').click();
		}
	}
}