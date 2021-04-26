function yaTranslate(text, lang, lang2) {
    var translate_url = 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=[apikey]&text=[text]&lang=[lang]&format=[format]';
    var langFrom = lang2 ? lang2 : 'ru';

    translate_url = translate_url
        .replace(/\[apikey\]/, YANDEX_TRANSLATE_API_KEY)
        .replace(/\[lang\]/, langFrom + '-' + lang)
        .replace(/\[text\]/, encodeURIComponent(text))
        .replace(/\[format\]/, 'plain');

    var translated = '';

    $.when(makeTranslate(translate_url)).done(function (result) {
        if (('code' in result) && (result.code === 200) && ('text' in result)) {
            translated = result.text[0];
        }
    });

    return translated;
}

function makeTranslate(translate_url) {

    return $.ajax({
        dataType: "json",
        url: translate_url,
        async: false,
        data: {}
    });
}