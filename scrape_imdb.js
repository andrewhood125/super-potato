var fullTitle = $('h1[itemprop=name]').html();
var title = fullTitle.substring(0, fullTitle.indexOf('&'));
var tags = [];
$('.itemprop').each(function(index, element) {
    if(element.innerHTML.indexOf('<') === -1) {
        tags.push(element.innerHTML);
    }
});

$.post('http://homestead.dev/videos/attach', {
    video: title,
    tags: tags
});
