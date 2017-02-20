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
function $import(_src, _func, _args, _delay, _noCache, _absolute)
{
	if(_delay === true)
	{
		_delay = 100;
	}
	else if(!_delay)
	{
		_delay = 0;
	}
	// pre define args as empty object if notset
	if(!_args)
	{
		_args = {};
	}
	var myArgs  = {};
	myArgs.data = _args;

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
				callFunction(_func, myArgs);
			});
		}
		else
		{
			// if function is exist, call it!
			if(callFunction(_func, null, true))
			{
				callFunction(_func, myArgs);
			}
			// else if not exist import it for calling!
			else
			{
				$.cachedScript(_src).done(function(_script, _textStatus)
				{
					myArgs.firstTime = true;
					callFunction(_func, myArgs);
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
	if(_func && typeof window[_func] === 'function')
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
	$('.dropmenu a').on('focus',function()
	{
		console.log('selected..');
		$(this).parents('.dropmenu').addClass('open');
		// set scroll to top of page
		$('body').scrollTop(0);

	}).on('blur', function()
	{
		$(this).parents('.dropmenu').removeClass('open');
	});
}


/**
 * [setProperty description]
 * @param {[type]} argument [description]
 */
function setProperty(_prop)
{
	if(!_prop)
	{
		_prop = 'favorite';
	}
	$(document).on('change', '[name="' + _prop + '"]', function()
	{
		var _self = $(this);
		var id    = _self.attr("data-id");
		if(!id)
		{
			id = _self.parents('[data-id]').attr("data-id");
		}

		_self.ajaxify(
		{
			ajax:
			{
				data:
				{
					'setProperty': _prop,
					'status': this.checked,
					'id': id
				},
				abort: true,
				method: 'post',
				error: function(e, data, x)
				{
					console.log(data);
					if(data !== 'success')
					{
						_self.prop("checked", false);
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
		var key    = String(e.which);
		var ctrl   = e.ctrlKey  ? 'ctrl'  : '';
		var shift  = e.shiftKey ? 'shift' : '';
		var alt    = e.altKey   ? 'alt'   : '';
		var mytxt  = key + ctrl + alt + shift;
		// console.log(mytxt);

		switch (mytxt)
		{
			// shift + delete
			case '46shift':
				var focusedElement = $(e.target);
				if(focusedElement.is('.input-group.sortable > li .input'))
				{
					focusedElement.closest('li').find('.delete').click();
				}
				break;

			case '82ctrl':
				// if we have chart redraw it with new data
				if($('.chart').length)
				{
					redrawChart();
				}
				e.preventDefault();
				break;

			// ctrl + s
			case '83ctrl':
				// on /@/add by pressing ctrl+s save
				var myurl = window.location.pathname;
				if(myurl.indexOf('/@/add') >= 0)
				{
					// send data to server for saving
					requestSavingData(true);
					e.preventDefault();
				}
				break;

			// f1
			case '112':
				$import('lib/introJs/introJs.js', 'runHelp');
				e.preventDefault();
				break;

			default:
				break;
		}
	});
}


/**
 * [giveFile description]
 * @param  {[type]} _file [description]
 * @return {[type]}       [description]
 */
function giveFile(_file)
{
	// get output
	var output   = $(_file).parents('.ultra').find('.preview')[0];
	// get file object
	var fileObj  = $(_file)[0].files[0];
	var fileUrl  = '';
	// get file size in mb
	var fileSize = (fileObj.size/1024/1024);

	// reach maximum size
	if(fileSize > 1)
	{
		$this = $(_file).parent();
		$this.addClass('invalid');
		setTimeout(function()
		{
			$this.removeClass('invalid');
		}, 1000);

		console.log('max size is reached!');
		return false;
	}
	if(fileObj)
	{
		// get file url
		fileUrl = window.URL.createObjectURL(fileObj);
		// set object of file to preview box data val
		$(output).data('file', fileObj)
			.attr('data-file-type', fileObj.type)
			.attr('data-file-temp', fileUrl)
			.attr('data-file-url', fileUrl)
			.attr('data-file-local', true);

		showPreview(output);
	}
}


/**
 * [fileTypeAnalyser description]
 * @param  {[type]} _file [description]
 * @return {[type]}       [description]
 */
function fileTypeAnalyser(_file)
{
	var myFile  = {};
	if(_file)
	{
		myFile.type = _file.substr(0, _file.indexOf('/'));
		myFile.ext  = _file.substr(_file.indexOf('/') + 1);
	}

	return myFile;
}


/**
 * [showPreview description]
 * @param  {[type]} _file   [description]
 * @param  {[type]} _output [description]
 * @return {[type]}         [description]
 */
function showPreview(_output, _empty)
{
	var $output = $(_output);
	// if we do not support fileReader return false!
	if (typeof (FileReader) == "undefined")
	{
		return false;
	}
	// clear for each file
	$output.html('').removeClass('otherFile');
	$output.closest('.ultra').find('.audio').html('');

	// get attribute of this file, from input or from server
	var attrType  = $output.attr('data-file-type');
	var attrUrl   = $output.attr('data-file-url');
	var attrLocal = $output.attr('data-file-local');
	var attrObj   = $output.data('file');
	// generate some prop of file
	var fileType    = fileTypeAnalyser(attrType);
	var fileModel   = fileType.type;
	var fileExt     = fileType.ext;
	if(!fileModel)
	{
		fileModel = 'file';
	}
	if(_empty)
	{
		$output.html('');
	}
	else if(fileModel != 'image')
	{
		removeFile($output);
	}
	else
	{
		// create file preview url
		var filePrevUrl = '/static/images/file/' + fileModel + '.svg';
		// for image use real image for preview
		if(fileModel == 'image')
		{
			filePrevUrl = attrUrl;
		}
		else if(fileModel != 'audio' && fileModel != 'video')
		{
			filePrevUrl = '/static/images/file/file.svg';
		}

		var imageEl     = '<img src="'+ filePrevUrl + '"/>';
		// fill output with image
		$output.html(imageEl);
	}

	// show some work depending on file model
	switch (fileModel)
	{
		case "image":
			// open modal for edit
			$(window).trigger('cropBox:open', $output);
			break;

		case "audio":
			var mediaEl = createNewEl(fileModel, {"url":attrUrl});
			// only for mp3 show preview
			if(fileExt == 'mp3')
			{
				$output.closest('.ultra').find('.audio').html(mediaEl);
			}
			break;

		case "video":
			var audioEl = createNewEl(fileModel, {"url":attrUrl});
			// only for mp4 show preview
			if(fileExt == 'mp4')
			{

			}
			break;

		// unknown file model use file simple preview
		case "":
			fileModel = 'file';
			break;

		default:
			$output.addClass('otherFile');
			break;
	}


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


/**
 * [drawMedia description]
 * @return {[type]} [description]
 */
function drawMedia()
{
	$.each($('.input-group.sortable > li .preview[data-file-url]'), function(key, value)
	{
		showPreview(this);
	});
}


/**
 * [createNewEl description]
 * @param  {[type]} _type [description]
 * @param  {[type]} _args [description]
 * @return {[type]}       [description]
 */
function createNewEl(_type, _args)
{
	var createdEl = '';
	switch (_type)
	{
		case 'audio':
			if(_args && _args.url)
			{
				createdEl = '<audio controls><source src="' + _args.url + '" type="audio/mp3"></audio>';
			}
			break;


		case 'video':
			if(_args && _args.url)
			{
				createdEl = '<video controls><source src="' + _args.url + '" type="video/mp4"></video>';
			}
			break;
	}
	// return created el
	return createdEl;
}


/**
 * [startCrop description]
 * @param  {[type]} _el [description]
 * @return {[type]}     [description]
 */
function startCrop(_el)
{
	$('#modal-preview').trigger('open');

	var finalPreview = $('#modal-preview .finalPreview');
	var elImgPrev    = $(_el).find('img');
	var attr         = {};
	attr.type        = $(_el).attr('data-file-type');
	attr.url         = $(_el).attr('data-file-url');
	var fileType     = fileTypeAnalyser(attr.type);
	attr.model       = fileType.type;
	attr.ext         = fileType.ext;
	// transfer image to modal

	switch (attr.model)
	{
		case 'image':
			// create a clone from selected image
			var elImgNew  = elImgPrev.clone();
			// fill final preview with image
			finalPreview.html(elImgNew);
			var argSend      = {};
			argSend.targetEl = finalPreview;
			argSend.preview  = $(_el);
			$import('lib/cropper/cropper.min.js', 'runCropper', argSend);
			break;

		case 'audio':
		case 'video':
			// create multimedia el for audio and video
			var multimediaEl = createNewEl(attr.model, {"url":attr.url});
			// fill final preview with image
			finalPreview.html(multimediaEl);
			break;

		default:
			finalPreview.html('');
			break;
	}
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
		// if we are in this language
		if(lang == $('html').attr('lang'))
		{
			$(index).attr('class', 'isCurrent');
		}
		if(lang == 'en')
		{
			lang = '';
		}
		else if(lang == $('html').attr('lang'))
		{
			lang = '/' + lang;
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
	$import('lib/amcharts/amcharts.js', 'drawChart', null, 70);
	$import('lib/ammap/ammap.js', 'getMyMapData', null, 50);
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

	if(countQuestionOpts() >= 20)
	{
		console.log('maximum opt is reached!');
	}
	// elseif we had no empty inputs and we needed one do this
	else if (numberOfEmptyInputs === 0 && !$('.input-group.sortable').hasClass('editing'))
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
		var isExist = $('.input-group.sortable > li[data-type='+ _type+']');
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
function showQuestionOptsDel(_this, _delete)
{
	if(!_delete)
	{
		// hide all elements
		$('.input-group.sortable > li .delete').fadeOut(100);
	}
	var currentRowStatus = $(_this).closest('li').attr('data-empty');
	var totalRow         = countQuestionOpts();
	var emptyRow         = totalRow - countQuestionOpts(true);
	// always show delete button on input focus
	if(totalRow > 2)
	{
		if(currentRowStatus && emptyRow == 1)
		{

		}
		else
		{
			if(_delete)
			{
				deleteOpt(_this);
			}
			else
			{
				$(_this).stop().fadeIn(200);
			}
			detectPercentage();
		}
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
		myEl = $('.input-group.sortable > li[data-type=' + _this + ']');
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
	$.each($('.input-group.sortable > li'), function(key, value)
	{
		var num = key + 1;
		var row = num;
		var inputVal = $(this).find('.element .input').val();
		// remove empty null
		if(inputVal)
		{
			$(this).attr('data-empty', null);
		}
		else
		{
			$(this).attr('data-empty', true);
		}
		$(this).attr('data-row', num);
		// if data-type isset, use it as alternative of number
		if($(this).attr('data-type'))
		{
			// row = $(this).attr('data-type');
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
		// fill it
		var list = clearJson(_data);
		var tree_opts = '';
		if(list.length > 0)
		{
			tree_opts = '<ul>';
			$.each(list, function(_i)
			{
				var itemId = 'tree_opt_' + this.key;
				tree_opts += '<li>';
				tree_opts += '<span class="checkbox"><input type="checkbox" id="' + itemId + '"><label class="check-box" for="' + itemId + '"></label></span>';
				tree_opts += '<label for="' + itemId + '">'+ this.title + '</label>';
				tree_opts += '</li>';
			});
			tree_opts += '</ul>';
		}
		else
		{
			tree_opts = "Not Found!";
		}
		// fill in tree result
		$('.tree-result-list').html(tree_opts);
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
	var sCurrent = $('.page-progress').attr('data-current');
	var result   = true

	var dAdd     = $('.page-progress #step-add');
	var dFilter  = $('.page-progress #step-filter');
	var dPublish = $('.page-progress #step-publish');

	var cAdd     = $('.stepAdd');
	var cFilter  = $('.stepFilter');
	var cPublish = $('.stepPublish');

	// if go from step1 to another step
	if(!firstTime && sthis != 'step1' && sCurrent == 'step1')
	{
		var myTitle = $('#title');
		if(!myTitle.val() && $('#question-add').attr('data-id'))
		{
			myTitle.addClass('isError');
			// return false;
		}
	}
	// $('#question-add').addClass('loading');

	switch(sthis)
	{
		default:
		case 'step-add':
		case 'step1':
			if(firstTime)
			{
				cAdd.show();
			}
			sthis = 'step1';
			dAdd.prop('checked', true).parent('.checkbox').addClass('active');
			dFilter.prop('checked', false).parent('.checkbox').removeClass('active');
			dPublish.prop('checked', false).parent('.checkbox').removeClass('active');
			// show step1
			cAdd.slideDown();
			cFilter.slideUp();
			cPublish.slideUp();
			if(!firstTime)
			{
				window.location.hash = 'step1';
			}
			break;

		case 'step-filter':
		case 'step2':
			if(firstTime)
			{
				cFilter.show();
			}
			sthis = 'step2';
			dAdd.prop('checked', true).parent('.checkbox').addClass('active');
			dFilter.prop('checked', true).parent('.checkbox').addClass('active');
			dPublish.prop('checked', false).parent('.checkbox').removeClass('active');
			// show step2
			cAdd.slideUp();
			cFilter.slideDown();
			cPublish.slideUp();
			window.location.hash = 'step2';
			break;

		case 'factor':
			$('#totalPrice').addClass('isHighligh');
			setTimeout(function()
			{
				$('#totalPrice').removeClass('isHighligh');
			}, 700);
		case 'step-publish':
		case 'step3':
			if(firstTime)
			{
				cPublish.show();
			}
			sthis = 'step3';
			dAdd.prop('checked', true).parent('.checkbox').addClass('active');
			dFilter.prop('checked', true).parent('.checkbox').addClass('active');
			dPublish.prop('checked', true).parent('.checkbox').addClass('active');
			// show step3
			cAdd.slideUp();
			cFilter.slideUp();
			cPublish.slideDown();
			window.location.hash = 'step3';
			break;
	}
	// change title of btn
	$("#next-step").text($('.page-progress .active:last').attr('data-btn'));
	if(sthis == 'step3')
	{
		var pollAddr = $('#short_url').val();
		$("#next-step").attr('href',  pollAddr);
	}
	else
	{
		$("#next-step").attr('href', null);
	}

	$('.page-progress').attr('data-current', sthis);
	setTimeout(function()
	{
		$('#question-add').removeClass('loading');
		detectPercentage();
	}, 300);
	scrollSmoothTo('top', null, 300);
	return result;
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
				detectStep('step2');
				break;

			case 'step-filter':
				detectStep('step3');
				break;

			case 'step-publish':
				// do nothing. only navigate
				break;
		}
	});

	$(document).on('click', ".stepPublish .changeStatus", function()
	{
		var requestedStatus = $('.stepPublish .changeStatus').attr('data-request');
		if(requestedStatus === 'publish')
		{
			requestSavingData(true, requestedStatus);
		}
		else
		{
			changePollStatus(requestedStatus);
		}
	});
}


/**
 * [changePollStatus description]
 * @param  {[type]} _status [description]
 * @return {[type]}         [description]
 */
function changePollStatus(_status)
{
	if(!_status)
	{
		_status = 'publish';
	}

	$('#question-add').ajaxify(
	{
		ajax:
		{
			data: {status : _status},
			// abort: true,
			method: 'post',
			success: function(e, data, x)
			{
				console.log('successfully change status!');
			},
			error: function(e, data, x)
			{
				console.log('error!');
			}
		},
		// lockForm: false,
	});
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
			var optCount   = countQuestionOpts()-1;
			optCount       = optCount<=2? 2: optCount;
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
	$('.badge.active[data-ratio-val]').each(function(index, el)
	{
		var currentRatio = parseInt($(el).attr('data-ratio-val'));
		totalPercent     += currentRatio;
	});
	// set return result
	var returnResult    = {};
	returnResult.base   = basePrice;
	returnResult.person = totalPerson;
	returnResult.filter = totalPercent;
	// change percent to ratio
	totalPercent = totalPercent/100;
	totalPrice   = totalPerson + (totalPerson * totalPercent);
	totalPrice   = totalPrice * basePrice;
	totalPrice   = Math.round(totalPrice);
	// set value to show to enduser
	setCompleteVal(totalEl, totalPrice);

	// return final result
	return returnResult;
}


/**
 * [calcTotalPrice description]
 * @return {[type]} [description]
 */
function calcTotalPrice()
{
	var totalPrice = 0;
	// get filter data
	var filters    = calcFilterPrice();
	var prAdd      = $('#prAdd');
	var prPerson   = $('#prPerson');
	var prFilter   = $('#prFilter');
	var prBrand    = $('#prBrand');
	var prTotal    = $('#prTotal');
	var prCash     = $('#prCash');
	var prBalance  = $('#prBalance');


	// if person count isset show or hide
	if(filters.person)
	{
		// remove base price
		prPerson.removeClass('hide');
		// if exsit show or hide
		if(filters.filter)
		{
			prFilter.removeClass('hide');
		}
		else
		{
			prFilter.addClass('hide');
		}
		// hide base price
		// prAdd.addClass('hide');
	}
	else
	{
		// hide person and filter
		prPerson.addClass('hide');
		prFilter.addClass('hide');
		// show add base price
		prAdd.removeClass('hide');
	}
	// add question price to total
	totalPrice = parseInt(prAdd.find('.pr').attr('data-val'));
	// if person is correct
	if(typeof filters.person == "number")
	{
		var personPrice = filters.base * filters.person;
		prPerson.attr('data-per-person', filters.base).attr('data-person', filters.person);
		// set value
		setCompleteVal(prPerson.find('.pr'), personPrice);

		totalPrice += personPrice;

		if(typeof filters.filter == "number")
		{
			// person * filter
			var filterPrice = personPrice * (filters.filter/100);

			prFilter.find('span:first-child b').text(fitNumber(filters.filter) + '%');
			setCompleteVal(prFilter.find('.pr'), filterPrice);

			totalPrice += filterPrice;
		}
	}

	// brand
	if($('#meta_branding').is(":checked"))
	{
		prBrand.removeClass('hide');
		var untilBrand = calcUntilPrice('prBrand');
		// set value of brand
		var brandFactor = parseInt(prBrand.attr('data-val'));
		prBrand.find('span:first-child b').text('x' + fitNumber(brandFactor));
		var brandPrice  = untilBrand * brandFactor;
		// set value of price
		setCompleteVal(prBrand.find('.pr'), brandPrice);

		// add brand to totalPrice
		totalPrice += brandPrice;
	}
	else
	{
		prBrand.addClass('hide');
	}

	// set total price
	setCompleteVal(prTotal.find('.pr'), totalPrice);
	// get and set cash
	var myCash = $('#financial-box .total .value').attr('data-val');
	setCompleteVal(prCash.find('.pr'), myCash);
	// calc final balance and set
	var finalBalance = myCash - totalPrice;
	setCompleteVal(prBalance.find('.pr'), finalBalance);
	// change text of new status allowed
	setStatusText();
	if(finalBalance < 0)
	{
		prBalance.addClass('isHighligh');
		// show charge
		$('.stepPublish .charge').slideDown();
		// hide publish
		// $('.stepPublish .changeStatus').slideUp();
	}
	else
	{
		prBalance.removeClass('isHighligh');
		// hide charge
		$('.stepPublish .charge').slideUp();
		// show publish
		// $('.stepPublish .changeStatus').slideDown();
	}

	// show on topbox
	$('#financial-box .cost .value').attr('data-val', totalPrice).text(fitNumber(totalPrice));
	$('#financial-box .cost').addClass('isCurrent');

	return totalPrice;
}


/**
 * [setStatusText description]
 */
function setStatusText()
{
	var cStatus    = $('#question-add').attr('data-status');
	var txtDraft   = $('.stepPublish .changeStatus').attr('data-draft');
	var txtPublish = $('.stepPublish .changeStatus').attr('data-publish');

	switch (cStatus)
	{
		case 'awaiting':
		case 'publish':
			$('.stepPublish .changeStatus').text(txtDraft);
			$('.stepPublish .changeStatus').attr('data-request', 'draft');
			break;

		default:
		case 'draft':
			$('.stepPublish .changeStatus').text(txtPublish);
			$('.stepPublish .changeStatus').attr('data-request', 'publish');
			break;
	}
	// show btn after change text
	$('.stepPublish .changeStatus').fadeIn().removeClass('hide');
}


/**
 * [setCompleteVal description]
 * @param {[type]} _el [description]
 */
function setCompleteVal(_el, _val)
{
	_el.attr('data-val', _val).text(fitNumber(_val));
}


/**
 * [calcUntilPrice description]
 * @param  {[type]} _current [description]
 * @return {[type]}          [description]
 */
function calcUntilPrice(_current)
{
	var continuesValue = 0;
	var untilCurrent   = null;
	$('#totalPrice div:not(".hide") .pr').each(function()
	{
		// if find this element
		if(untilCurrent === null)
		{
			// if($(this).closest('div').attr('id') === 'pr' + _current.ucFirst())
			if($(this).closest('div').attr('id') === _current)
			{
				untilCurrent = continuesValue;
			}
		}

		var thisValue = $(this).attr('data-val');
		if(thisValue)
		{
			continuesValue += parseInt(thisValue);
			$(this).closest('div').attr('data-until', continuesValue);
		}
	});

	return untilCurrent;
}



String.prototype.ucFirst = function()
{
	return this.charAt(0).toUpperCase() + this.slice(1);
}


/**
 * [requestSavingData description]
 * @return {[type]} [description]
 */
function requestSavingData(_manualRequest, _status)
{
	// cal total price with a short delay to give all
	setTimeout(function()
	{
		calcTotalPrice();
	},50);

	if(_manualRequest === true)
	{
		sendQuestionData(_status);
	}
	else if($('html').attr('data-develop') != undefined)
	{
		// we are Godzilla
		console.log('we are Godzilla')
	}
	else
	{
		console.log('automatically saving...');
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
}


/**
 * [sendQuestionData description]
 * @return {[type]} [description]
 */
function sendQuestionData(_status)
{
	// change status to syncing
	syncing(true);

	var myPoll  = {};
	myPoll      = prepareQuestionData();
	myPoll.from = prepareQuestionFilter();
	console.log(myPoll);
	myPoll      = JSON.stringify(myPoll);


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
					$('#question-add').attr('data-id', id);
					if(myurl.indexOf('add/'+ id) >= 0)
					{
						// edit
					}
					else
					{
						// change language if transfered to new location after a short delay
						setTimeout(function(){setLanguageURL();}, 300);

						var myurl = window.location.pathname + '/' + id + window.location.hash;
						// add new and redirect url
						if(e.result.short_url)
						{
							$('#short_url').val(e.result.short_url);
						}
						Navigate(
						{
							url: myurl,
							fake: true,
						});
					}
					// change status if wanna to change it
					if(_status)
					{
						changePollStatus(_status);
					}
					var limit = null;
					if(e.msg && e.msg.member_exist)
					{
						limit = parseInt(e.msg.member_exist);
						console.log('limit is: ' + limit);
					}
					if(limit < 100)
					{
						lockStep();
					}
					$('#rangepersons').rangeSlider('option', 'max_limit', limit, 1);
				}
				syncing();
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
 * [lockStep description]
 * @return {[type]} [description]
 */
function lockStep()
{
	console.log('lock step!');

}


/**
 * [syncing description]
 * @param  {[type]} _status   [description]
 * @param  {[type]} _progress [description]
 * @return {[type]}           [description]
 */
function syncing(_status, _progress)
{
	if(_status === true)
	{
		// change status to syncing
		$('.sync').attr('data-syncing', true);
	}
	else if(_status === false)
	{
		// change status to syncing
		$('.sync').attr('data-syncing', false);
	}
	else
	{
		$('.sync').attr('data-syncing', null);
	}
}


/**
 * [prepareQuestionData description]
 * @return {[type]} [description]
 */
function prepareQuestionData()
{
	var myQuestion     = {};
	myQuestion.title   = $('#title').val();
	// change status of title
	if($('#title').val())
	{
		$('#title').removeClass('isError')
	}
	else
	{
		$('#title').addClass('isError');
	}
	// myQuestion.type    = 'poll';
	myQuestion.answers = {};

	// for each input added by user
	$('.input-group.sortable > li').each(function(_e)
	{
		var $this   = $(this);
		var row     = $this.attr('data-row');
		// dont get id from html
		// row         = _e+1;
		var thisOpt = {};
		// title
		thisOpt.title   = $('#answer'+row).val();
		// set file id if exist
		var fileId = $this.find('.preview').attr('data-file-id');
		if(fileId)
		{
			thisOpt.file = fileId;
		}

		// complete profile
		if($('#complete-profile').is(":checked"))
		{
			thisOpt.profile = $this.find('.profile-module').attr('data-val');
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

					thisOpt.select.is_true = $this.find('#true'+row).is(":checked");
				}
				// if checked score, save it
				if($('#score').is(":checked"))
				{
					thisOpt.select.score = {};
					thisOpt.select.score.value = parseInt($this.find('#score'+row).val());
					if($('#score-advance').is(":checked"))
					{
						thisOpt.select.score.group = $this.find('.scoreCat').val();
					}
				}
				break;
		}
		// add to total array of this question
		myQuestion.answers[_e] = thisOpt;
	});
	// get branding data
	if($('#meta_branding').is(":checked"))
	{
		myQuestion.brand       = {};
		myQuestion.brand.title = $('#answer_brand').val();
		myQuestion.brand.url   = $('#answer_brand_url').val();
	}
	// get tree data
	if($('#tree').is(':checked'))
	{
		myQuestion.tree         = {};
		myQuestion.tree.parent  = $('#tree-search').attr('data-val');
		myQuestion.tree.answers = [];

		$('.tree-result-list input:checked').each(function(_e)
		{
			var myId = $(this).attr('id');
			myId     = myId.substr(9);
			myQuestion.tree.answers.push(myId);
		});
		if(myQuestion.tree.answers.length < 1)
		{
			myQuestion.tree.answers = true
		}
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
	// set for multi
	if(typeof myQuestion.options.multi === "object")
	{
		var multiRangeEl = $('#multiple-range');
		var multiMin     = parseInt($("input[name=multiple-range-min]").val());
		var multiMax     = parseInt($("input[name=multiple-range-max]").val());

		// set for min value
		if(multiRangeEl.attr('data-min') == multiMin)
		{
			// do not need to set
			// myQuestion.options.multi.min = null;
		}
		else
		{
			myQuestion.options.multi.min = multiMin;
		}
		// set for max value
		if(multiRangeEl.attr('data-max') == multiMax)
		{
			// do not need to set
			// myQuestion.options.multi.max = null;
		}
		else
		{
			myQuestion.options.multi.max = multiMax;
		}
	}

	// save randomSort for multiple selection
	myQuestion.options.random_sort = $('#random_sort').is(":checked");
	// data of publish page
	// category
	if($('#cat0').attr('data-val'))
	{
		myQuestion.cat = $('#cat0').attr('data-val');
		if($('#cat1').attr('data-val'))
		{
			myQuestion.cat = $('#cat1').attr('data-val');
			if($('#cat2').attr('data-val'))
			{
				myQuestion.cat = $('#cat2').attr('data-val');
			}
		}
	}
	// articles
	myQuestion.articles = $('#article').attr('data-val');
	if(myQuestion.articles)
	{
		myQuestion.articles = JSON.parse(myQuestion.articles);
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


// ================================================================== @/add
// route(/\@\/add/, function()
route(/\@\/add(|\/[^\/]*)$/, function()
{
	// run on input change and add new opt for this question
	$(this).on('input', '.input-group.sortable > li .input[type="text"]', function(event)
	{
		checkAddOpt();
	});

	// --------------------------------------------------------------------------------- Delete Elements
	// show and hide delete btn on special condition
	$(this).on('mouseenter', '.input-group.sortable > li', function()
	{
		showQuestionOptsDel($(this).find('.delete'));
	}).on('focus', '.input-group.sortable > li input[type="text"]', function()
	{
		showQuestionOptsDel($(this).closest('li').find('.delete'));
	});

	// on input and press shift+del remove current record
	// $(this).on('keyup', '.input-group.sortable > li input', function(e)
	// {
	// 	if(countQuestionOpts() > 2)
	// 	{
	// 		if(e.shiftKey && e.keyCode === 46)
	// 		{

	// 			$(this).closest('li').find('.delete').click();
	// 		}
	// 	}
	// });

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
	$(this).on('click', '.input-group.sortable > li .delete', function()
	{
		showQuestionOptsDel(this, true);
	}).on('input', '.input-group.sortable > li .delete', function(e)
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

}).once(function()
{
	// import needed js
	$import('lib/rangeSlider/rangeSlider.js', 'runRangeSlider', null, 150);
	$import('lib/sortable/Sortable.min.js', 'setSortable', null, 200);
	$import('lib/awesomplete/awesomplete.min.js', 'fillAuto', null, 300);
	$import('lib/tagDetector/tagDetector.js', 'runTagDetector', null, 400);

	// simulateTreeNavigation();
	checkNextStep();
	// handle copy btn
	handleCopy();
	// // draw factor and fill total price on start
	// calcTotalPrice();
	// draw media for each item contain media
	drawMedia();

	$('.page-progress input').on('click', function(e)
	{
		return detectStep($(this).attr('name'));
		// e.stopPropagation();
		// return false;
	});
	// on click on price goto step3
	$('#financial-box .cost').on('click', function(e)
	{
		detectStep('factor');
	});
	// on click on price goto step3
	$('.sync:not([data-syncing])').on('click', function(e)
	{
		requestSavingData(true);
	});
	// on init
	detectStep();

	// on init calc price
	$('#rangepersons').bind('range-slider::init::after', function(_e, _min, _max)
	{
		// ready to send data
		calcTotalPrice();
	});

	$('#rangepersons').bind('range-slider::change', function(_e, _min, _max)
	{
		calcTotalPrice();

		// if value isset to zero hide filters
		if(_max == 0)
		{
			$('.stepFilter #filter-conditions').fadeOut();
		}
		else
		{
			$('.stepFilter #filter-conditions').fadeIn();
		}
	});

	$('#rangepersons').bind('range-slider::changeAfter', function(_e, _min, _max)
	{
		// console.log('range changed...');
		// ready to send data
		requestSavingData();
	});


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
		// var output = $(this).parents('.ultra').find('.preview');
		// var imagePreview = showPreview(this, output);

		giveFile(this);
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
		startCrop(_el);
	});
	// on click on preview of imagee
	$('body').on("click", ".preview", function(_e, _el)
	{
		startCrop(this);
	});
	// on click on preview of imagee
	// $('body').on("click", "#modal-preview .btn", function(_e, _el)
	// {
	// 	// complete croping
	// 	$('#modal-preview').trigger('close');
	// });


	// ================================================================== filter
	$(this).on('click','button', function()
	{
		detectPercentage(true);
	});
	detectPercentage();

	// on open tree load content to it
	// $(window).off( "response:open");
	// $(window).on( "response:open", function(_obj, _name, _value)
	// {
	//	calcTotalPrice();
	// });

	// ================================================================== publish
	// runAutoComplete();

	$('#question-add').on('change', 'input, textarea', function()
	{
		requestSavingData();
	});

});



/**
 * [removeFile description]
 * @return {[type]} [description]
 */
function removeFile(_preview)
{
	_preview.attr('data-file-id', null);
	_preview.attr('data-file-url', null);
	_preview.attr('data-file-type', null);
	// _preview.attr('data-file-temp', null);
	// _preview.attr('data-file-local', null);
	showPreview(_preview, true);

	// close modal
	$('#modal-preview').trigger('close');
}


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
//
// 						}
// 					}
// 				}
// 			});
// 		});
// 	});
// });


/**
 * [saveAnswers description]
 * @return {[type]} [description]
 */
function saveAnswers(_type)
{
	var data = {};
	switch (_type)
	{
		case "skip":
			data.skip = true;
			break;

		default:
			var answerSelected = false;
			var answerBox      = $('ul[data-answer-type]');
			switch (answerBox.attr('data-answer-type'))
			{
				case "select":
					var selectedAns = answerBox.find('input[name="anwserOpt"]:checked').val();
					data.answer     = {};
					if(selectedAns)
					{
						data.answer[selectedAns] = true;
						answerSelected           = true;
					}
					break;

				case "multi":
					var checkedAns = answerBox.find('input[type="checkbox"]:checked');
					data.answer = {};
					if(checkedAns.length)
					{
						checkedAns.each(function()
						{
							var myVal = $(this).val();
							console.log(myVal);
							data.answer[myVal] = true;
						});
						answerSelected = true;
					}
					break;

				case "ordering":
					// complete as soon as posible...
					break;
			}
			break;
	}
	data = JSON.stringify(data);

	$('#saveAnswers').ajaxify(
	{
		ajax:
		{
			data: {data : data},
			// abort: true,
			method: 'post',
			success: function(e, data, x)
			{
				console.log('successfully save answer..');
				// redraw chart after successfully change poll
				redrawChart()
			},
			error: function(e, data, x)
			{
				console.log('error on saving answer!');
			}
		},
		// lockForm: false,
	});
}


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
	// hide cost box on all page except add new poll
	$('#financial-box .cost').removeClass('isCurrent');

	$('#saveAnswers').click(function()
	{
		saveAnswers();
	});

	$('#skipAnswers').click(function()
	{
		saveAnswers('skip');
	});
	// check and uncheck radios
	$('ul[data-answer-type] input[name="anwserOpt"]').click(function()
	{
		if($(this).attr('checked'))
		{
			$(this).attr('checked', false);
		}
		else
		{
			$(this).attr('checked', true);
		}

		$(this).attr('checked', $(this).is(':checked'));
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
	setProperty('favorite');
	setProperty('heart');


	// load needed js file
	loadFiles();
}


/**
 * [loadFiles description]
 * @return {[type]} [description]
 */
function loadFiles()
{
	$import('lib/dataResponse/dataResponse.js', 'runDataResponse', null, 50);
}