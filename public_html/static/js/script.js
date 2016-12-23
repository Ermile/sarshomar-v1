var TEMP = null;


// Range Slider
!function(t){"use strict";function e(){t(this).removeClass("range-slider"),t(this).data("range-slider",void 0),t(this).init.prototype.range=void 0,t("*",this).remove()}function i(){t(this).rangeSlider("destroy"),t(this).rangeSlider()}t.fn.rangeSlider=function(n){t(this).each(function(){switch(n){case"destroy":return e.call(this);case"restart":return i.call(this);case"":return i.call(this)}t(this).hasClass("range-slider")&&t(this).rangeSlider("destroy"),t(this).trigger("range-slider::init::before"),t(this).addClass("range-slider");var r=t(this).attr("id");r&&(t('<input type="hidden" name="'+r+'-max" data-range-bind="'+r+'" data-range-value="max">').appendTo(this),t('<input type="hidden" name="'+r+'-min" data-range-bind="'+r+'" data-range-value="min">').appendTo(this));var s=t(this).attr("data-infinity"),d=t(this).attr("data-type");"vertical"!=d&&(d="horizontal",t(this).attr("data-type","horizontal"));var o=Number(t(this).attr("data-step"));isNaN(o)&&(o=1);var l=Number(t(this).attr("data-min"));isNaN(l)&&(l=0);var p=Number(t(this).attr("data-max"));(isNaN(p)||l>=p)&&(p=l+100);var h=p-l,m=Number(t(this).attr("data-min-default"));(isNaN(m)||m<l)&&(m=0);var u=Number(t(this).attr("data-max-default"));(isNaN(u)||p<u)&&(u=h),t(this).data("range-slider",{min:l,max:p,min_default:m,max_default:u,unit:h,step:o,type:d,margin:m,depth:p}),t(this).init.prototype.range=function(e,i,a){var n=e,r=i,s=this.data("range-slider"),d={type:"unit"};t.extend(d,a),d.from_type=d.type,d.to_type=d.type;var o="vertical"==s.type?"height":"width";null!==n&&n!==!1&&void 0!==n||(n=this.find(".dynamic-margin")[o](),"vertical"==s.type&&(n=this[o]()-(n+this.find(".dynamic-range")[o]())),d.from_type="pixel"),null!==r&&r!==!1&&void 0!==r||(r=this.find(".dynamic-margin")[o]()+this.find(".dynamic-range")[o](),"vertical"==s.type&&(r=this[o]()-this.find(".dynamic-margin")[o]()),d.to_type="pixel");var l=this[o]();"pixel"==d.from_type?n=n*s.unit/l:"percent"==d.from_type?n=n*s.unit/100:"ziro_unit"==d.from_type?n-=s.min:"step_plus"==d.from_type&&(n=n*s.step+s.from),"pixel"==d.to_type?r=r*s.unit/l:"percent"==d.to_type?r=r*s.unit/100:"ziro_unit"==d.to_type?r-=s.min:"step_plus"==d.to_type&&(r=r*s.step+s.to);var p=Math.round(n/s.step)*s.step,h=Math.round(r/s.step)*s.step;h<p&&(s.to==h?h=p:p=h),h>s.unit&&(h=s.unit),p>s.unit&&(p=s.unit),h<0&&(h=0),p<0&&(p=0);var m=this.attr("id");m&&t('[data-range-bind="'+m+'"]').each(function(){var e=t(this).attr("data-range-value");"max"==e?t(this).val(s.min+h):"min"==e&&t(this).val(s.min+p)}),n=100*p/s.unit,r=100*h/s.unit;var u=r-n;s.to==h&&s.from==p||(this.data("range-slider").from=p,this.data("range-slider").to=h,this.trigger("range-slider::change::before",[s.min+p,s.min+h]),"vertical"==s.type?this.find(".dynamic-margin").css(o,100-r+"%"):this.find(".dynamic-margin").css(o,n+"%"),this.find(".dynamic-range").css(o,u+"%"),this.trigger("range-slider::change",[s.min+p,s.min+h]))};var g=t("<div class='dynamic-margin'></div>"),c=t("<div class='dynamic-range'></div>");"max"==s?a.call(this,"min").appendTo(c):"min"==s?a.call(this,"max").appendTo(c):(a.call(this,"max").appendTo(c),a.call(this,"min").appendTo(c)),g.hide(),c.hide(),g.appendTo(this),c.appendTo(this),t(this).range(m,u),g.show(),c.show(),t(this).trigger("range-slider::init::after")})};var a=function(e){var i=t(this).data("range-slider"),a=this,n=t("<div class='"+e+"'></div>");return t(this).trigger("range-slider::selection",[n,e]),n.attr("tabindex","0"),n.unbind("mousedown.range-slider"),n.bind("mousedown.range-slider",function(){return t(document).unbind("mousemove.range-slider"),t(document).bind("mousemove.range-slider",function(n){var r="vertical"==i.type?n.pageY:n.pageX,s="vertical"==i.type?t(a).offset().top:t(a).offset().left,d=r-s;d="vertical"==i.type?t(a).height()-d:d,"max"==e?t(a).range(null,d,{type:"pixel"}):t(a).range(d,null,{type:"pixel"})}).bind("mouseup.range-slider",function(){t(document).unbind("mouseup.range-slider"),t(document).unbind("mousemove.range-slider")}),!1}).bind("mouseup",function(){t(document).unbind("mousemove.range-slider")}).bind("keydown.range-slider",function(e){var i,a;return i=1,a=null,t(this).is(".max")&&(i=null,a=1),38==e.keyCode||39==e.keyCode?(t(this).parents(".range-slider").range(i,a,{type:"step_plus"}),!1):37==e.keyCode||40==e.keyCode?(t(this).parents(".range-slider").range(i*-1,a*-1,{type:"step_plus"}),!1):void 0}),n}}(jQuery);

/* HTML5 Sortable jQuery Plugin -http://farhadi.ir/projects/html5sortable */
!function(t){var e,r=t();t.fn.sortable=function(a){var n=String(a);return a=t.extend({connectWith:!1},a),this.each(function(){if(/^(enable|disable|destroy)$/.test(n)){var i=t(this).children(t(this).data("items")).attr("draggable","enable"==n);return void("destroy"==n&&i.add(this).removeData("connectWith items").off("dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s"))}var s,d,i=t(this).children(a.items),o=t("<"+(/^(ul|ol)$/i.test(this.tagName)?"li":"div")+' class="sortable-placeholder">');i.find(a.handle).mousedown(function(){s=!0}).mouseup(function(){s=!1}),t(this).data("items",a.items),r=r.add(o),a.connectWith&&t(a.connectWith).add(this).data("connectWith",a.connectWith),i.attr("draggable","true").on("dragstart.h5s",function(r){if(a.handle&&!s)return!1;s=!1;var n=r.originalEvent.dataTransfer;n.effectAllowed="move",n.setData("Text","dummy"),d=(e=t(this)).addClass("sortable-dragging").index()}).on("dragend.h5s",function(){e&&(e.removeClass("sortable-dragging").show(),r.detach(),d!=e.index()&&e.parent().trigger("sortupdate",{item:e}),e=null)}).not("a[href], img").on("selectstart.h5s",function(){return this.dragDrop&&this.dragDrop(),!1}).end().add([this,o]).on("dragover.h5s dragenter.h5s drop.h5s",function(n){return i.is(e)||a.connectWith===t(e).parent().data("connectWith")?"drop"==n.type?(n.stopPropagation(),r.filter(":visible").after(e),e.trigger("dragend.h5s"),!1):(n.preventDefault(),n.originalEvent.dataTransfer.dropEffect="move",i.is(this)?(a.forcePlaceholderSize&&o.height(e.outerHeight()),e.hide(),t(this)[o.index()<t(this).index()?"after":"before"](o),r.not(o).detach()):r.is(this)||t(this).children(a.items).length||(r.detach(),t(this).append(o)),!1):!0})})}}(jQuery);

/**
 * check input for first time
 */
function runDataResponse()
{
	// on select and input check inputs
	$('input, select', this).change(function()
	{
		checkInputResponse(this, false);
	});
	// add data-response-realtime to run realtime on input type
	$('input[data-response-realtime]', this).keyup(function()
	{
		checkInputResponse(this, false);
	});

	// // run for the first time
	// $('input[type=checkbox]').each(function ()
	// {
	//   checkInputResponse(this, true);
	// });
}


/**
 * [getInputValue description]
 * @param  {[type]} _el   [description]
 * @param  {[type]} _type [description]
 * @return {[type]}       [description]
 */
function getInputValue(_el)
{
	var elValue;
	switch ($(_el).attr('type'))
	{
		case 'checkbox':
			elValue = $(_el).is(":checked");
			break;

		case 'radio':
			elValue = $(_el).val();
			break;

		case 'text':
		default:
			elValue = $(_el).val();
			break;
	}

	return elValue;
}


/**
 * check inputs and change status of them if needed
 * V2
 * @param  {[type]} _this      [description]
 * @param  {[type]} _firstTime [description]
 * @return {[type]}            [description]
 */
function checkInputResponse(_this, _firstTime)
{
	// declare variable
	var elResult;
	var elGroup    = false;
	var elID       = $(_this).attr('id');
	var elName     = $(_this).attr('name');
	var elType     = $(_this).attr('type');
	var elGroupEl  = $(_this).parents('[data-respnse-group]');
	var elGet      = $(_this).attr('data-response-get');
	var elValue    = getInputValue($(_this), elType);
	var elSelector = elName;
	// change selector if exist
	if(elGet == 'id')
	{
		elSelector = elID;
	}
	var childrens  = $('[data-response*="'+ elSelector +'"]');

	// if one of then parents of this element has data-response-group then check for group
	if(elGroupEl.length)
	{
		elGroupEl.each(function(index, el)
		{
			var myGroup = $(this).attr('data-respnse-group');
			elGroup     = myGroup;
			childrens   = childrens.add('[data-response*="'+ myGroup +'"]');
		});
	}

	childrens.each(function()
	{
		var effect     = $(this).attr('data-response-effect');
		var timing     = $(this).attr('data-response-timing');
		var where      = $(this).attr('data-response-where');
		var whereNot   = $(this).attr('data-response-where-not');
		var toggle     = $(this).attr('data-response-toggle');
		var disable    = $(this).attr('data-response-disable');
		var classTrue  = $(this).attr('data-response-class');
		var classFalse = $(this).attr('data-response-class-false');
		var callFn     = $(this).attr('data-response-call');
		var repeat     = $(this).attr('data-response-repeat');

		if(!disable && $(this).attr('disabled'))
		{
			disable = $(this).attr('disabled');
			$(this).attr('data-response-disable', 'disabled-manual');
		}

		// set effect name and default is fade
		if(effect == 'slide')
		{
			effect = {'name':'slide', 'toggle':'slideToggle', 'open':'slideDown', 'close':'slideUp'}
		}
		else
		{
			effect = {'name':'fade', 'toggle':'fadeToggle', 'open':'fadeIn', 'close':'fadeOut'}
		}
		if(!timing)
		{
			timing = 'fast';
		}

		// if we have group then check for all values
		if(elGroup)
		{
			var groupWhere       = elGroupEl.attr('data-response-where');
			var groupWhereNot    = elGroupEl.attr('data-response-where-not');
			var groupWhereResult = true;
			if(groupWhereNot)
			{
				groupWhereResult = false;
			}
			// check all input in this group
			elGroupEl.find('input').each(function(index, el)
			{
				// if need where not then in normal it's false
				// if one condition is exist then set to true
				if(groupWhereNot)
				{
					if(getInputValue(el).toString() !== groupWhereNot)
					{
						groupWhereResult = true;
					}
				}
				// if condition is normal where on group
				// then chack false status and if exist set as false
				// else in normal condition it's true
				else if(groupWhere)
				{
					if(getInputValue(el).toString() !== groupWhere)
					{
						groupWhereResult = false;
					}
				}
			});
			// save group where as final where
			where = groupWhereResult;
		}
		else
		{
			// check where and if want set true or false for where
			if(where)
			{
				// for each sentence in where seperated by |
				$.each(where.split('|'), function(index, whereValue)
				{
					// if where is okay
					if(whereValue == elValue.toString())
					{
						where = true;
					}
				});
				// if where is not true set it as false
				if(where !== true)
				{
					where = false;
				}
			}
			else
			{
				if(elValue != false)
				{
					where = true;
				}
			}
		}

		// if wanna to toggle element do this
		if(toggle)
		{
			$(this)[effect['effect']](timing);
		}
		else
		{
			// show and hide elements depending on parent change
			if(where)
			{
				if(disable)
				{
					$(this).prop('disabled', false);
				}
				else
				{
					// if have class name toggle class name
					if(classTrue !== undefined)
					{
						$(this).addClass(classTrue);
						$(this).removeClass(classFalse);
					}
					else if(repeat != undefined)
					{

					}
					else
					{
						// if condition is true run effect
						$(this).attr('data-response-hide', null);
						$(this)[effect['open']](timing);
					}
				}
				if($(this).find('[data-response-focus]').length)
				{
					$(this).find('[data-response-focus]').focus();
				}
				else
				{
					$(this).closest('[data-response-focus]').focus();
				}
				elResult = 'open';
			}
			else
			{
				if(disable)
				{
					$(this).prop('disabled', true);
				}
				else
				{
					// if have class name toggle class name
					if(classTrue !== undefined)
					{
						$(this).addClass(classFalse);
						$(this).removeClass(classTrue);
					}
					else if(repeat != undefined)
					{

					}
					else
					{
						// if condition is false run effect
						$(this)[effect['close']](timing, function()
						{
							$(this).attr('data-response-hide', '');
						});
					}
				}
				elResult = 'close';
			}
		}
		// if wanna to repeat something
		if(repeat != undefined)
		{
			var parentValue = '';
			switch(elType)
			{
				case 'checkbox':
					parentValue = $(_this).is(":checked");
					$(this).prop('checked', parentValue);
					break;

				case 'radio':
				case 'text':
				default:
					parentValue = $(_this).val();
					$(this).val(parentValue);
					break;
			}
		}
		// if want to call
		if(callFn)
		{
			// true means open and false means close
			if(elResult == true)
			{
				elResult = true;
			}
			else
			{
				elResult = false;
			}
			// call function with 3 paramenter
			// first is status
			// second is current el
			// third is parent el
			if (typeof window[callFn] === "function")
			{
				window[callFn](elResult, $(this), $(_this));
			}
		}
	});

	$(window).trigger('response:open', [elSelector, elResult]);
}


/**
 * fix problem of jumping on first slide with set height
 * @return {[type]} [description]
 */
function fixSlideJumping()
{
	$('[data-response-hide][data-response-effect="slide"]:not([data-response-notfix])', this).css('height',function(i,h)
	{
		$(this).hide();
		return h;
	});
}


/**
 * allow textarea to be resizable
 * @return {[type]} [description]
 */
function resizableTextarea()
{
	$('textarea[data-resizable]', this).keyup(function()
	{
		$(this).css( "height", function( index )
		{
			return $(this)[0].scrollHeight;
		});
	});
}



// ================================================================== run on all part of each page
route('*', function ()
{
	// run data-response detector
	runDataResponse.call(this);
	// run rangeSlider on all pages
	$(".range-slider", this).rangeSlider();
	// run func to fixSlideJumping
	fixSlideJumping.call(this);
}).once(function()
{
});


// ************************************************************************************************************ Add
/**
 * add new record of answer
 */
function checkAddOpt()
{
	var numberOfEmptyInputs = 0;
	var emptyRowNumber;

	// check if current element has not value and we have no empty inputs
	$.each($('.input-group.sortable .element .input[type="text"]'), function(key, value)
	{
		if ( !$(this).val() && numberOfEmptyInputs === 0 )
		{
			numberOfEmptyInputs++;
			emptyRowNumber = key;
		}
	});

	// if we had no empty inputs and we needed one do this
	if (numberOfEmptyInputs === 0 && !$('.input-group.sortable').hasClass('editing'))
	{
		addNewOpt();
	}
	// if we had empty inputs do this
	else
	{
		// highlight empty row
	}
}


/**
 * add new option to items
 */
function addNewOpt(_group, _title)
{
	var template = $('.input-group.sortable>li').eq(0).clone();
	var num = $('.input-group.sortable>li').length + 1;
	if(_group)
	{
		template.attr('data-profile', _group);
	}
	if(_title)
	{
		template.find('.element label.title').text(_title);
	}
	else
	{
		// if language is farsi then convert number to persian
		if($('html').attr('lang') === 'fa')
		{
			template.find('.element label.title b').text(num.toString().toFarsi());
		}
		else
		{
			template.find('.element label.title b').text(num);
		}
	}
	template.find('.element label.title').attr('for', 'answer' + num);
	template.find('.element .input').attr('id', 'answer' + num);
	template.find('.element .input').attr('name', 'answer' + num);
	template.find('.element .input').attr('value', '');
	template.find('.element .input').val('');
	// set true
	template.find('.element .true input').attr('name', 'true' + num);
	template.find('.element .true label').attr('for', 'true' + num);
	template.find('.element .true input').attr('id', 'true' + num);
	template.find('.element .true input').attr('checked', false);;
	// set score
	template.find('.element .score input').attr('name', 'score' + num);
	template.find('.element .score input').attr('id', 'score' + num);
	template.find('.element .score input').val('');
	template.attr('data-row', num);

	$('.input-group.sortable').append(template);
	template.addClass('animated fadeInDown').delay(1000).queue(function()
	{
		$(this).removeClass("animated fadeInDown").dequeue();
	});
	setSortable();
}


/**
 * [showQuestionOptsDel description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function showQuestionOptsDel(_this)
{
	// hide all elements
	$('.input-group.sortable .element .delete').fadeOut(100);
	var currentRowValue = $(_this).parent('.element').children('input[type="text"]').val();
	// always show delete button on input focus
	if(countQuestionOpts() > 2 && currentRowValue)
	{
		$(_this).stop().fadeIn(200);
	}
}


/**
 * delete selected opt and do some event after that
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function deleteQuestionOpts(_this)
{
	var currentRowValue = $(_this).closest('li').find('input[type="text"]').val();
	if (countQuestionOpts() > 2 && currentRowValue)
	{
		$(_this).closest('li').addClass('animated fadeOutSide').slideUp(200, function()
		{
			// set focus to next input
			$(this).closest('li').next().find('input').focus();
			// remove element
			$(this).remove();
			// rearrange question opts
			rearrangeQuestionOpts();
			// recalc percentage of progress bar
			detectPercentage();
		});
	}
}


/**
 * generate sortable again and again after each change
 */
function setSortable(_onlyDestroy)
{
	$('.sortable').sortable('destroy');
	if(!_onlyDestroy)
	{
		$('.sortable').sortable({handle: '.title'}).bind('sortupdate', function(e, ui)
		{
			rearrangeQuestionOpts();
		});
	}
}


/**
 * rearrange number of opts in question
 * @return {[type]} [description]
 */
function rearrangeQuestionOpts()
{
	$.each($('.input-group.sortable li'), function(key, value)
	{
		var row = key+1;
		$(this).attr('data-row', row);
		$(this).find('.element label').attr('for', 'answer' + row);
		// if language is farsi then convert number to persian
		if($('html').attr('lang') === 'fa')
		{
			$(this).find('.element label.title b').text(row.toString().toFarsi());
		}
		else
		{
			$(this).find('.element label.title b').text(row);
		}
		$(this).find('.element .input').attr('id', 'answer' + row);
		$(this).find('.element .input').attr('name', 'answer' + row);
		// set true
		$(this).find('.element .true input').attr('id', 'true' + row);
		$(this).find('.element .true label').attr('for', 'true' + row);
		$(this).find('.element .true input').attr('name', 'true' + row);
		// set score
		$(this).find('.element .score input').attr('id', 'score' + row);
		$(this).find('.element .score input').attr('name', 'score' + row);
	});
}


/**
 * return count of question options exist in page
 * @return {[type]} [description]
 */
function countQuestionOpts(_fill)
{
	if(_fill)
	{
		_fill = 0;
		$.each($('.input-group.sortable .element .input[type="text"]'), function(key, value)
		{
			if($(this).val())
			{
				_fill++;
			}
		});

		return _fill;
	}
	return $('.input-group.sortable .element').length;
}


/**
 * search in tree;
 * @param  {[type]} _page [description]
 * @return {[type]}       [description]
 */
function treeSearch(_search, _notLoad)
{
	if(_notLoad && $('#tree-search').attr('data-loaded'))
	{
		return false;
	}
	$('#tree-search').attr('data-loaded', true);

	var path    = location.pathname;
	if(!_search)
	{
		_search = $('#tree-search').val();
	}
	path        = path.substr(0, path.indexOf('/add')) + '/add/tree';
	if(_search)
	{
		path = path+ '/search='+ _search;
	}

	Navigate(
	{
		data: false,
		url: path,
		nostate: true,
	});
}


/**
 * [completeProfileFill description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function completeProfileFill(_this)
{
	// create a clone form opts
	if(!window.TEMP)
	{
		window.TEMP = $('.input-group.sortable').clone();
	}
	// get options
	var dropValue      = $('#complete-profile-dropdown');
	var dropValues     = dropValue.find('option:checked').attr('data-value');
	if(dropValue.val())
	{
		if(dropValues)
		{
			var dropValueArray = dropValues.split(',');
			$('.input-group.sortable').addClass('editing');
			for (var i = 0; i < dropValueArray.length; i++)
			{
				addNewOpt(dropValue.val(), dropValueArray[i]);
				$('.input-group.sortable li[data-profile!="'+ dropValue.val() +'"]').remove();
			}
			setSortable(true);
		}
		detectPercentage();
	}
	else
	{
		completeProfileRevert();
	}
}


/**
 * revert before check complete profile exactly
 * @return {[type]} [description]
 */
function completeProfileRevert()
{
	if($('.input-group.sortable').hasClass('editing'))
	{
		$('.input-group.sortable').removeClass('editing');
		if(window.TEMP)
		{
			$('.input-group.sortable').replaceWith(window.TEMP);
			window.TEMP = null;
			setSortable();
		}
	}
	detectPercentage();
}


/**
 * detect percentage of current state
 * @return {[type]} [description]
 */
function detectPercentage(_submit)
{
	var percentage = 0;
	// if press submit then plus 10 percent
	// based on each type of page
	switch ($('.page-progress').attr('data-current'))
	{
		case 'add':
		case 'add_tree':
			if($('#title').val())
			{
				percentage += 15;
			}
			var optCount = countQuestionOpts()-1;
			optCount     = optCount<=2? 2: optCount;
			var optPercent = countQuestionOpts(true) * (30/optCount);
			if(optPercent > 30)
			{
				optPercent = 30;
			}
			percentage += optPercent;
			if(_submit)
			{
				percentage += 10;
			}
			break;

		case 'filter':
			percentage = 50;
			if(_submit)
			{
				percentage += 40;
			}
			break;

		case 'publish':
			percentage = 100;
			break;
	}
	// call draw func
	drawPercentage(percentage, '%');
}


/**
 * draw percentage of progress bar
 * @return {[type]} [description]
 */
function drawPercentage(_percent, _axis)
{
	if($('.page-progress').attr('fix'))
	{
		return;
	}
	if(_percent < 0 || _percent > 100)
	{
		return false;
	}
	var currentStep = $('.page-progress').attr('data-current');

	if(!$('.page-progress b').length)
	{
		$('.page-progress').append('<b></b>');
	}
	$('.page-progress b').width(_percent+_axis);
	// check chekcbox of this step
	if(currentStep == 'add')
	{
		if(_percent >= 50)
		{
			$('.page-progress [name="step-add"]').prop('checked', true);
		}
		else
		{
			$('.page-progress [name="step-add"]').prop('checked', false);
		}
	}
	else if(currentStep == 'filter')
	{
		if(_percent >= 90)
		{
			$('.page-progress [name="step-filter"]').prop('checked', true);
		}
		else
		{
			$('.page-progress [name="step-filter"]').prop('checked', false);
		}
	}
	else if(currentStep == 'publish')
	{
		// on some condition check checkbox after ending
	}
}


/**
 * simulate tree navigation and do not change url on this condition
 * @return {[type]} [description]
 */
function simulateTreeNavigation()
{
	$("nav.pagination a").click(function()
	{
		Navigate(
		{
			data: false,
			url: $(this).attr('href'),
			nostate: true,
		});
		return false;
	});
}


// ================================================================== @/add
// route(/\@\/add/, function()
route(/\@\/add(|\/[^\/]*)$/, function()
{
	// run textarea resizable
	// declare functions
	resizableTextarea.call(this);
	setSortable();
	detectPercentage();

	// run on input change and add new opt for this question
	$(this).on('input', '.input-group.sortable .element .input[type="text"]', function(event)
	{
		checkAddOpt();
	});

	// --------------------------------------------------------------------------------- Delete Elements
	// show and hide delete btn on special condition
	$(this).on('mouseenter', '.input-group.sortable .element', function()
	{
		// showQuestionOptsDel($(this).children('.delete'));
	}).on('focus', '.input-group.sortable .element input[type="text"]', function()
	{
		showQuestionOptsDel($(this).parent().children('.delete'));
	});

	// on keyup and press shift+del remove current record
	$(this).on('keyup', '.input-group.sortable .element input', function(e)
	{
		if(countQuestionOpts() > 2)
		{
			if(e.shiftKey && e.keyCode === 46)
			{
				$(this).parent().children('.delete').click();
			}
		}
		detectPercentage();
	});

	$(this).on('change', '#title', function()
	{
		detectPercentage();
	});

	// on press delete on each opt
	$(this).on('click', '.input-group.sortable .element .delete', function()
	{
		deleteQuestionOpts(this);
	}).on('keyup', '.input-group.sortable .element .delete', function(e)
	{
		if((e.shiftKey && e.keyCode === 46) || e.keyCode === 13)
		{
			$(this).parent().children('.delete').click();
		}
	});

	// --------------------------------------------------------------------------------- Tree
	// on open tree load content to it
	$(window).on( "response:open", function(_obj, _name, _value)
	{
		// if open tree then fill with last qustions
		if(_name == 'tree' && _value == 'open')
		{
			treeSearch.call(null, null, true);
		}
	});
	$(this).on('input', '#tree-search', function(event)
	{
		var tree_search_timeout = $(this).data('tree-search-timeout');
		if(tree_search_timeout)
		{
			clearTimeout(tree_search_timeout);
		}
		var timeout = setTimeout(treeSearch.bind(this), 200);
		$(this).data('tree-search-timeout', timeout);
	});

	// if user click on title of each question
	$(this).on('change', '.tree-result-list > li > [name="parent_tree_id"]', function(event)
	{
		var selectedItem = $(this).parents('li').children('.options');
		if(selectedItem.is(':visible'))
		{
			// if want to close, close all tags
			$('.tree-result-list > li .options').slideUp();
		}
		else
		{
			$('.tree-result-list > li .options').slideUp();
			selectedItem.slideDown();
			// $('[name="parent_tree_id"]').val($(this).parent('li').attr('data-id'));
			$('.tree-result-list > li.active').removeClass('active');
			$(this).parents('li').addClass('active');
		}
	});
	// if user change selection of each item
	$(this).on('change', '.tree-result-list > li > .options .checkbox', function(event)
	{
		// get list of checked item and create text from them
		var selectedOpts = $(this).parents('.options').find('input:checkbox:checked').map(function(){ return $(this).val();});
		$('[name="parent_tree_opt"]').val(selectedOpts.get());
	});

	// if($('#tree').is(':checked'))
	// {
	// 	// console.log('checkd default...');
	// 	// $('#tree-search').val($('[name="parent_tree_id"]').val());
	// 	// treeSearch();
	// }

	// --------------------------------------------------------------------------------- Complete profile
	// if remove complete profile checkbox, return to old status and rerun sortable
	$(this).on('change', '#complete-profile', function(event)
	{
		if (!this.checked)
		{
			// revert
			completeProfileRevert();
			// change dropdown to default value
			$('#complete-profile-dropdown').val('');
		}
	});

	// if any item of complete profile is selected, then fill item with profile values
	$(this).on('change', '#complete-profile-dropdown', function()
	{
		completeProfileFill();
	});

	// show profile detail with tab
	$(this).on('focus', '.profile-detail a', function()
	{
		$(this).parents('.profile').addClass('open');
	}).on('blur', '.profile-detail a', function()
	{
		$(this).parents('.profile').removeClass('open');
	});


	$(this).on('click','button', function()
	{
		detectPercentage(true);
		$('#submit-form').attr("value", $(this).attr("send-name"));
		$('#submit-form').attr("name", $(this).attr("send-name"));
	});
}).once(function()
{
	simulateTreeNavigation();
});


// ************************************************************************************************************ Filter
/**
 * [calcFilterPrice description]
 * @return {[type]} [description]
 */
function calcFilterPrice()
{
	var totalEl      = $('.pay-info .price .value');
	var basePrice    = parseInt(totalEl.attr('data-basePrice'));
	// var totalPerson  = parseInt($('[data-range-bind="rangepersons"]').val());
	// var totalPerson  = $('#rangepersons').rangeSlider('to');
	var totalPerson  = $('#rangepersons').data('range-slider');
	if(totalPerson)
	{
		totalPerson = totalPerson.to;
	}
	else
	{
		totalPerson = 0;
	}
	// var totalPerson  = parseInt($('#rangepersons').val());

	var totalPercent = 0;
	var totalPrice   = 0;
	$('.badge.active[data-ratio]').each(function(index, el)
	{
		var currentRatio = parseInt($(el).attr('data-ratio'));
		totalPercent     += currentRatio;
	});
	// change percent to ratio
	totalPercent = totalPercent/100;
	totalPrice   = totalPerson + (totalPerson * totalPercent);
	totalPrice   = totalPrice * basePrice;

	// set value to show to enduser
	totalEl.text(totalPrice.toLocaleString());
}

// ================================================================== @/add/7pr/filter
// route(/\@\/add(|\/[^\/]*)$/, function()
route(/\@\/add\/.+\/filter$/, function()
{
	// if any item of complete profile is selected, then fill item with profile values
	$(this).on('range-slider::change', '#rangepersons', function(_e, _min, _max)
	{
		calcFilterPrice.call(this);
	});

	// on open tree load content to it
	$(window).on( "response:open", function(_obj, _name, _value)
	{
		// console.log($(_obj).attr('data-group'));
		// console.log($(_obj).attr('data-response-group'));
		// console.log(_obj);
		// console.log(_name);
		// console.log(_value);
		calcFilterPrice.call(this);
	});
	// run rangeslider
}).once(function()
{
	console.log('once on filter....');
	calcFilterPrice.call(this);
	$(this).on('click','button', function()
	{
		detectPercentage(true);
	});
	detectPercentage();
});

// ************************************************************************************************************ Publish
function runAutoComplete()
{
	$('.dropdown').autoComplete(
	{
		minChars: 0,
		// source: function(term, suggest)
		// {
		// 	term = term.toLowerCase();
		// 	var choices = ['ActionScript', 'AppleScript', 'Asp', 'PHP', 'CSS', 'JS'];
		// 	var matches = [];
		// 	for (i=0; i<choices.length; i++)
		// 		if (~choices[i].toLowerCase().indexOf(term)) matches.push(choices[i]);
		// 	suggest(matches);
		// },
		source: function(term, response)
		{
			try { xhr.abort(); } catch(e){}
			xhr = $.getJSON('/tag/', { q: term }, function(data){ response(data); });
			console.log(xhr);
		}
	});
	console.log('run...');
	$('.dropdown').on('keydown', function(e)
	{
		if(e.keyCode == 13)
		{
			saveAutoComplete();
			return false;
		}
	});
}

function saveAutoComplete()
{
	console.log('save');
}

// ================================================================== @/add/7pr/publish
// route(/\@\/add\/[\w.]+\/publish/, function()
route(/\@\/add\/.+\/publish$/, function()
{

}).once(function()
{
	console.log('once on publish....');
	runAutoComplete();
	detectPercentage();
});


// Profile
route(/\@\/profile/, function() {
	var initial  = $('input[name="initial"]');
	var isNormal = false;

	// dblclick
	$(this).on('dblclick', '.element.has-data', function()
	{
		// if double clicked input has not class similar-tag
		if (!$(this).children('.input').hasClass('similar-tag')) {
			isNormal = true;
			initial.val( $(this).children('.input').val() );
		}
		$(this).removeClass('has-data').append(btns).children('.input').removeAttr('disabled').focus();
	});

	// click
	$(this).on('click', '.element.no-data', function()
	{
		// if clicked input has not class similar-tag
		if (!$(this).children('.input').hasClass('similar-tag')) {
			isNormal = true;
			initial.val("");
		}
		$(this).removeClass('no-data').append(btns).children('.input').removeAttr('disabled').focus();
	});

	 //  $(this).on('focus', '.element .input', function(event) {
	 //  	$(this).unbind('blur.sarshomarblur');
	 //  	$(this).bind('blur.sarshomarblur', function(e){
	 //  		$(this).unbind('blur.sarshomarblur');
	 //  		var element = $(this).parents('.element');
			// var val     = $(this).parents('.element').children('.input').val();
			// if ( isNormal )
			// {
			// 	if ( initial.val() )
			// 	{
			// 		element.addClass('has-data');
			// 	}
			// 	else
			// 	{
			// 		element.addClass('no-data');
			// 	}
			// 	element.children('.input').attr('disabled', '');
			// 	element.children('.btn').remove();
			// 	element.children('.input').val( initial.val() );
			// }
	 //  	});
	 //  });

	$(this).on('click', '.btn.save button', function(event) {
		var element = $(this).parents('.element');
		var val     = $(this).parents('.element').children('.input').val();
		var name    = $(this).parents('.element').children('.input').attr("name");
		$(this).ajaxify({
			ajax: {
				data: {
					'name': name,
					'value': val
				},
				abort: true,
				success: function(e, data, x) {
					if ( val && isNormal )
					{
						element.addClass('has-data');
						element.children('.input').attr('disabled', 'disabled');
						element.children('.btn').remove();
					}
					else if ( (!val) && isNormal)
					{
						element.addClass('no-data');
						element.children('.input').attr('disabled', 'disabled');
						element.children('.btn').remove();
					}
				},
				method: 'post'
			}
		});
	});

	$(this).on('click', '.btn.cancel button', function(event) {
		var element = $(this).parents('.element');
		var val     = $(this).parents('.element').children('.input').val();
		if ( initial.val() )
		{
			element.addClass('has-data');
		}
		else
		{
			element.addClass('no-data');
		}
		element.children('.input').attr('disabled', '');
		element.children('.btn').remove();
		element.children('.input').val( initial.val() );
	});
});


// pollsearch | Knowledge
// route(/^\/?(fa\/)?\$(.*)$/, function ()
route('*', function ()
{
	var change = 0;

	$(document).on('keyup', '.pollsearch', function(e)
	{
		var val = $(this).val();
		change  += 1;
		setTimeout(function()
		{

			if(change <= 1)
			{

				url = window.location.pathname;

				test = /\/search\=(.*)\/?/.test(url);

				if(test)
				{
					url = url.replace(/\/search\=[^\/]*\/?/, '/search=' + val + '/');
				}
				else
				{
					url = url + '/search=' + val + '/';
				}

				Navigate({ url: url });
			}
			change -= 1;
		}, 250);
	});

	$('.pollsearch').focus().val($('.pollsearch').val());
});


// Me | Profile
route('*', function ()
{
	$.each($('input.autocomplete'),function()
	{
		$(this).keyup(function(e)
		{
			name = $(this).attr('name');
			val  = $(this).val();

			$(this).ajaxify(
			{
				ajax:
				{
					method: 'post',
					url : '/',
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
							console.log(data[a]['term_title']);
							console.log(data[a]['term_url']);
							console.log(data[a]['term_count']);
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
		$('#' + split).val($('#'+ split).val().replace(span.text() + ',', ''));
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
		$.each($('#'+ split).val().split(','), function (t, item)
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
			$('#' + split).val($('#' + split).val() + newTag + ',');
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