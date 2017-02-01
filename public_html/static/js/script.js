var TEMP = null;


/**
 * now can call js files easily
 * @param  {[type]} _src      [description]
 * @param  {[type]} _func     [description]
 * @param  {[type]} _delay    [description]
 * @param  {[type]} _noCache  [description]
 * @param  {[type]} _absolute [description]
 * @return {[type]}           [description]
 */
function $import(_src, _func, _delay, _noCache, _absolute)
{
	if(!_delay)
	{
		_delay = 100;
	}
	setTimeout(function()
	{
		if(!_absolute)
		{
			_src = '/static/js/' + _src;
		}
		if(_noCache)
		{
			$.getScript(_src, function()
			{
				callFunction(_func);
			});
		}
		else
		{
			// if function is exist, call it!
			if(callFunction(_func, null, true))
			{
				callFunction(_func);
			}
			// else if not exist import it for calling!
			else
			{
				$.cachedScript(_src).done(function(_script, _textStatus)
				{
					callFunction(_func, true);
				});
			}
		}
	}, _delay);
}


/**
 * cache import requests
 * @param  {[type]} url     [description]
 * @param  {[type]} options [description]
 * @return {[type]}         [description]
 */
jQuery.cachedScript = function(url, options)
{
  // Allow user to set any option except for dataType, cache, and url
  options = $.extend( options || {},
  {
    dataType: "script",
    cache: true,
    url: url
  });
  // Use $.ajax() since it is more flexible than $.getScript
  // Return the jqXHR object so we can chain callbacks
  return jQuery.ajax(options);
};


/**
 * call function if exist
 * @param  {[type]} _func [description]
 * @return {[type]}       [description]
 */
function callFunction(_func, _arg, _onlyCheckExist)
{
	isExist = false;
	// if wanna to call function and exist, call it
	if(typeof window[_func] === 'function')
	{
		isExist = true;
		if(!_onlyCheckExist)
		{
			window[_func](_arg);
		}
	}
	return isExist;
}


/**
 * [importCSS description]
 * @param  {[type]} _src [description]
 * @return {[type]}      [description]
 */
function importCSS(_src)
{
	var isFirstTime = true;
	$('head link[data-load="dynamic"]').each(function(i, _el)
	{
		if(_src == $(_el).attr('href'))
		{
			isFirstTime = false;
		}
	});
	if(isFirstTime)
	{
		var fileref = document.createElement("link");
		fileref.setAttribute("rel", "stylesheet");
		fileref.setAttribute("data-load", 'dynamic');
		fileref.setAttribute("href", _src);

		if (typeof fileref != "undefined" )
		{
			document.getElementsByTagName("head")[0].appendChild(fileref);
		}
	}
}


/**
 * allow textarea to be resizable
 * @return {[type]} [description]
 */
function resizableTextarea()
{
	$(document).on('input', 'textarea[data-resizable]', function()
	{
		var min     = 100;
		var max     = 500;
		var height  = $(this).height();
		var sHeight = this.scrollHeight;

		if (sHeight > min && sHeight < max && height < sHeight)
		{
			this.style.height = min+'px';
			this.style.height = this.scrollHeight+'px';
		}
	});
}


/**
 * [copy description]
 * @return {[type]} [description]
 */
function handleCopy()
{
	$('[data-copy]').on('click', function()
	{
		var $this = $(this);
		var targetEl = $($this.attr('data-copy'));
		if(targetEl.length)
		{
			targetEl.attr('disabled', null);
			targetEl.select();
			try
			{
				// copy to clipboard
				document.execCommand('copy');
				targetEl.blur();

				// copied animation
				$this.addClass('copied');
				setTimeout(function() { $this.removeClass('copied'); }, 200);
			}
			catch (err)
			{
				console.log('cant copy! Ctrl/Cmd+C to copy')
			}
		}
	})
}


/**
 * [isActiveChecker description]
 * @return {[type]} [description]
 */
function isActiveChecker()
{
    var myLoc = window.location.href;
    $('.isActive').removeClass('isActive');
	$('[href$="' + myLoc+ '"]').addClass("isActive");
}


/**
 * [openTopNav description]
 * @return {[type]} [description]
 */
function openTopNav()
{
	// show profile detail with tab
	$(document).on('focus', '.profile-detail a', function()
	{
		$(this).parents('.profile').addClass('open');
		// set scroll to top of page
		$('body').scrollTop(0);

	}).on('blur', '.profile-detail a', function()
	{
		$(this).parents('.profile').removeClass('open');
	});
}

/**
 * [setFav description]
 * @param {[type]} argument [description]
 */
function setFav()
{
	$(document).on('change', '[name="favorite"]', function()
	{
		_self = $(this);
		id    = _self.parents('[data-id]').attr("data-id");
		console.log(id);

		_self.ajaxify(
		{
			ajax:
			{
				data:
				{
					'type': 'favourites',
					'id': id
				},
				abort: true,
				method: 'post',
				error: function(e, data, x)
				{
					console.log(_self);
					if(data !== 'success')
					{
						_self.prop("checked", false);
						console.log(_self.prop('checked'));
					}
				},
			}
		});
	});
}


/**
 * [shortkey description]
 * @return {[type]} [description]
 */
function shortkey()
{
	$(document).keydown(function(e)
	{
		switch (e.keyCode)
		{
			// f1
			case 112:
				$import('lib/introJs.js', 'runHelp', 0);
				e.preventDefault();
				break;

			default:
				break;
		}
	});
}


function showPreview(_file, _output)
{
	// if we do not support fileReader return false!
	if (typeof (FileReader) == "undefined")
	{
		return false;
	}
	// declare variables
	var files = $(_file)[0].files;

	// Loop through the FileList and render image files as thumbnails.
	for (var i = 0, f; f = files[i]; i++)
	{
		// clear for each file
		$(_output).removeClass('otherFile');
		$(_output).html('');
		$(_output).find('.audio').html('');

		var fileType = 'other';
		var fileUrl  = '';
		console.log(f.type);

		// Only process image files.
		if(f.type.match('image.*'))
		{
			fileType = 'image';
			var fileUrl  = window.URL.createObjectURL(f);
		}
		else if(f.type == 'audio/mp3')
		{
			fileType = 'audio';

			// add element of audio
			var audioEl = '<audio controls><source src="'+ window.URL.createObjectURL(f)+ '" type="audio/mp3"></audio>';
			$(_output).parents('li').find('.audio').html(audioEl);
		}
		else if(f.type == 'video/mp4')
		{
			fileType = 'video';
			// show image of audio
			// fileUrl = '/static/images/file/video.svg';
			// // add element of audio
			// var audioEl = '<video controls><source src="'+ window.URL.createObjectURL(f)+ '" type="video/mp4"></audio>';
			// $(_output).parents('li').find('.audio').html(audioEl);
		}
		else
		{
			fileType = 'file';
			// fileUrl = '/static/images/file/file.svg';
			$(_output).addClass('otherFile');
			// continue;
		}

		// if is not set use default prev image
		if(!fileUrl)
		{
			fileUrl = '/static/images/file/' + fileType + '.svg';
		}
		var imageEl = '<img src="'+ fileUrl + '"/>';
		$(_output).html(imageEl);


		// // create new instance
		// var reader = new FileReader();
		// // Closure to capture the file information.
		// reader.onload = (function(theFile)
		// {
		// 	return function(e)
		// 	{
		// 		// if span of preview is not exist, then create element for preview
		// 		// var span = document.createElement('span');

		// 		// Render thumbnail
		// 		var createdEl = '';
		// 		createdEl = '<img src="'+ e.target.result+ '" title="'+ escape(theFile.name)+ '"/>';

		// 		$(_output).html(createdEl);
		// 		$(window).trigger('cropBox:open', _output);
		// 	};
		// })(f);

		// Read in the image file as a data URL.
		// reader.readAsDataURL(f);
	}
}


/**
 * [startCrop description]
 * @param  {[type]} _el [description]
 * @return {[type]}     [description]
 */
function startCrop(_el)
{
	$('#modal-crop').trigger('open');
	console.log(_el);

	var cropBox = $('#modal-crop .cropBox');
	var img     = $(_el).find('img').clone();
	console.log(img);


	cropBox.html(img);

	cropBox.find('img').cropper(
	{
		aspectRatio: 1,
		preview: '.img-preview',
		crop: function(e)
		{
			// Output the result data for cropping image.
			// console.log(e.x);
			// console.log(e.y);
			// console.log(e.width);
			// console.log(e.height);
			// console.log(e.rotate);b
			// console.log(e.scaleX);
			console.log(e);
		}
	});
}


/**
 * set link of language on each page
 */
function setLanguageURL()
{
	var urlPath     = window.location.pathname;
	var indexOfLang = urlPath.indexOf('/' + $('html').attr('lang'));

	if(indexOfLang === 0)
	{
		urlPath = urlPath.substr(4);
	}
	else
	{
		urlPath = urlPath.substr(1);
	}

	$('.langlist a').each(function(key, index)
	{
		var lang = $(index).attr('hreflang');
		if(lang == 'en')
		{
			lang = '';
		}
		var url = lang + '/' + urlPath;
		$(index).attr('href', url.trim('/'));
	})
}


// ================================================================== run on all part of each page
route('*', function ()
{
	// run func to fixSlideJumping
	// fixSlideJumping.call(this);
}).once(function()
{
	setLanguageURL();
	isActiveChecker();
	// load maps and chart js
	$import('lib/amcharts/amcharts.js', 'drawChart', 70);
	$import('lib/ammap/ammap.js', 'getMyMapData', 50);
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
function addNewOpt(_type, _title, _placeholder, _group)
{
	var template = $('.input-group.sortable>li:not([data-type])').eq(0).clone();
	var num      = $('.input-group.sortable>li').length + 1;
	if(_type)
	{
		var isExist = $('.input-group.sortable li[data-type='+ _type+']');
		// if this special name is exist, return false
		if(isExist.length > 0)
		{
			return false;
		}
		// set title and data for special type
		template.attr('data-type', _type);
		template.find('.element label.title').html('<b>'+ num + '</b> '+ _title).attr('data-pl', _placeholder);
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
	// if(_group)
	// {
	// 	template.attr('data-profile', _group);
	// }

	// clear all input and textareas
	template.find("input:not([type='submit']), textarea").val('');
	template.find("input[type='checkbox']").attr('checked', false);
	// remove image and audio
	template.find('.preview img').remove();
	template.find('.audio audio').remove();
	// clear tagbox
	template.find('.tagDetector').attr('data-val', '');
	template.find('.tagDetector .tagBox').html('');

	$('.input-group.sortable').append(template);
	// rearrange after add new element
	rearrangeSortable();

	template.addClass('animated fadeInDown').delay(1000).queue(function()
	{
		$(this).removeClass("animated fadeInDown").dequeue();
	});

	$(document).trigger('addNewOpt', [template]);
}


/**
 * [showQuestionOptsDel description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function showQuestionOptsDel(_this)
{
	// hide all elements
	$('.input-group.sortable li .delete').fadeOut(100);
	var currentRowValue = $(_this).closest('li').find('.element input[type="text"]').val();
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
		deleteOpt(_this);
	}
}


/**
 * [deleteOpt description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function deleteOpt(_this)
{
	var myEl = $(_this);
	// if wanna delete specefic type of li, set it
	if(typeof _this === 'string')
	{
		myEl = $('.input-group.sortable li[data-type=' + _this + ']');
	}
	if(myEl.length != 1)
	{
		return false;
	}

	myEl.closest('li').addClass('animated fadeOutSide').slideUp(200, function()
	{
		// if have data type
		if($(this).attr('data-type') && $(this).attr('data-type') === 'other')
		{
			$('#descriptive').prop('checked', false);
		}
		// set focus to next input
		$(this).closest('li').next().find('input').focus();
		// remove element
		$(this).remove();
		// rearrange question opts
		rearrangeSortable();
		// recalc percentage of progress bar
		detectPercentage();
	});
}


/**
 * rearrange number of opts in question
 * @return {[type]} [description]
 */
function rearrangeSortable()
{
	$.each($('.input-group.sortable li'), function(key, value)
	{

		var num = key + 1;
		var row = num;
		$(this).attr('data-row', num);
		// if data-type isset, use it as alternative of number
		if($(this).attr('data-type'))
		{
			row = $(this).attr('data-type');
		}
		$(this).find('.element label').attr('for', 'answer' + row);
		// if language is farsi then convert number to persian
		if($('html').attr('lang') === 'fa')
		{
			$(this).find('.element label.title b').text(num.toString().toFarsi());
		}
		else
		{
			$(this).find('.element label.title b').text(num);
		}
		// set placeholder
		var myPl = $(this).find('.element label.title').attr('data-pl');
		if(!myPl)
		{
			myPl = $(this).find('.element label.title').text();
		}
		$(this).find('.element .input').attr('placeholder', myPl);
		// set main input
		$(this).find('.element .input').attr('id', 'answer' + row);
		// set file
		$(this).find('.element .file input').attr('id', 'file' + row);
		$(this).find('.element .file label').attr('for', 'file' + row);
		// set true
		$(this).find('.element .true input').attr('id', 'true' + row);
		$(this).find('.element .true label').attr('for', 'true' + row);
		// set score
		$(this).find('.score-module .scoreVal').attr('id', 'score' + row);
		$(this).find('.score-module label').attr('for', 'score' + row);
	});


	$('#multiple-range').attr('data-max', countQuestionOpts);
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
 * [fillTree description]
 * @return {[type]} [description]
 */
function fillTree(_el)
{
	// prepare sended data
	var sendData  = {};
	sendData.list = 'opts'
	sendData.id   = $(_el).attr('data-val');
	// try to abort old request
	try { xhr.abort(); } catch(e){}
	// try to get new list from server
	var xhr = $.getJSON('', sendData, function(_data)
	{

		console.log('get from reza, json of poll detail');
		// fill it
		console.log(_data);
		var list = clearJson(_data);
		console.log(list);
	});
}


/**
 * [clearJson description]
 * @param  {[type]} _data [description]
 * @return {[type]}       [description]
 */
function clearJson(_data)
{
	var list = null;
	if(_data && _data.msg && _data.msg.list)
	{
		list = _data.msg.list;
		list = JSON.parse(list);
		if(list)
		{
			// do nothing
		}
		else
		{
			list = [];
		}
	}
	return list;
}


// /**
//  * search in tree;
//  * @param  {[type]} _page [description]
//  * @return {[type]}       [description]
//  */
// function treeSearch(_search, _notLoad)
// {
// 	if(_notLoad && $('#tree-search').attr('data-loaded'))
// 	{
// 		return false;
// 	}
// 	$('#tree-search').attr('data-loaded', true);

// 	var path    = location.pathname;
// 	if(!_search)
// 	{
// 		_search = $('#tree-search').val();
// 	}
// 	path        = path.substr(0, path.indexOf('/add')) + '/$';
// 	if(_search)
// 	{
// 		path = path+ '/search='+ _search;
// 	}
// 	path = path + '?onlySearch=true';

// 	Navigate(
// 	{
// 		data: false,
// 		url: path,
// 		nostate: true,
// 	});
// }


/**
 * [completeProfileFill description]
 * @param  {[type]} _e      [description]
 * @param  {[type]} _target [description]
 * @param  {[type]} _this   [description]
 * @return {[type]}         [description]
 */
function completeProfileFill(_e, _target, _this)
{
	// get items of selected value
	var selectedList  = $('#profile_cat_list option[value="'+ $(_this).val() + '"]');
	var selectedValue = selectedList.attr('data-value');
	var items         = selectedList.attr('data-items');

	// if selectedValue is notSet then decide about it
	if(!selectedValue)
	{
		selectedValue = $(_this).val();
		selectedValue = '';
	}
	// add data untranslated value
	$(_this).attr('data-value', selectedValue);
	// if we have item try to parse as array
	if(items)
	{
		items = JSON.parse(items);
	}
	console.log(items);
	// get name of target list
	var targetList = $('#'+ $(_target).attr('list'));
	// if we have targetList fill it with new data
	if(targetList.length == 1)
	{
		// get first item
		targetList = targetList[0];
		// empty it
		targetList.innerHTML = "";
		// if we have items, fill it
		if(items)
		{
			$(_target).addClass('filled').delay(1000).queue(function(next)
			{
				$(this).removeClass("filled");
				next();
			});

			$.each(items, function(_value, _transKey)
			{
				var option = document.createElement('option');
				option.value = _transKey;
				$(option).attr('data-value', _value);
				targetList.appendChild(option);
			});
		}
		// change placeholder in all condition
		if(!$(_target).attr('placeholder-first'))
		{
			$(_target).attr('placeholder-first', $(_target).attr('placeholder'));
		}
		if(selectedValue)
		{
			$(_target).attr('placeholder', $(_this).val());
		}
		else
		{
			$(_target).attr('placeholder', $(_target).attr('placeholder-first'));
		}
	}
	else
	{
		targetList = null;
	}
}


/**
 * [detectStep description]
 * @return {[type]} [description]
 */
function detectStep(_name)
{
	var firstTime = (!_name);
	if(!_name && window.location.hash)
	{
		// _name = 'step-' + (window.location.hash).substr(1);
		_name = (window.location.hash).substr(1);
	}
	// declare variables
	var sthis    = _name;
	var sAdd     = $('.page-progress #step-add:checkbox:checked').length;
	var sFilter  = $('.page-progress #step-filter:checkbox:checked').length;
	var sPublish = $('.page-progress #step-publish:checkbox:checked').length;

	switch(sthis)
	{
		default:
		case 'step-add':
		case 'step1':
			sthis = 'step-add';
			$('.page-progress #step-add').prop('checked', true).parent('.checkbox').addClass('active');
			$('.page-progress #step-filter').prop('checked', false).parents('.checkbox').removeClass('active');
			$('.page-progress #step-publish').prop('checked', false).parents('.checkbox').removeClass('active');
			break;

		case 'step-filter':
		case 'step2':
			$('.page-progress #step-add').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-filter').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-publish').prop('checked', false).parents('.checkbox').removeClass('active');
			break;

		case 'step-publish':
		case 'step3':
			$('.page-progress #step-add').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-filter').prop('checked', true).parents('.checkbox').addClass('active');
			$('.page-progress #step-publish').prop('checked', true).parents('.checkbox').addClass('active');
			break;
	}
	// change title of btn
	$("#next-step").text($('.page-progress .active:last').attr('data-btn'));
	changeStep(sthis, firstTime);
}


/**
 * [changeStep description]
 * @param  {[type]} _name [description]
 * @return {[type]}       [description]
 */
function changeStep(_name, _first)
{
	switch(_name)
	{
		default:
		case 'step1':
		case 'step-add':
			$('.stepAdd').slideDown();
			$('.stepFilter').slideUp();
			$('.stepPublish').slideUp();
			if(!_first)
			{
				window.location.hash = 'step1';
			}
			break;

		case 'step2':
		case 'step-filter':
			$('.stepAdd').slideUp();
			$('.stepFilter').slideDown();
			$('.stepPublish').slideUp();
			window.location.hash = 'step2';
			break;

		case 'step3':
		case 'step-publish':
			$('.stepAdd').slideUp();
			$('.stepFilter').slideUp();
			$('.stepPublish').slideDown();
			window.location.hash = 'step3';
			break;
	}

	$('.page-progress').attr('data-current', _name);
	// window.location.hash = _name;
	// _name = _name.substr(5);
	detectPercentage();
}


/**
 * [checkNextStep description]
 * @return {[type]} [description]
 */
function checkNextStep()
{
	$(document).on('click', "#next-step", function()
	{
		switch ($('.page-progress .active:last').find('input').attr('id'))
		{
			case 'step-add':
				detectStep('step-filter');
				break;

			case 'step-filter':
				detectStep('step-publish');
				break;

			case 'step-publish':
				console.log('what now?');
				break;

		}
	})
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
		case 'step-add':
		case 'step1':
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
		case 'step-filter':
		case 'step2':
			percentage = 50;
			if(_submit)
			{
				percentage += 40;
			}
			break;

		case 'publish':
		case 'step-publish':
		case 'step3':
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
	var totalPerson  = parseInt($('input[name="rangepersons-max"]').val());
	if(totalPerson)
	{
		// totalPerson = totalPerson.to;
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
	totalPrice   = Math.round(totalPrice);

	// set value to show to enduser
	totalEl.text(totalPrice.toLocaleString());
}


/**
 * [prepareAdd description]
 * @return {[type]} [description]
 */
function prepareAdd()
{

	var saveTimeout = $('#question-add').attr('data-saving-timeout');
	if(saveTimeout)
	{
		clearTimeout(saveTimeout);
	}
	var savingTimeout = setTimeout(function()
	{
		sendQuestionData();
		$('#question-add').attr('data-saving-timeout', null);

	}, 2000);
	$('#question-add').attr('data-saving-timeout', savingTimeout);
}


/**
 * [sendQuestionData description]
 * @return {[type]} [description]
 */
function sendQuestionData()
{
	var myPoll  = {};
	myPoll      = prepareQuestionData();
	myPoll.from = prepareQuestionFilter();
	console.log(myPoll);
	myPoll      = JSON.stringify(myPoll);
	$('#question-add').addClass('syncing');

	$('#question-add').ajaxify(
	{
		ajax:
		{
			data: {data : myPoll},
			// data: myPoll,
			// dataType: "json",
			// contentType:"application/json; charset=utf-8",
			abort: true,
			method: 'post',
			success: function(e, data, x)
			{
				if(e.result && e.result.id)
				{
					var id = e.result.id;
					var myurl = window.location.pathname;
					if(myurl.indexOf('add/'+ id) >= 0)
					{
						// edit
					}
					else
					{
						var myurl = window.location.pathname + '/' + id + window.location.hash;
						// add new and redirect url
						$('#question-add').attr('data-id', id);
						$('#short_url').val(id);
						Navigate(
						{
							url: myurl,
							fake: true,
						});
					}
				}
				$('#question-add').removeClass('syncing');
			},
			error: function(e, data, x)
			{
				$('#question-add').addClass('failed');
				console.log('error!');
			}
		},
		lockForm: false,
	});

	return myPoll;
}



/**
 * [prepareQuestionData description]
 * @return {[type]} [description]
 */
function prepareQuestionData()
{
	var myQuestion     = {};
	myQuestion.title   = $('#title').val();
	// myQuestion.type    = 'poll';
	myQuestion.answers = {};

	// for each input added by user
	$('.input-group.sortable li').each(function(_e)
	{
		var $this   = $(this);
		var row     = $this.attr('data-row');
		// dont get id from html
		// row         = _e+1;
		var thisOpt = {};
		// title
		thisOpt.title   = $('#answer'+row).val();
		// complete profile

		if($('#complete-profile').is(":checked"))
		{
			thisOpt.profile = $('#profileTag'+row).attr('data-val');
			if(thisOpt.profile)
			{
				thisOpt.profile = JSON.parse(thisOpt.profile);
			}
			else
			{
				thisOpt.profile = {};
			}
		}
		// type of opt
		thisOpt.type    = $this.attr('data-type');
		if(!thisOpt.type)
		{
			thisOpt.type = 'select';
		}
		// switch for each type of data
		switch(thisOpt.type)
		{
			case 'other':
				thisOpt.type = 'descriptive';
			case 'select':
				thisOpt.select = {};
				// if checked true, save it
				if($('#true_answer').is(":checked"))
				{
					thisOpt.select.is_true = $('#true'+row).is(":checked");
				}
				// if checked score, save it
				if($('#score').is(":checked"))
				{
					thisOpt.select.score = {};
					thisOpt.select.score.value = parseInt($('#score'+row).val());
					if($('#score-advance').is(":checked"))
					{
						thisOpt.select.score.group = $('#score'+row).parent().find('.scoreCat').val();
					}
				}
				break;
		}
		// add to total array of this question
		myQuestion.answers[_e] = thisOpt;
	});

	if($('#meta_branding').is(":checked"))
	{
		myQuestion.brand       = {};
		myQuestion.brand.title = $('#answer_brand').val();
		myQuestion.brand.url   = $('#answer_brand_url').val();
	}
	// summary
	myQuestion.summary     = $('#summary').val();
	// description
	myQuestion.description = $('#description').val();
	// languages
	myQuestion.language    = $('input[name="ui-language"]:checked').val();
	// options
	myQuestion.options     = {};

	// get choice_mode
	var choice_mode = $('input[name="meta_choicemode"]:checked').val();

	switch(choice_mode)
	{
		case 'multi':
			myQuestion.options.multi = {};
			break;

		case 'ordering':
			myQuestion.options.ordering = true;
			break;

		case 'one':
		default:
			// no thing!
			break;
	}

	if(typeof myQuestion.options.multi === "object")
	{
		myQuestion.options.multi.min = parseInt($("input[name=multiple-range-min]").val());
		myQuestion.options.multi.max = parseInt($("input[name=multiple-range-max]").val());
	}

	// save randomSort for multiple selection
	myQuestion.options.random_sort = $('#random_sort').is(":checked");
	// data of publish page
	// articles
	myQuestion.article = $('#article').attr('data-val');
	if(myQuestion.article)
	{
		myQuestion.article = JSON.parse(myQuestion.article);
	}
	// tags
	myQuestion.tags = $('#tags').attr('data-val');
	if(myQuestion.tags)
	{
		myQuestion.tags = JSON.parse(myQuestion.tags);
	}
	// myQuestion.inHomepage = $('#inHomepage').is(":checked");

	return myQuestion;
}


/**
 * [prepareQuestionFilter description]
 * @return {[type]} [description]
 */
function prepareQuestionFilter()
{
	var myFilters = {};
	// get total person
	var totalPerson  = $('input[name="rangepersons-max"]').val();
	if(totalPerson)
	{
		// totalPerson = totalPerson.to;
	}
	else
	{
		totalPerson = 0;
	}
	myFilters.count = totalPerson;
	// myFilters.gender = $('input[name="meta_choicemode"]:checked').val();

	$('.element[data-respnse-group]').each(function(_e)
	{
		var group      = $(this).attr('data-respnse-group');
		var activeVals = $(this).find('input[type="checkbox"]:checked');
		if(activeVals.length)
		{
			myFilters[group] = [];
			activeVals.each(function()
			{
				myFilters[group].push($(this).val());
			});
		}
	});

	return myFilters;
}


/**
 * [fillCategory description]
 * @return {[type]} [description]
 */
function fillCategory(_el)
{
	console.log('fill');
	console.log(_el);
}


// ================================================================== @/add
// route(/\@\/add/, function()
route(/\@\/add(|\/[^\/]*)$/, function()
{
	// run on input change and add new opt for this question
	$(this).on('input', '.input-group.sortable li .input[type="text"]', function(event)
	{
		checkAddOpt();
	});

	// --------------------------------------------------------------------------------- Delete Elements
	// show and hide delete btn on special condition
	$(this).on('mouseenter', '.input-group.sortable li', function()
	{
		showQuestionOptsDel($(this).find('.delete'));
	}).on('focus', '.input-group.sortable li input[type="text"]', function()
	{
		showQuestionOptsDel($(this).closest('li').find('.delete'));
	});

	// on input and press shift+del remove current record
	$(this).on('input', '.input-group.sortable li input', function(e)
	{
		if(countQuestionOpts() > 2)
		{
			if(e.shiftKey && e.keyCode === 46)
			{
				$(this).closest('li').find('.delete').click();
			}
		}
		detectPercentage();
	});

	$(this).on('input', '#title', function()
	{
		detectPercentage();
	});


	$(this).on('change', '#descriptive', function()
	{
		if(this.checked)
		{
			addNewOpt('other', $(this).attr('data-title'), $(this).attr('data-subtitle'));
		}
		else
		{
			deleteOpt('other');
		}
	});

	// on press delete on each opt
	$(this).on('click', '.input-group.sortable li .delete', function()
	{
		deleteQuestionOpts(this);
	}).on('input', '.input-group.sortable li .delete', function(e)
	{
		if((e.shiftKey && e.keyCode === 46) || e.keyCode === 13)
		{
			$(this).closest('li').find('.delete').click();
		}
	});

	// --------------------------------------------------------------------------------- Tree
	// $(this).on('input', '#tree-search', function(event)
	// {
	// 	var tree_search_timeout = $(this).data('tree-search-timeout');
	// 	if(tree_search_timeout)
	// 	{
	// 		clearTimeout(tree_search_timeout);
	// 	}
	// 	var timeout = setTimeout(treeSearch.bind(this), 200);
	// 	$(this).data('tree-search-timeout', timeout);
	// });

	// // if user change selection of each item
	// $(this).on('change', '.tree-result-list > li > .options .checkbox', function(event)
	// {
	// 	// get list of checked item and create text from them
	// 	var selectedOpts = $(this).parents('.options').find('input:checkbox:checked').map(function(){ return $(this).val();});
	// 	$('[name="parent_tree_opt"]').val(selectedOpts.get());
	// });

	// if($('#tree').is(':checked'))
	// {
	// 	// console.log('checkd default...');
	// 	// $('#tree-search').val($('[name="parent_tree_id"]').val());
	// 	// treeSearch();
	// }


	$(this).on('click','button', function()
	{
		detectPercentage(true);
		$('#submit-form').attr("value", $(this).attr("send-name"));
		$('#submit-form').attr("name", $(this).attr("send-name"));
	});


	$(this).bind('range-slider::change', '#rangepersons', function(_e, _min, _max)
	{
		calcFilterPrice.call(this);
	});
}).once(function()
{
	// import needed js
	$import('lib/rangeSlider.js', 'runRangeSlider', 150);
	$import('lib/Sortable.min.js', 'setSortable', 200);
	$import('lib/awesomplete.min.js', 'fillAuto', 300);
	$import('lib/tagDetector.js', 'runTagDetector', 400);

	// simulateTreeNavigation();
	checkNextStep();
	// handle copy btn
	handleCopy();

	$('.page-progress input').on('click', function(e)
	{
		detectStep($(this).attr('name'));
		// e.stopPropagation();
		// return false;
	});
	// on init
	detectStep();


	// // on open tree load content to it
	// $(window).off("response:open");
	// $(window).on("response:open", function(_obj, _name, _value)
	// {
	// 	// if open tree then fill with last qustions
	// 	if(_name == 'tree' && _value == 'open')
	// 	{
	// 		treeSearch.call(null, null, true);
	// 	}
	// });


	// ------------------------------------------------------------------ Tree
	// if user click on title of each question
	$(document).off('change', '.tree-result-list > li > [name="parent_tree_id"]');
	$(document).on('change', '.tree-result-list > li > [name="parent_tree_id"]', function(event)
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


	// ------------------------------------------------------------------ File Preview
	//
	$(this).on('change', 'input[type="file"]', function(event)
	{
		var output = $(this).parents('.ultra').find('.preview');
		var imagePreview = showPreview(this, output);
	});
	// after complete loading, open cropbox
	$(window).off("cropBox:open");
	$(window).on("cropBox:open", function(_e, _el)
	{
		if(!_el)
		{
			return false;
		}
		$(_el).attr('data-modal', '');
		// start crop with this image
		// startCrop(_el);
	});
	// on click on preview of imagee
	$('body').on("click", ".file .preview", function(_e, _el)
	{
		startCrop(this);
	});
	// on click on preview of imagee
	$('body').on("click", "#modal-crop .btn", function(_e, _el)
	{
		// complete croping
		$('#modal-crop').trigger('close');
	});


	// ================================================================== filter
	calcFilterPrice.call(this);
	$(this).on('click','button', function()
	{
		detectPercentage(true);
	});
	detectPercentage();

	// on open tree load content to it
	$(window).off( "response:open");
	$(window).on( "response:open", function(_obj, _name, _value)
	{
		// console.log($(_obj).attr('data-group'));
		// console.log($(_obj).attr('data-response-group'));
		// console.log(_obj);
		// console.log(_name);
		// console.log(_value);
		calcFilterPrice.call(this);
	});

	// ================================================================== publish
	// runAutoComplete();

	$('#question-add').on('change', 'input, textarea', function()
	{
		prepareAdd();
	});

});


// ================================================================== $
/**
 * [searchInPolls description]
 * @return {[type]} [description]
 */
function searchInPolls()
{
	var path = location.pathname;
	_search  = $('.search-box input').val();
	path     = path.substr(0, path.indexOf('/$')+2);
	if(_search)
	{
		path = path+ '/search='+ _search;
	}


	Navigate({ url: path, ajax:{method:'get', data:{'onlySearch' : true}}});
	// Navigate({ url: path});
}


route(/\$/, function()
{

}).once(function()
{
	// --------------------------------------------------------------------------------- Search
	$(this).off('input', '.search-box input');
	$(this).on('input', '.search-box input', function(event)
	{
		var search_timeout = $(this).attr('data-search-timeout');
		if(search_timeout)
		{
			clearTimeout(search_timeout);
		}
		var timeout = setTimeout(function(){ searchInPolls(); }, 500);
		$(this).attr('data-search-timeout', timeout);
	});

});


// pollsearch | Knowledge
// route(/^\/?(fa\/)?\$(.*)$/, function ()
route('*', function ()
{
	var change = 0;

	$(document).on('input', '.pollsearch', function(e)
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


// // Me | Profile
// route('*', function ()
// {
// 	$.each($('input.autocomplete'),function()
// 	{
// 		$(this).keyup(function(e)
// 		{
// 			name = $(this).attr('name');
// 			val  = $(this).val();

// 			$(this).ajaxify(
// 			{
// 				ajax:
// 				{
// 					method: 'post',
// 					url : '/',
// 					data:
// 					{
// 						'type'  : 'autocomplete',
// 						'data'  : name,
// 						'search': val
// 					},
// 					abort: true,
// 					success: function(e, data, x)
// 					{
// 						data = e.msg.callback;
// 						for (a in data)
// 						{
// 							console.log(data[a]['term_title']);
// 							console.log(data[a]['term_url']);
// 							console.log(data[a]['term_count']);
// 						}
// 					}
// 				}
// 			});
// 		});
// 	});
// });



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


runAllScripts();
/**
 * this function run all scripts and all subfunctions
 * @return {[type]} [description]
 */
function runAllScripts()
{
	// handle all shortkeys
	shortkey();
	// set all textarea resizable
	resizableTextarea();
	// open profile on getting focus
	openTopNav();
	// allow to set fav
	setFav();


	// load needed js file
	loadFiles();
}


/**
 * [loadFiles description]
 * @return {[type]} [description]
 */
function loadFiles()
{
	$import('lib/data-response.js', 'runDataResponse', 50);
}