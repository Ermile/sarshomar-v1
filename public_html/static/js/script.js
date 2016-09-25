$('#tag-add').keypress(function(e) {
    // if Enter pressed disallow it and run add func
    if (e.which == 13) { addTag();
        return false; }
});
$(document).on('click', '#tag-add-btn', function() { addTag(); });
$(document).on('click', '#tag-list span i', function() {
    var span = $(this).parent();
    $('#sp-tags').val($('#sp-tags').val().replace(span.text() + ', ', ''));
    span.remove();
});

$(document).ready(function() {
    var tagDefault = $('#sp-tags').val();
    $('#tag-list').text('');
    if (tagDefault) {
        $.each(tagDefault.split(', '), function(t, item) {
            if (item.trim())
                $('#tag-list').append("<span><i class='fa fa-times'></i>" + item + "</span>");
        });
    }
});

function addTag() {
    var tag = $('#tag-add');
    var newTag = tag.val().trim();
    if (newTag) {
        var exist = false;
        $.each($('#sp-tags').val().split(', '), function(t, item) {
            if (item == newTag) { exist = t + 1; }
        });
        if (exist) {
            existEl = $("#tag-list span:nth-child(" + exist + ")");
            bg = existEl.css('background-color');
            existEl.css('background-color', '#ddd');
            setTimeout(function() { existEl.css("background-color", bg) }, 500);
        } else {
            $('#tag-list').append("<span><i class='fa fa-times'></i>" + newTag + "</span>");
            $('#sp-tags').val($('#sp-tags').val() + newTag + ', ');
        }
    }
    tag.val('');
}
