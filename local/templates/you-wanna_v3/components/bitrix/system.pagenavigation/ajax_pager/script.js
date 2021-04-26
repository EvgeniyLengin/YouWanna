$(function() {
    var load_more = false;

    $(window).scroll(function() {
        if($("#ajax_next_page").length && !load_more) {
            var url = $("#ajax_next_page").attr("href");
            var offset_button = $("#ajax_next_page").offset();
            if($(this).scrollTop() >= offset_button.top - $(window).height()) {
                load_more = true;
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {IS_AJAX: 'Y'},
                    success: function(data) {
                        $("#ajax_next_page").after(data);
                        $("#ajax_next_page").remove();
                        load_more = false;
                    }
                });
            }
        }
    });
});