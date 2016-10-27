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
        $('input[name="poll_type"], input[name="filter_type"]').val( $(this).children('li.active').data('tab') );
      }
      // else select first child
      else
      {
        $(this).children('li:first-child').trigger("click");
        $('input[name="poll_type"], input[name="filter_type"]').val($(this).children('li:first-child').data('tab'));
      }
    });

    // change poll_type on tabs click
    $('.tabs li').click(function(){
      $('input[name="poll_type"], input[name="filter_type"]').val( $(this).data('tab') );
    });


});

route('@/add', function(doc) {
  $(document).on('input', '.option .input[type="text"]', function(event) {
    $(this).formFunc();
  });
});

(function($) {
    var element = $('<div class="element option"><label class="addon" for="answer2">Answer 2</label><input class="input" type="text" name="answers[]" id="answer2"><input type="hidden" name="answer_true[]" value="true"><input type="hidden" name="answer_type[]" value="text"></div>');

    $.fn.formFunc = function()
    {
      var is_null = 0;

      $.each($('.option .input[type="text"]'), function(key, value)
      {
        if ( !$(this).val() && is_null === 0 )
        {
          is_null++;
        }
      });

      if (is_null === 0)
      {
        var len = $('.option .input[type="text"]').length;
        var _element = $('.option .input[type="text"]').eq(len - 1);
        _element = _element.parents('.element.option');
        var num = _element.data('number') + 1;
        _element = _element.clone();
        _element.children('label').text('answer' + num).attr('for', 'answer' + num);
        _element.children('.input').attr('id', 'answer' + num).val();
        _element.children('.input').val('');
        $('.input-group').append(_element);
        _element.addClass('animated fadeInDown');
      }
      else
      {
        console.log(0);
      }
    }
}(jQuery));

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














// -------------------------------------------------- Features
var getDirection = function (ev, obj) {
    var w = obj.offsetWidth,
        h = obj.offsetHeight,
        x = (ev.pageX - obj.offsetLeft - (w / 2) * (w > h ? (h / w) : 1)),
        y = (ev.pageY - obj.offsetTop - (h / 2) * (h > w ? (w / h) : 1)),
        d = Math.round( Math.atan2(y, x) / 1.57079633 + 5 ) % 4;

    return d;
};

var addClass = function ( ev, obj, state ) {
    var direction = getDirection( ev, obj ),
        class_suffix = "";

    obj.className = "";

    switch ( direction ) {
        case 0 : class_suffix = '-top';    break;
        case 1 : class_suffix = '-right';  break;
        case 2 : class_suffix = '-bottom'; break;
        case 3 : class_suffix = '-left';   break;
    }

    obj.classList.add( state + class_suffix );
};


$('#features .wrapper .features li').on("mouseover", function (ev) { addClass( ev, this, 'in' ); });
$('#features .wrapper .features li').on("mouseout", function (ev) { addClass( ev, this, 'out' );});
