/**
 * Created on 03.02.2017.
 */

var shopsPoints;
var shopsCitiesPlacemarks = {};
var shopsCountriesPlacemarks = {};
var shopsMap = false;
ymaps.ready(shopsInit);

function shopsInit() {

    console.log(shopsPoints);

    if (shopsPoints.length == 0) {
        return;
    }

    var coord1 = 55.76;
    var coord2 = 37.64;
    var zoom = 10;
    if (!shopsAllPoints || (shopsPoints.length == 1)) {
        zoom = 16;
        coord1 = 0.0;
        coord2 = 0.0;
        for (var i in shopsPoints) {
            coord1 += parseFloat(shopsPoints[i]['coords'][0]);
            coord2 += parseFloat(shopsPoints[i]['coords'][1]);
        }
        coord1 = coord1 / shopsPoints.length;
        coord2 = coord2 / shopsPoints.length;
    }

    myMap = new ymaps.Map('shops-map', {
        center: [coord1, coord2],
        zoom: zoom,
        openBalloonOnClick: false,
        controls: ["zoomControl"]
    });
    myMap.behaviors.disable('scrollZoom'); // Выключаем скрол при прокрутке колесом мыши
    shopsMap = myMap;

    var placemark;
    var placemarks = [];
    for (var i in shopsPoints) {
        coords = shopsPoints[i]['coords'];
        if (coords.length !== 2) {
            continue;
        }

        var content = '<div class="balloon">';
        content += '<span style="color:#222;">' + shopsPoints[i]['name'] + '</span><br/>';


        if (shopsPoints[i]['time'])
            content += '<br /><span style="color:#888;">Время работы: ' + shopsPoints[i]['time'] + '</span>';

        if (shopsPoints[i]['phone'])
            content += '<br /><span style="color:#888;">Телефон: ' + shopsPoints[i]['phone'] + '</span>';

        /* TODO надо ли отображать ссылку на страницу магазина в карточке на карте?
         if (shopsPoints[i]['url'])
         {
         content += '<br /><br /><a style="color:#888;text-decoration:underline;font-size:12px;" href="' + shopsPoints[i]['url'] + '">Открыть страницу магазина</a>';
         }
         */

        content += '</div>';

        placemark = new ymaps.Placemark(coords, {
            balloonContent: content
        });
        shopsPoints[i]['placemark'] = placemark;

        if (shopsPoints[i]['cityId'] !== 'undefined') {
            var cityId = shopsPoints[i]['cityId'];
            if (!shopsCitiesPlacemarks[cityId]) {
                shopsCitiesPlacemarks[cityId] = [];
            }
            shopsCitiesPlacemarks[cityId].push(placemark);
        }

        if (shopsPoints[i]['countryId'] !== 'undefined') {
            var cityId = shopsPoints[i]['countryId'];
            if (!shopsCountriesPlacemarks[cityId]) {
                shopsCountriesPlacemarks[cityId] = [];
            }
            shopsCountriesPlacemarks[cityId].push(placemark);
        }

        myMap.geoObjects.add(placemark);
        placemarks.push(placemark);
    }
    if (shopsPoints.length > 1) {
        myMap.setBounds(myMap.geoObjects.getBounds());
    }
}

$( function () {


    function ShopsFilterProcessSelects(step) {
        var langText = '';
        switch (step) {
            case 'country':
                langText = $('.js-filter-select-city').data('lang');
                $('.js-filter-select-city').attr('disabled', true).html('<option value="">' + langText + '</option>');
            case 'city':
                langText = $('.js-filter-select-metro').data('lang');
                $('.js-filter-select-metro').attr('disabled', true).html('<option value="">' + langText + '</option>').hide();
            case 'metro':
                langText = $('.js-filter-select-shop').data('lang');
                $('.js-filter-select-shop').attr('disabled', true).html('<option value="">' + langText + '</option>');
                break;
        }
    }

    function ShopsFilterShowByCityID(cityId) {
        $('#js-shops-map-search-input').val('');
        $('.js-shops-search-result').html('');

        if (cityId == '') {
            var countryId = $('.js-filter-select-country').val();
            ShopsFilterShowByCountryID(countryId);
            return;
        }
        if (shopsMap !== false) {
            var placemarksQty = 0;
            var pointA = false;
            var pointB = false;
            for (var id in shopsCitiesPlacemarks) {
                var isVisible = true;
                if (id != cityId) {
                    isVisible = false;
                }
                for (var placemark in shopsCitiesPlacemarks[id]) {
                    if (isVisible) {
                        placemarksQty++;
                        var currPoint = shopsCitiesPlacemarks[id][placemark].geometry.getBounds()[0];
                        currPoint[0] = parseFloat(currPoint[0]);
                        currPoint[1] = parseFloat(currPoint[1]);

                        if (pointA === false) {
                            pointA = [];
                            pointA.push(currPoint[0]);
                            pointA.push(currPoint[1]);
                        }
                        if (pointB === false) {
                            pointB = [];
                            pointB.push(currPoint[0]);
                            pointB.push(currPoint[1]);
                        }
                        if (pointA[0] > currPoint[0]) {
                            pointA[0] = currPoint[0];
                        }
                        if (pointA[1] > currPoint[1]) {
                            pointA[1] = currPoint[1];
                        }
                        if (pointB[0] < currPoint[0]) {
                            pointB[0] = currPoint[0];
                        }
                        if (pointB[1] < currPoint[1]) {
                            pointB[1] = currPoint[1];
                        }

                    }
                    shopsCitiesPlacemarks[id][placemark].options.set('visible', isVisible);
                }
            }
            
            var $city = $('.js-select-cities-show-city-shops[data-city-id="' + cityId + '"]');
            if ($city.length) {
                var $shops = $city.siblings('ul');
                $shops.find('li').each(function (inx, val) {
                    $(val).clone().appendTo('.js-shops-search-result');
                });
            }

            if (placemarksQty == 1) {
                shopsMap.setZoom(10);
                shopsMap.setCenter(pointA);
            }
            else if (placemarksQty > 1) {
                shopsMap.setBounds([pointA, pointB]);
            }
        }
    }

    function ShopsFilterShowByCountryID(countryId) {
        $('#js-shops-map-search-input').val('');
        $('.js-shops-search-result').html('');

        if (shopsMap !== false) {
            var placemarksQty = 0;
            var pointA = false;
            var pointB = false;
            for (var id in shopsCountriesPlacemarks) {
                var isVisible = true;
                if (countryId != '') {
                    if (id != countryId) {
                        isVisible = false;
                    }
                }
                for (var placemark in shopsCountriesPlacemarks[id]) {
                    if (isVisible) {
                        placemarksQty++;
                        var currPoint = shopsCountriesPlacemarks[id][placemark].geometry.getBounds()[0];
                        currPoint[0] = parseFloat(currPoint[0]);
                        currPoint[1] = parseFloat(currPoint[1]);

                        if (pointA === false) {
                            pointA = [];
                            pointA.push(currPoint[0]);
                            pointA.push(currPoint[1]);
                        }
                        if (pointB === false) {
                            pointB = [];
                            pointB.push(currPoint[0]);
                            pointB.push(currPoint[1]);
                        }
                        if (pointA[0] > currPoint[0]) {
                            pointA[0] = currPoint[0];
                        }
                        if (pointA[1] > currPoint[1]) {
                            pointA[1] = currPoint[1];
                        }
                        if (pointB[0] < currPoint[0]) {
                            pointB[0] = currPoint[0];
                        }
                        if (pointB[1] < currPoint[1]) {
                            pointB[1] = currPoint[1];
                        }
                    }
                    shopsCountriesPlacemarks[id][placemark].options.set('visible', isVisible);
                }
            }
            if (placemarksQty == 1) {
                shopsMap.setZoom(10);
                shopsMap.setCenter(pointA);
            }
            else if (placemarksQty > 1) {
                shopsMap.setBounds([pointA, pointB]);
            }
        }
    }

    function ShopsFilterShowByShopsList(shopList) {
        if (shopsMap !== false) {
            var placemarksQty = 0;
            var pointA = false;
            var pointB = false;
            for (var id in shopsPoints) {
                if (shopsPoints[id]['placemark'] !== false) {
                    var isVisible = true;
                    if (shopsPoints[id]['shopId'] != '') {
                        if (shopList.indexOf(parseInt(shopsPoints[id]['shopId'])) == -1) {
                            isVisible = false;
                        }
                        else {
                        }
                    }
                    if (isVisible) {
                        placemarksQty++;
                        var currPoint = shopsPoints[id]['placemark'].geometry.getBounds()[0];
                        currPoint[0] = parseFloat(currPoint[0]);
                        currPoint[1] = parseFloat(currPoint[1]);

                        if (pointA === false) {
                            pointA = [];
                            pointA.push(currPoint[0]);
                            pointA.push(currPoint[1]);
                        }
                        if (pointB === false) {
                            pointB = [];
                            pointB.push(currPoint[0]);
                            pointB.push(currPoint[1]);
                        }
                        if (pointA[0] > currPoint[0]) {
                            pointA[0] = currPoint[0];
                        }
                        if (pointA[1] > currPoint[1]) {
                            pointA[1] = currPoint[1];
                        }
                        if (pointB[0] < currPoint[0]) {
                            pointB[0] = currPoint[0];
                        }
                        if (pointB[1] < currPoint[1]) {
                            pointB[1] = currPoint[1];
                        }
                    }
                    shopsPoints[id]['placemark'].options.set('visible', isVisible);
                }
            }
            if (placemarksQty == 1) {
                shopsMap.setZoom(10);
                shopsMap.setCenter(pointA);
            }
            else if (placemarksQty > 1) {
                shopsMap.setBounds([pointA, pointB]);
            }
        }
    }

    $('.js-filter-select-country').change(function () {
        ShopsFilterProcessSelects('country');
        var countryId = $('.js-filter-select-country').val();
        var $selectCity = $('.js-filter-select-city');
        if (countryId) {

            var items = [];
            for(var i in shopsFilter[countryId]['CITIES'])
            {
                var country = shopsFilter[countryId]['CITIES'][i];
                items.push({
                    'ID' : country['ID'],
                    'NAME' : country['NAME'].charAt(0).toUpperCase() + country['NAME'].slice(1)
                });
            }
            items.sort(function (a, b) {
                if (a.NAME > b.NAME) {
                    return 1;
                }
                if (a.NAME < b.NAME) {
                    return -1;
                }
                return 0;
            });

            for (var i in items) {
                var country = items[i];
                $selectCity.append('<option value="' + country['ID'] + '">' + country['NAME'] + '</option>');
            }
            $selectCity.removeAttr('disabled');
        }
        ShopsFilterShowByCountryID(countryId);
    });

    $('.js-filter-select-city').change(function () {
        ShopsFilterProcessSelects('city');
        var countryId = $('.js-filter-select-country').val();
        var cityId = $('.js-filter-select-city').val();
        var $selectMetro = $('.js-filter-select-metro');
        var $selectShop = $('.js-filter-select-shop');
        if (cityId) {
            var cntMetro = 0;
            var cntShop = 0;

            for (var i = 0 in shopsFilter[countryId]['CITIES'][cityId]['METRO']) {
                cntMetro++;
                var metro = shopsFilter[countryId]['CITIES'][cityId]['METRO'][i];
                $selectMetro.append('<option value="' + metro['ID'] + '">' + metro['NAME'] + '</option>');
            }
            if (cntMetro > 0) {
                $selectMetro.removeAttr('disabled').show();
            }

            for (var i = 0 in shopsFilter[countryId]['CITIES'][cityId]['SHOPS']) {
                cntShop++;
                var shop = shopsFilter[countryId]['CITIES'][cityId]['SHOPS'][i];
                $selectShop.append('<option value="' + shop['DETAIL_PAGE_URL'] + '">' + shop['NAME'] + '</option>');
            }
            if (cntShop > 0) {
                $selectShop.removeAttr('disabled');
            }
        }

        ShopsFilterShowByCityID(cityId);

    });

    $('.js-filter-select-metro').change(function () {
        ShopsFilterProcessSelects('metro');
        var countryId = $('.js-filter-select-country').val();
        var cityId = $('.js-filter-select-city').val();
        var metroId = $('.js-filter-select-metro').val();
        var $selectShop = $('.js-filter-select-shop');
        if (countryId && cityId && metroId) {
            var cntShop = 0;

            for (var i = 0 in shopsFilter[countryId]['CITIES'][cityId]['METRO'][metroId]['SHOPS']) {
                cntShop++;
                var shop = shopsFilter[countryId]['CITIES'][cityId]['METRO'][metroId]['SHOPS'][i];
                $selectShop.append('<option value="' + shop['DETAIL_PAGE_URL'] + '">' + shop['NAME'] + '</option>');
            }
            if (cntShop > 0) {
                $selectShop.removeAttr('disabled');
            }
        }
    });

    $('.js-filter-select-shop').change(function () {
        var url = $(this).val();
        if (url) {
            location.href = url;
        }
    });

    var mapSearchTimeout = false;
    var mapSearchStr = '';

    function mapShowSearched() {
        var srcStr = $('#js-shops-map-search-input').val();
        var str = $('#js-shops-map-search-input').val().toUpperCase();

        $('.city-shops').hide();
        //$('.city-shops a').removeClass('state-found');
        $('.js-shops-search-result').html('');

        var shopsList = [];

        if (mapSearchStr !== str) {
            if (str != '') {
                $('.js-filter-select-country option').first().prop('selected', true).trigger('change');
                $('#js-shops-map-search-input').val(srcStr);

                var re = new RegExp('(' + str + ')', 'ig');
                $('.city-shops li a').each(function (inx, val) {
                    var text = $(this).text().toUpperCase();
                    if (text.indexOf(str) > -1) {
                        shopsList.push($(val).data('shop-id'));
                        //$(val).addClass('state-found');
                        //$(val).parents('ul.city-shops').show();
                        var $copy = $(val).parent().clone();
                        var name = $copy.find('a').html();
                        name = name.replace(re, "<span>$1</span>");
                        $copy.find('a').html(name);
                        $copy.appendTo('.js-shops-search-result');
                    }
                });
                ShopsFilterShowByShopsList(shopsList);
                if ($('.js-shops-search-result li').length == 0) {
                    $('.js-shops-search-result').append('<li>Магазины не найдены...</li>');
                }
            }
            else {
            }
        }
    }

    $('#js-shops-map-search-input').off('keydown');
    $('#js-shops-map-search-input').parents('form').submit(function (e) {
        e.preventDefault();
        return false;
    });
    $('body').on('keyup', '#js-shops-map-search-input', function (e) {
        if (mapSearchTimeout !== false) {
            clearTimeout(mapSearchTimeout);
        }
        if (e.keyCode != 13) {
            mapSearchTimeout = setTimeout(mapShowSearched, 1000);
        }
        else {
            mapShowSearched();
        }
    });

    $('.js-filter-select-country').trigger('change');

});
