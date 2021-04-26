function changeActiveGallery(color) {
    $('.product-gallery')
        .removeClass('product-gallery--active')
        .addClass('product-gallery--deactivated');
    $('.product-gallery[data-color=' + color + ']')
        .removeClass('product-gallery--deactivated')
        .addClass('product-gallery--active');

    if($('.product-gallery__full').hasClass('product-gallery__full--opened')) {
        $('.product-gallery__clear-photo').click();
    }
}

