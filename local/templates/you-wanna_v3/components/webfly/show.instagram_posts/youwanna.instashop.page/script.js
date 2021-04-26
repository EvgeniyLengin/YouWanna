/**
 * Ajax подгрузка товаров, вместо пагинации
 */
function ajaxLoadCatalogSectionElements(element) {

    var itemLink = '';
    var container = $('.instashop-page-wrapper');
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
            + '&ajax_page=Y&more-posts=page-' + itemNavNum + '=' + itemNextPage
            + '&items=Y';
    } else {
        if (document.location.search) {
            itemLink = document.location.pathname + document.location.search;
            itemLink += "&ajax_page=Y&more-posts=page-" + itemNextPage + '&items=Y';
        } else {
            itemLink = document.location.pathname;
            itemLink += "?ajax_page=Y&more-posts=page-" + itemNextPage + '&items=Y';
        }
    }
    console.log(favoriteArray);
    console.log(itemLink);

    if (itemNextPage <= itemLastPage) {
        $('.js-ajax-load-items-button').css('opacity', '0.5');
        $.get(
            itemLink,
            {},
            function (data) {
                container.attr('data-next-page', itemNextPage+1);
                container.append(data);
                console.log($('.js-ajax-load-items-button').eq(0));
                $('.js-ajax-load-items-button').eq(0).remove();
                // Возвращаем подгрузку товаров по ajax при скроле
                if (container.hasClass('ajaxAutoLoadActive')) {
                    setTimeout(function () {
                        $(window).on('scroll', scrollingTotalLooksAjaxLoad);
                        $(window).on('scroll', scrollingAjaxLoad);
                    }, 500);
                }
            }
        );
    }

    return false;
}



/**
 * При скроле почти до самого низа, активируем автоматическую подгрузку товаров
 */
$(window).on('scroll', scrollingAjaxLoad);

function scrollingAjaxLoad() {
    if ($('#js-ajax-nav-section-catalog').hasClass('ajaxAutoLoadActive')) {
        var currentHeight = $('body').height();
        if ($(this).scrollTop() >= (currentHeight - $(this).height() - 800)) {
            // Отключение вызова функции прокрутки во избежание неоднократного вызова функции
            $(this).unbind('scroll');
            // Подгружаем товары по ajax
            ajaxLoadCatalogSectionElements();
        }
    }
}