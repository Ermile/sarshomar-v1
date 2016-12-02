var TEMP = null;
/**
 * check input for first time
 */
function checkInputChange()
{
  $('input, select', this).change(function()
  {
    checkInput(this, false);
  });

  // // run for the first time
  // $('input[type=checkbox]').each(function ()
  // {
  //   checkInput(this, true);
  // });
}


/**
 * check inputs and change status of them if needed
 * V2
 * @param  {[type]} _this      [description]
 * @param  {[type]} _firstTime [description]
 * @return {[type]}            [description]
 */
function checkInput(_this, _firstTime)
{
  var elID    = $(_this).attr('id');
  var elName  = $(_this).attr('name');
  var elType  = $(_this).attr('type');
  var elResult;
  var elValue;
  switch (elType)
  {
  	case 'checkbox':
		elValue = $(_this).is(":checked");
  		break;

  	case 'radio':
		elValue = $(_this).val();
		elID    = elName;
  		break;

  	case 'text':
  	default:
		elValue = $(_this).val();
  		break;
  }

  var childrens = $('[data-response*='+ elID +']');
  childrens.each(function()
  {
	var effect   = $(this).attr('data-response-effect');
	var timing   = $(this).attr('data-response-timing');
	var where    = $(this).attr('data-response-where');
	var whereNot = $(this).attr('data-response-where-not');
	var toggle   = $(this).attr('data-response-toggle');
	var disable  = $(this).attr('data-response-disable');
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
		    	// if condition is true
		    	$(this)[effect['open']](timing);
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
		    	// if condition is false
		    	$(this)[effect['close']](timing);
		    }
		    elResult = 'close';
	    }
    }
  });

  $(window).trigger('response:open', [elID, elResult]);
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


/**
 * add new record of answer
 */
function checkAddOpt()
{
	var numberOfEmptyInputs = 0;
	var emptyRowNumber;

	// check if current element has not value and we have no empty inputs
	$.each($('#tab-multiple_choice .input-group .element .input[type="text"]'), function(key, value)
	{
		if ( !$(this).val() && numberOfEmptyInputs === 0 )
		{
			numberOfEmptyInputs++;
			emptyRowNumber = key;
		}
	});

	// if we had no empty inputs and we needed one do this
	if (numberOfEmptyInputs === 0 && !$('#tab-multiple_choice .input-group').hasClass('editing'))
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
	var template = $('#tab-multiple_choice>.input-group>li').eq(0).clone();
	var num = $('#tab-multiple_choice>.input-group>li').length + 1;
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
	template.find('.element .score input').attr('name', 'score' + num);
	template.find('.element .score input').attr('id', 'score' + num);
	template.find('.element .score input').val('');
	template.attr('data-row', num);

	$('#tab-multiple_choice>.input-group').append(template);
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
	$('.input-group .element .delete').fadeOut(100);
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
		});
	}
}

// Range Slider
!function(a){a.fn.ermile_slider=function(){a(this).each(function(){var d=a(this).attr("data-infinity"),e=a(this).attr("data-unit"),f=a(this).attr("data-max"),g=a(this).attr("data-min");if(e||(e=100,h=100),!f&&e&&(f=e),f)var h=f;var i=0;if(g)var i=g;var j=a("<div class='dynamic-range'></div>"),k=c.call(this,"min",e,h,i),l=c.call(this,"max",e,h,i);"max"==d?l.appendTo(j):"min"==d?k.appendTo(j):(k.appendTo(j),l.appendTo(j)),j.appendTo(this),_self=this,b.call(this,i,e,d)})};var b=function(b,c,d){var e=a(this).find(".dynamic-range"),f=a(this).width(),g=f/c,h=a(this).attr("data-type"),i=b*g;console.log(g),"horizontal"==h&&(e.css("left",i),e.css("width",g)),"horizontal"!=h&&(f=a(this).height(),e.css("bottom",i),e.css("height",g)),"min"==d?("horizontal"==h&&(e.css("width",i),e.css("left",f-i)),"horizontal"!=h&&(e.css("bottom",f),e.css("height",i))):"max"==d&&("horizontal"==h&&(e.css("width",i),e.css("left",0)),"horizontal"!=h&&(e.css("bottom",0),e.css("height",i)))},c=function(b,c,e,f){var g=a(this).attr("data-type");if("vertical"!=g)var g="horizontal";var h=a("<span class='"+b+"' tabindex='0'></span>");return h.bind("mousedown.range-slider tap",function(h){a(document).unbind("mousemove.range-slider"),a(document).unbind("mouseup.range-slider");var i=this,j=a(this).parents(".dynamic-range").height();original_beginning=a(this).parents(".dynamic-range").css("bottom");var k=-h.pageY;if("vertical"!=g){var k=h.pageX;j=a(this).parents(".dynamic-range").width(),original_beginning=a(this).parents(".dynamic-range").css("left")}a(document).bind("mousemove.range-slider",function(h){var l=a(i).parents(".dynamic-range").height()-j,m=-h.pageY;if("vertical"!=g){var m=h.pageX;l=a(i).parents(".dynamic-range").width()-j}var n=m-k-l;d.call(i,n,g,b,j,original_beginning,c,e,f)}).bind("mouseup.range-slider",function(){a(document).unbind("mousemove.range-slider")}).bind("tap",function(){a("#x").text(h.pageX)})}).bind("keydown.range-slider",function(h){var i=a(this).parents(".dynamic-range").height();original_beginning=a(this).parents(".dynamic-range").css("bottom");-h.pageY;if("vertical"!=g){h.pageX;i=a(this).parents(".dynamic-range").width(),original_beginning=a(this).parents(".dynamic-range").css("left")}var k=0,l=40,m=38,n=a(this).parents(".dynamic-range"),o=n.parent().height();"vertical"!=g&&(o=n.parent().width());var p=o/c;if(my_range=p,p<1&&(my_range=1),"vertical"!=g&&(l=37,m=39),h.keyCode==m?k=my_range:h.keyCode==l&&(k=-1*my_range),k)return h.shiftKey&&(k*=10),d.call(this,k,g,b,i,original_beginning,c,e,f),!1}),h},d=function(b,c,d,e,f,g,h,i){var j=a(this).parents(".dynamic-range"),k=j.height(),l=j.parent().height();"vertical"!=c&&(k=j.width(),l=j.parent().width());var m=l/g;m>=1&&(b=Math.round(b/m),b*=m),m<1&&m>0&&(b*=m);var j=a(this).parents(".dynamic-range"),k=j.height(),n=parseInt(j.css("bottom")),o=n+k+b,l=j.parent().height();if("vertical"!=c&&(k=j.width(),n=parseInt(j.css("left")),o=n+k+b,l=j.parent().width()),"max"==d){l=m*h;var p=k+b;v=m*i,_data_infinity=a(this).parents(".range-slider").attr("data-infinity"),o<=v&&(u=v),o>=l&&(p=l-n),_data_infinity&&"max"==_data_infinity&&p<=v&&(p=v);var q="vertical"==c?"height":"width";j.css(q,p)}else if("min"==d){var q="vertical"==c?"height":"width",r="vertical"==c?"bottom":"left",s=k+b,t=s-e,p=e-t,u=t+parseInt(f),v=i*m;_data_infinity=a(this).parents(".range-slider").attr("data-infinity"),l=m*h,_data_infinity&&"min"==_data_infinity?(p<=v&&(p=v,u=l-v),p>=l&&(p=l,u=0)):u<=v&&(console.log("changed_margin < real_min"),console.log(f),console.log(e),u=v,p=parseInt(f)+e-v),p<=0&&(p=0,u=parseInt(f)+e),j.css(q,p),j.css(r,u)}}}(jQuery);

/* HTML5 Sortable jQuery Plugin -http://farhadi.ir/projects/html5sortable */
!function(t){var e,r=t();t.fn.sortable=function(a){var n=String(a);return a=t.extend({connectWith:!1},a),this.each(function(){if(/^(enable|disable|destroy)$/.test(n)){var i=t(this).children(t(this).data("items")).attr("draggable","enable"==n);return void("destroy"==n&&i.add(this).removeData("connectWith items").off("dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s"))}var s,d,i=t(this).children(a.items),o=t("<"+(/^(ul|ol)$/i.test(this.tagName)?"li":"div")+' class="sortable-placeholder">');i.find(a.handle).mousedown(function(){s=!0}).mouseup(function(){s=!1}),t(this).data("items",a.items),r=r.add(o),a.connectWith&&t(a.connectWith).add(this).data("connectWith",a.connectWith),i.attr("draggable","true").on("dragstart.h5s",function(r){if(a.handle&&!s)return!1;s=!1;var n=r.originalEvent.dataTransfer;n.effectAllowed="move",n.setData("Text","dummy"),d=(e=t(this)).addClass("sortable-dragging").index()}).on("dragend.h5s",function(){e&&(e.removeClass("sortable-dragging").show(),r.detach(),d!=e.index()&&e.parent().trigger("sortupdate",{item:e}),e=null)}).not("a[href], img").on("selectstart.h5s",function(){return this.dragDrop&&this.dragDrop(),!1}).end().add([this,o]).on("dragover.h5s dragenter.h5s drop.h5s",function(n){return i.is(e)||a.connectWith===t(e).parent().data("connectWith")?"drop"==n.type?(n.stopPropagation(),r.filter(":visible").after(e),e.trigger("dragend.h5s"),!1):(n.preventDefault(),n.originalEvent.dataTransfer.dropEffect="move",i.is(this)?(a.forcePlaceholderSize&&o.height(e.outerHeight()),e.hide(),t(this)[o.index()<t(this).index()?"after":"before"](o),r.not(o).detach()):r.is(this)||t(this).children(a.items).length||(r.detach(),t(this).append(o)),!1):!0})})}}(jQuery);

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
		$.each($('#tab-multiple_choice .input-group .element .input[type="text"]'), function(key, value)
		{
			if($(this).val())
			{
				_fill++;
			}
		});

		return _fill;
	}
	return $('#tab-multiple_choice .input-group .element').length;
}


/**
 * search in tree;
 * @param  {[type]} _page [description]
 * @return {[type]}       [description]
 */
function treeSearch(_search, _page)
{
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
		window.TEMP = $('#tab-multiple_choice>.input-group.sortable').clone();
	}
	// get options
	var dropValue      = $('#complete-profile-dropdown');
	var dropValues     = dropValue.find('option:checked').attr('data-value');
	if(dropValue.val())
	{
		if(dropValues)
		{
			var dropValueArray = dropValues.split(',');
			$('.input-group').addClass('editing');
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
	if($('.input-group').hasClass('editing'))
	{
		$('.input-group').removeClass('editing');
		if(window.TEMP)
		{
			$('.input-group').replaceWith(window.TEMP);
			window.TEMP = null;
			setSortable();
		}
	}
	detectPercentage();
}



function detectPercentage()
{
	var percentage = 0;
	console.log($('body').attr('id'));
	switch ($('body').attr('id'))
	{
		case 'add':
		case 'add_tree':
			if($('#title').val())
			{
				percentage += 20;
			}
			var optPercent = countQuestionOpts(true) * 15;
			if(optPercent > 30)
			{
				optPercent = 30;
			}
			percentage += optPercent;
			break;

		case 'filter':
			percentage = 70;
			break;

		case 'publish':
			percentage = 90;
			break;
	}
	// call draw func
	drawPercentage(percentage +'%');
}


/**
 * draw percentage of progress bar
 * @return {[type]} [description]
 */
function drawPercentage(_percent)
{
	if(!$('.page-progress b').length)
	{
		$('.page-progress').append('<b></b>');
	}

	$('.page-progress b').width(_percent);
}


// Add
route(/\@\/add/, function()
{
	// declare functions
	checkInputChange.call(this);
	resizableTextarea.call(this);
	setSortable();
	detectPercentage();

	$(".range-slider").ermile_slider();

	// run on input change and add new opt for this question
	$(this).on('input', '.input-group .element .input[type="text"]', function(event)
	{
		checkAddOpt();
	});

	// --------------------------------------------------------------------------------- Delete Elements
	// show and hide delete btn on special condition
	$(this).on('mouseenter', '.input-group .element', function()
	{
		// showQuestionOptsDel($(this).children('.delete'));
	}).on('focus', '.input-group .element input[type="text"]', function()
	{
		showQuestionOptsDel($(this).parent().children('.delete'));
	});

	// on keyup and press shift+del remove current record
	$(this).on('keyup', '.input-group .element input', function(e)
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
	$(this).on('click', '.input-group .element .delete', function()
	{
		deleteQuestionOpts(this);
	}).on('keyup', '.input-group .element .delete', function(e)
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
			treeSearch.call(null);
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
	$(this).on('click', '.tree-result-list > li > div', function(event)
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
			$('[name="parent_tree_id"]').val($(this).parent('li').attr('data-id'));
		}
	});
	// if user change selection of each item
	$(this).on('change', '.tree-result-list > li > .options .checkbox', function(event)
	{
		// get list of checked item and create text from them
		var selectedOpts = $(this).parents('.options').find('input:checkbox:checked').map(function(){ return $(this).val();});
		$('[name="parent_tree_opt"]').val(selectedOpts.get());
	});
	if($('#tree').is(':checked'))
	{
		console.log('checkd default...');
		$('#tree-search').val($('[name="parent_tree_id"]').val());
		// treeSearch();
	}


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



	$(this).on('click','button', function()
	{
		$('#submit-form').attr("value", $(this).attr("send-name"));
		$('#submit-form').attr("name", $(this).attr("send-name"));
	});
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