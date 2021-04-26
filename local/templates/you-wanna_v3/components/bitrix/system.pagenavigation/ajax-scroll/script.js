$(document).ready(function(){
    var loadmore = false;
    if(typeof callback === "function") {

    }

    $(document).on('scroll.ajax', function () {
        
        var targetContainer = $('[data-ajax=list]').last(),
            btnContainer = $('[data-ajax=btn-container]').last(), //  Контейнер, в котором хранятся элементы
            url =  $('.btn-loadmore').attr('data-url'), //  URL, из которого будем брать элементы
            s_top = $(document).scrollTop(),
            yes = $('[data-ajax=item]').last().offset().top - 600;
            if(s_top > yes && !loadmore){
                loadmore = true;

                $('[data-ajax=btn]').remove(); // Удаляем старую навигацию сразу, чтобы не хватало новый контент по несколько раз

                if (url !== undefined) {
                    $.ajax({
                        type: 'GET',
                        url: url,
                        data: {
                            'ajax': 'Y'
                        },
                        dataType: 'html',
                        success: function(data){
                            console.log("Yes");

                            var elements = $(data).find('[data-ajax=item]'),  //  Ищем элементы
                                pagination = $(data).find('[data-ajax=btn]');//  Ищем навигацию

                            btnContainer.append(pagination);     //  добавляем навигацию следом
                            targetContainer.append(elements);   //  Добавляем посты в конец контейнера

                            loadmore = false;

                        }
                    }).done(function() {
                        $(document).trigger('initTS');
                    });

                    
                } else {
                    $(document).off('scroll.ajax'); 
                    console.log("off");                   
                }
            }

    });
});