$(document).ready(function () {
    initListOwl();
    initDetailCatalogTrigger();
});

$(document).ready(function () {
	
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