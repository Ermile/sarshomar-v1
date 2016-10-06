$('#tag-add').keypress(function (e) {
    // if Enter pressed disallow it and run add func
    if (e.which == 13) {
        addTag();
        return false;
    }
});
$(document).on('click', '#tag-add-btn', function () { addTag(); });
$(document).on('click', '#tag-list span i', function () {
    var span = $(this).parent();
    $('#sp-tags').val($('#sp-tags').val().replace(span.text() + ', ', ''));
    span.remove();
});

$(document).ready(function () {
    var tagDefault = $('#sp-tags').val();
    $('#tag-list').text('');
    if (tagDefault) {
        $.each(tagDefault.split(', '), function (t, item) {
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
        $.each($('#sp-tags').val().split(', '), function (t, item) {
            if (item == newTag) { exist = t + 1; }
        });
        if (exist) {
            existEl = $("#tag-list span:nth-child(" + exist + ")");
            bg = existEl.css('background-color');
            existEl.css('background-color', '#ddd');
            setTimeout(function () { existEl.css("background-color", bg) }, 500);
        } else {
            $('#tag-list').append("<span><i class='fa fa-times'></i>" + newTag + "</span>");
            $('#sp-tags').val($('#sp-tags').val() + newTag + ', ');
        }
    }
    tag.val('');
}


// --------------------------------- Sliders ---------------------------------

function pauseEvent(e){
	if (e.stopPropagation) e.stopPropagation();
	if (e.preventDefault) e.preventDefault();
	e.cancelBubble = true;
	e.returnValue = false;
	return false;
}

//$(function(){
	$('.input-slider').each(function(id, el){
		$(el).append($('<div style="width: 10px;height: 25px;background: #666;position: absolute;top: 5px;left: 52px;cursor:pointer"></div>'));

		var offset = $(el.children[0]).outerWidth(),
			inpEl = $(el.children[1]),
		    maxim = inpEl.outerWidth() - 10,
		    sl = $(el.children[2]);

		inpEl.css({height: 8, margin: '11px 0' });
		inpEl.on('mousedown, mouseup', function(ev) {
				setSlider(offset + ev.screenX - inpEl.offset().left);
				return pauseEvent(ev);
			});


		var dragStart = false, currentLeft;
		sl.on('mousedown', function (ev) {
			dragStart = ev.screenX;
			currentLeft = parseFloat(sl.css('left'));
			inpEl.focus();
			return pauseEvent(ev);
		});


		var minVal = inpEl.data('min'), maxVal = inpEl.data('max');
		if(minVal==undefined) minVal = 0;
		if(maxVal==undefined) maxVal = 100;

		$(document).on('mousemove', function (ev) {
			if (dragStart && ev.buttons !== 0) {
				setSlider(currentLeft + ev.screenX - dragStart);
				return pauseEvent(ev);
			}
		});

		function setSlider(val) {
			var newLeft = Math.max(Math.min(val, offset + maxim), offset);
			sl.css('left', newLeft);
			inpEl.val( parseInt((newLeft-offset)*(maxVal-minVal) / maxim)+minVal );
			inpEl.focus();
		}

		$(document).on('mouseup', function(){
			dragStart = false;
		});
	});
//});






