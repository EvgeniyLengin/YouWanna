$(document).ready(function(){
    $('.last_item').click(function(){
        if ($(this).find('ul.hidden_elements').css('display') === 'none') {
            $(this).find('ul.hidden_elements').show();
        } else {
            $(this).find('ul.hidden_elements').hide();
        }
    })
})