$(function () {
    $('.js-select-cities-letter').click(function (e) {

        e.preventDefault();

        var $this = $(this);
        if (!$this.hasClass('selected')) {
            $('.js-select-cities-letter').removeClass('selected');
            $this.addClass('selected');
            var letter = $(this).text();

            $('.cities-c .item, .cities-c .item a').removeClass('selected').addClass('unselected');
            var $cities = $('.cities-c .item a[data-letter=' + letter + ']');
            $cities.addClass('selected').removeClass('unselected');
            $cities.parent().addClass('selected').removeClass('unselected');
        }
        else {
            $('.js-select-cities-letter').removeClass('selected');
            $('.cities-c .item, .cities-c .item a').removeClass('selected').removeClass('unselected');
        }

        return false;
    });

    $('.js-select-cities-show-city-shops').click(function (e) {
        e.preventDefault();
        $(this).siblings('.city-shops').toggle();
        return false;
    });
    $('.shop-item .toggle').click(function() {
        $('.shops-menu .hm').toggle();
    });
});
