// contact form
route(/contact/, function()
{
	$('form').on('ajaxify:success',function(data, debug)
	{
		if(debug.status)
		{
			$('input').val('');
			$('textarea').val('');
		}
	});
});


// Add
route(/\@\/add/, function()
{
	$(this).on('click','button', function()
	{
		$('#submit-form').attr("value", $(this).attr("send-name"));
		$('#submit-form').attr("name", $(this).attr("send-name"));
	});
	// run on input change
	$(this).on('input', '.element.small .input[type="text"]', function(event)
	{
		var number_of_empty_inputs = 0;

		// check if current element has not value and we have no empty inputs
		$.each($('.element.small .input[type="text"]'), function(key, value)
		{
			if ( !$(this).val() && number_of_empty_inputs === 0 )
			{
				number_of_empty_inputs++;
			}
		});

		// if we had no empty inputs and we needed one do this
		if (number_of_empty_inputs === 0)
		{
			var template = $('.element.small .input[type="text"]').eq(0).parents('.element.small').clone();
			var num = $('.element.small .input[type="text"]').length + 1;
			template.children('label').text('answer ' + num).attr('for', 'answer' + num);
			template.children('.input').attr('id', 'answer' + num);
			template.children('.input').val('');
			$('.input-group').append(template);
			template.addClass('animated fadeInDown');
		}

		// if we had empty inputs do this
		else
		{

		}
	});

	$(this).on('mouseenter', '.element.small', function()
	{
		$(this).children('.delete').stop().fadeIn(300);
	}).on('mouseleave', '.element.small', function()
	{
		$(this).children('.delete').stop().fadeOut(300);
	});

	$(this).on('click', '.element.small .delete', function()
	{
		if ($('.element.small').length > 2)
		{
			$(this).parents('.element.small').remove();
		}
		else
		{
			alert('You should have at least two answers.');
		}
	})

	$(this).on('focus', '.element.small input[type="text"]', function()
	{
		// always show delete button on input focus
	});

	$(this).on('click', '.questions > li > div', function(event)
	{
		$(this).parents('li').children('.answers').slideToggle();
	});

	$(this).on('change', '.answers > .ac-checkbox input[type="checkbox"]', function(event)
	{
		set_tree( $(this).attr('name') );
	});

	var questions   = [];
	var answers     = [];

	function set_tree(name)
	{
		var _name       = name.split('-');
		var question_id = _name[0];
		var answer_id   = _name[1];

		var old_id = $('input[name=parent_tree_id]').val();
		if(old_id != question_id)
		{
			$('input[name=parent_tree_id]').val(question_id);
			$('input[name=parent_tree_opt]').val('');
		}
		var old_opt = $('input[name=parent_tree_opt]').val();

		split_opt = old_opt.split(',');
		if(split_opt[0] == '')
		{
			$('input[name=parent_tree_opt]').val(answer_id);
		}
		else
		{
			if(split_opt.indexOf(answer_id) == -1)
			{
				$('input[name=parent_tree_opt]').val($('input[name=parent_tree_opt]').val() + ',' + answer_id);
			}
			else
			{
				split_opt.splice(answer_id, 1);
				$('input[name=parent_tree_opt]').val(split_opt);
			}
		}

	}

	$(this).on('input', '#search', function(event)
	{
	  repository = $("#repository").val();
	  search     = $(this).val();
	  $(this).ajaxify(
	  {
	    ajax:
	    {
	      url: '@/add',
	      method: 'post',
	      data:
	      {
	        'repository': repository,
	        'search': search
	      },
	      abort: true,
	      success: function(e)
	      {
	      	$('.questions').html('');
	      	var el = '';

	        for (var r in e.msg.result)
	        {
	        	var id = e.msg.result[r].id;
	        	el += '<li>';
			  		el = el + '<div>' + e.msg.result[r].title + '</div>';
	          el += '<ul class="answers">';

	          for (var a in e.msg.result[r].meta.opt)
	          {
	            el += '<li class="ac-custom ac-checkbox ac-checkmark">';
	            el = el + '<input type="checkbox" name="' + id + '-' + e.msg.result[r].meta.opt[a].key + '" id="'+  id + '-' +e.msg.result[r].meta.opt[a].key +'">';
	            el = el + '<label for="' + id + '-' + e.msg.result[r].meta.opt[a].key + '">' + id + '-' + e.msg.result[r].meta.opt[a].txt + '</label>';
	            el += '</li>';
	          }
	          el += '</ul>';
	        	el += '</li>';
	        }

	        $('.questions').append($(el));
	      }
	    }
	  });
	});
});


// Me | Profile
route(/\@\/me/, function ()
{
	// btns is generated in display in order to true translation
	$.each($('.element.raw'), function (key, value)
	{
		if ($(this).children('.input').val())
		{
			$(this).dblclick(function ()
			{
				if (!$('.main').hasClass('editing'))
				{
					$('.main').addClass('editing');
					$(this).removeClass('raw').append(btns).children('.input').removeAttr('disabled').focus();
				}
			});
		}
		else
		{
			$(this).click(function ()
			{
				if (!$('.main').hasClass('editing'))
				{
					$('.main').addClass('editing');
					$(this).removeClass('raw').append(btns).children('.input').removeAttr('disabled').focus();
				}
			});
		}
	});

	$(this).on('blur', '.element .input', function (event)
	{
		$('.main').removeClass('editing');
	});

	$(this).on('click', '.btn.save button', function (event)
	{
		var val = $(this).parents('.element').children('.input').val();
		var name = $(this).parents('.element').children('.input').attr("name");
		$(this).ajaxify(
		{
			ajax:
			{
				data:
				{
					'name': name,
					'value': val
				},
				abort: true,
				success: function(e, data, x)
				{

				},
				url: '@/me',
				method: 'post'
			}
		});
	});
});


// pollsearch | Knowledge
route(/^\/?(fa\/)?\$(.*)$/, function ()
{
	var change = 0;
	$('.pollsearch', this).keyup(function(e)
	{
		var val = $(this).val();
		change  += 1;

		setTimeout(function()
		{
			if(change <= 1)
			{
				if(val)
				{
					val = '/search=' + val;
				}
				Navigate({ url: '$' + val });
			}
				change -= 1;
		}, 250);
	});
	$('.pollsearch', this).focus().val($('.pollsearch').val());
});


// Me | Profile
route('*', function ()
{
	$.each($('input.autocomplete'),function()
	{
		$(this).keyup(function(e)
		{
			name = $(this).attr('name');
			val = $(this).val();
			$(this).ajaxify(
			{
				ajax:
				{
					method: 'post',
					data:
					{
						'type'  : 'autocomplete',
						'data'  : name,
						'search': val
					},
					abort: true,
					success: function(e, data, x)
					{
						data = e.msg.callback;
						for (a in data)
						{
							// console.log(data[a]['term_title']);
							// console.log(data[a]['term_url']);
							// console.log(data[a]['term_count']);
						}
					}
				}
			});
		});
	});
});



route('*', function ()
{
	$('.similar-tag').keypress(function (e)
	{
		// if Enter pressed disallow it and run add func
		if (e.which == 13)
		{
			var element_id = $(this).attr('id');
			addTag(element_id);
			return false;
		}
	});

	$('.similar-tag').change(function ()
	{
		// if Enter pressed disallow it and run add func
		var element_id = $(this).attr('id');
		addTag(element_id);
		return false;
	});

	$(document).on('click', '.btn-add-tags' , function () { addTag($(this).attr('element-id')); return false; });
	$(document).on('click', '.remove-tags', function ()
	{
		var span = $(this).parent();
		var split = $(this).attr('data-split');
		$('#' + split).val($('#'+ split).val().replace(span.text() + ', ', ''));
		span.remove();
	});

	$('#features .wrapper .features li').on("mouseover", function (ev) { addClass( ev, this, 'in' ); });
	$('#features .wrapper .features li').on("mouseout", function (ev) { addClass( ev, this, 'out' );});

	// var tagDefault = $('#' + split).val();
	// $('#' + list).text('');
	// if (tagDefault)
	// {
	// 	$.each(tagDefault.split(', '), function (t, item)
	// 	{
	// 		if (item.trim())
	// 			$('#' + list).append("<span><i class='fa fa-times'></i>" + item + "</span>");
	// 	});
	// }

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


// -------------------------------------------------- Add Tag
function addTag(element_id)
{
	list  = $('#'+ element_id).attr('data-list');
	split = $('#'+ element_id).attr('data-split');

	var tag = $('#'+ element_id);
	var newTag = tag.val().trim();
	if (newTag)
	{
		var exist = false;
		$.each($('#'+ split).val().split(', '), function (t, item)
		{
			if (item == newTag) { exist = t + 1; }
		});
		if (exist)
		{
			existEl = $("#" + list + " a:nth-child(" + exist + ")");
			existEl.addClass("tag-exist");
			setTimeout(function () { existEl.removeClass("tag-exist") }, 500);
		}
		else
		{
			$('#' + list).append("<a><i class='fa fa-times remove-tags' data-split='"+split+"'></i>" + newTag + "</a>");
			$('#' + split).val($('#' + split).val() + newTag + ', ');
		}
	}
	tag.val('');
}


// --------------------------------- Sliders ---------------------------------

function pauseEvent(e)
{
	if (e.stopPropagation) e.stopPropagation();
	if (e.preventDefault) e.preventDefault();
	e.cancelBubble = true;
	e.returnValue = false;
	return false;
}

$(function()
{
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
var getDirection = function (ev, obj)
{
	var w = obj.offsetWidth,
		h = obj.offsetHeight,
		x = (ev.pageX - obj.offsetLeft - (w / 2) * (w > h ? (h / w) : 1)),
		y = (ev.pageY - obj.offsetTop - (h / 2) * (h > w ? (w / h) : 1)),
		d = Math.round( Math.atan2(y, x) / 1.57079633 + 5 ) % 4;

	return d;
};

var addClass = function ( ev, obj, state )
{
	var direction = getDirection( ev, obj ),
		class_suffix = "";

	obj.className = "";

	switch ( direction )
	{
			case 0 : class_suffix = '-top';    break;
			case 1 : class_suffix = '-right';  break;
			case 2 : class_suffix = '-bottom'; break;
			case 3 : class_suffix = '-left';   break;
	}

	obj.classList.add( state + class_suffix );
};


