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

    // add tab support to cp
    $('.tabs li').click(function()
    {
      var _this     = $(this);
      var tabNum    = _this.attr('data-tab');
      var tabsItems = _this.parent().children('li');
      var tabGroup  = _this.parent().attr('data-group');
      var tabSelected;
      var tabItems
      // if use group find it else use default tab value
      if(tabGroup)
      {
        tabItems  = $('.tab[data-group="'+ tabGroup +'"]');
      }
      else
      {
        tabItems  = $('.tab');
      }

      // remove active class from all items and select clicked item
      tabsItems.removeClass('active');
      _this.addClass('active');

      if(tabNum)
      {
        tabSelected = tabItems.children("#tab-"+tabNum);
        // $('[id^=tab-]').not(tabSelected).css('display', "none");
        tabItems.children('[id^=tab-]').not(tabSelected).css('display', "none");
      }
      else
      {
        tabNum = _this.index()+1;
        tabSelected = tabItems.children("li:nth-child("+tabNum+")");
        tabItems.children('li').not(tabSelected).css('display', "none");
      }
      $(tabSelected).fadeIn(300);
    })
    // run click for first time and show content of active tab
    $(".tabs").each(function()
    {
      // if select one element as active select content of it
      if($(this).children('li.active').length == 1)
      {
        $(this).children('li.active').trigger("click");
        $('input[name="poll_type"]').val( $(this).children('li.active').data('tab') );
      }
      // else select first child
      else
      {
        $(this).children('li:first-child').trigger("click");
        $('input[name="poll_type"]').val($(this).children('li:first-child').data('tab'));
      }
    });

    // change poll_type on tabs click
    $('.tabs li').click(function(){
      $('input[name="poll_type"]').val( $(this).data('tab') );
    });

    // $('input[name="title"]').on('input', function(){
    //     $('input[name="secondary-title"]').val($(this).val());
    // });
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

$(function(){
	$('.input-slider').each(function(id, el){
		$(el).append($('<div style="width: 10px;height: 25px;background: #666;position: absolute;top: 5px;left: 100px;cursor:pointer"></div>'));

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

		updateSlider();

		function updateSlider() {
			var val = parseFloat(inpEl.val());
			var newLeft = offset + ((val - minVal) / (maxVal - minVal)) * maxim;
			sl.css('left', newLeft);
		}
	});

	$('.panel-text>.title>span').click(function(){
    	$(this).parents('.panel-text').children('textarea').stop().slideToggle('fast');
    	$(this).children('.fa').toggleClass('fa-minus fa-plus');
  	});
});






