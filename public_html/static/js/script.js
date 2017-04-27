/**
 * v1.0.0
 */

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

	if(callFunction(_func, null, true))
	{
		// console.log('call func: ' + _src + " ::" + _func);
		callFunction(_func, myArgs);
	}
	else
	{
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
				// console.log('load once: ' + _src);
				// else if not exist import it for calling!
				$.cachedScript(_src).done(function(_script, _textStatus)
				{
					myArgs.firstTime = true;
					callFunction(_func, myArgs);
				});
			}
		}, _delay);
	}
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
	$('[data-copy]').off('click');
	$('[data-copy]').on('click', function()
	{
		console.log(22);
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
		$(this).parents('.dropmenu').addClass('open');
		// set scroll to top of page
		$('body').scrollTop(0);

	}).on('blur', function()
	{
		$(this).parents('.dropmenu').removeClass('open');
	});
}


/**
 * [disabledLink description]
 * @return {[type]} [description]
 */
function disabledLink()
{
	$('a.disabled').on('click', function(_e)
	{
		if($(this).hasClass('disabled') === true)
		{
			_e.preventDefault();
			return false;
		}
	})
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
			// backspace
			case '8':
				ifHoverChangeIt(false);
				break;

			// enter
			case '13':
				ifHoverChangeIt(true);

				var focusedElement = $(e.target);
				if(focusedElement.is('.stepAdd .input'))
				{
					var thisEl = $('.stepAdd .input');
					var selectedIndex = thisEl.index(focusedElement);
					thisEl.eq(selectedIndex + 1).focus();
				}
				break;

			// shift + enter
			case '13shift':
				var focusedElement = $(e.target);
				if(focusedElement.is('.stepAdd .input'))
				{
					var thisEl = $('.stepAdd .input');
					var selectedIndex = thisEl.index(focusedElement);
					thisEl.eq(selectedIndex - 1).focus();
				}
				break;

			// delete
			case '46':
				ifHoverChangeIt(false);
				break;


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
					callFunction('redrawPollChart');
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
				}
				e.preventDefault();
				break;

			// f1
			case '112':
				$import('lib/introJs/introJs.js', 'runHelp');
				e.preventDefault();
				break;

			default:
				break;
		}
		// if we are in answer page allow to select with shortkey
		shortkeySelectAnswerItem(e.which)
	});
}


/**
 * [ifHoverChangeIt description]
 * @param  {[type]} _newStatus [description]
 * @return {[type]}            [description]
 */
function ifHoverChangeIt(_newStatus)
{
	var isHoverOpt = $('.poll-options li:hover');
	if(isHoverOpt.length === 1)
	{
		if(_newStatus === true)
		{
			isHoverOpt.find('label').click();
		}
		else
		{
			uncheckRadio(isHoverOpt.find('label'));
		}
	}
}


/**
 * if we are in answer test page handle keyboard
 * @param  {[type]} _item [description]
 * @return {[type]}       [description]
 */
function shortkeySelectAnswerItem(_key)
{
	// if user is typing something, do nothing
	var focused = document.activeElement;
	if($(focused).parents('ul[data-answer-type]').length === 0)
	{
		if($(focused).is('input') || $(focused).is('textarea'))
		{
			return false;
		}
	}
	else
	{
		if($(focused).is('input[type="text"'))
		{
			return false;
		}
	}

	// if we are in answer test page handle keyboard
	if($('ul[data-answer-type]').length)
	{
		// special handle for select answers
		var charNumber = _key - 48;
		if((_key >= 65 && _key <= 73))
		{
			charNumber -= 16;
		}
		if((_key >= 97 && _key <= 105))
		{
			charNumber -= 48;
		}
		// if user select one to nine, select it if exist
		if(charNumber >= 1 && charNumber <= 9)
		{
			charNumber -= 1;
			var myLabel = $('ul[data-answer-type]>li>label').eq(charNumber);
			if(myLabel.length > 0)
			{
				// if is checked uncheck it
				if(myLabel.find('input').is(':checked'))
				{
					uncheckRadio(myLabel);
				}
				// else simulate click on it
				else
				{
					myLabel.click();
				}
				// return true on successfully edited
				return true;
			}
		}
	}
	return false;
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
		// check for add new opt
		checkAddOpt();
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
	var urlHash     = window.location.hash;
	var indexOfLang = urlPath.indexOf('/' + $('html').attr('lang'));
	var urlBase     = $('base').attr('href');
	urlBase         = urlBase.substr(0, urlBase.indexOf('/', 8));

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
			// lang = '/' + lang;
		}
		var myUrl = urlPath;
		if(lang)
		{
			myUrl = lang + '/' + myUrl;
		}
		// add hash if exist
		if(urlHash)
		{
			myUrl += urlHash;
		}
		myUrl = urlBase + '/' + myUrl;
		$(index).attr('href', myUrl.trim('/'));
	})
}


function homepageHighlight(_el)
{
	// $(document).on(
	// {
	// 	mouseenter: function ()
	// 	{
	// 		$(this).addClass('hideGray');
	// 	},
	// 	mouseleave: function ()
	// 	{
	// 		var $this = $(this);
	// 		setTimeout(function()
	// 		{
	// 			$this.removeClass('hideGray');
	// 		}, 1000);
	// 	}
	// }, '.customers-container .customer');


	if(!_el)
	{
		_el = $('.customers-container');
	}
	if(_el.length)
	{
		var posStart = _el.offset().top;
		var posEnd   = posStart + _el.height() - 100;
		var $window  = $(window);

		$window.scroll(function()
		{
			if( $window.scrollTop() >= (posStart - window.innerHeight / 2 + 100)  && posEnd > $window.scrollTop() )
			{
				_el.addClass('littleGray');
			}
			else
			{
				_el.removeClass('littleGray');
			}
		});
	}
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
	// handle copy btn
	handleCopy();
	// highlight homepage gray shapes
	homepageHighlight();
	// load maps and chart js
	$import('lib/amcharts/amcharts.js', 'drawChart', null, 70);
	var loadMapDelay = 50;
	if($('.map').length === 0)
	{
		loadMapDelay = 500;
	}
	$import('lib/ammap/ammap.js', 'getMyMapData', null, loadMapDelay);

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
	$('ul[data-answer-type] label').contextmenu(function(_e)
	{
		// for right click
		if(_e.button == 2)
		{
			uncheckRadio(this);
			return false;
		}
		// for one click
		// if($(this).attr('checked'))
		// {
		// 	$(this).attr('checked', false);
		// }
		// else
		// {
		// 	$(this).attr('checked', true);
		// }
		// $(this).attr('checked', $(this).is(':checked'));
	});
	// redraw chart after change group of needed
	$('input[name="chart_result"]').on('change', function()
	{
		callFunction('redrawPollChart');
	});

	// check validation on input and show custom msg
	setCustomValidityMsg();
	// search in polls
	searchInPolls();
});


// ************************************************************************************************************ Add
/**
 * add new record of answer
 */
function checkAddOpt()
{
	var numberOfEmptyInputs = 0;
	var numberOFInputs      = $('.input-group.sortable>li').length;
	var emptyRowNumber      = [];

	// check if current element has not value and we have no empty inputs
	$.each($('.input-group.sortable>li'), function(key, value)
	{
		if(!$(this).find('.element .input[type="text"]').val() && !$(this).find('.preview').attr('data-file-temp'))
		{
			numberOfEmptyInputs += 1;
			emptyRowNumber.push(key);
		}
	});

	if(countQuestionOpts() >= 40)
	{
		console.log('maximum opt is reached!');
	}
	// elseif we have only one empty inputs and we needed one add it
	else if (numberOfEmptyInputs < 1 && !$('.input-group.sortable').hasClass('editing'))
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
function addNewOpt(_type, _title, _placeholder, _defaulVal, _group)
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
	// remove file data
	removeFile(template.find('.preview'));
	// remove image and audio
	template.find('.preview img').remove();
	template.find('.audio audio').remove();
	// clear tagbox
	template.find('.tagDetector').attr('data-val', '');
	template.find('.tagDetector .tagBox').html('');

	$('.input-group.sortable').append(template);
	// rearrange after add new element
	rearrangeSortable();

	if(_type === 'other')
	{
		template.find('input.input').val(_defaulVal);
	}

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
	var questionBox = $('#question-add');
	if(questionBox.attr('data-status') === 'draft' || questionBox.attr('data-status') === "" || questionBox.attr('data-admin') !== undefined)
	{
		// allow delete
	}
	else
	{
		return false;
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

	setMultipleValueRange();
}


/**
 * [setMultipleValue description]
 */
function setMultipleValueRange()
{
	// set multiple value
	var countOfFull = countQuestionOpts(true);
	var myMultipe   = $('#multiple-range').data("ionRangeSlider");
	// if now to is in the last, after update set it as last
	if(myMultipe.result.max === myMultipe.result.to)
	{
		myMultipe.update(
		{
			max: countOfFull,
			to: countOfFull,
		});
	}
	else
	{
		myMultipe.update(
		{
			max: countOfFull,
		});
	}
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
			highlightIt($('#totalPrice'));
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
	var nextStepBtn = $("#next-step");
	nextStepBtn.text($('.page-progress .active:last').attr('data-btn'));
	if(sthis == 'step3')
	{
		var pollAddr = $('#short_url').val();
		// if we have address of poll on step3 change to it
		if(pollAddr)
		{
			nextStepBtn.attr('href',  pollAddr);
		}
		else
		{
			nextStepBtn.text(nextStepBtn.attr('data-draft'));
			nextStepBtn.attr('href',  null);
		}
	}
	else
	{
		nextStepBtn.attr('href', null);
	}

	$('.page-progress').attr('data-current', sthis);
	setTimeout(function()
	{
		$('#question-add').removeClass('loading');
		detectPercentage();
	}, 300);
	scrollSmoothTo('top', null, 300);
	setLanguageURL();
	return result;
}


/**
 * [checkNextStep description]
 * @return {[type]} [description]
 */
function checkNextStep()
{
	$(document).off('click', "#next-step");
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
				if($("#next-step").attr('href') === undefined)
				{
					requestSavingData(true);
				}
				// do nothing. only navigate
				break;
		}
	});

	$(document).on('click', ".stepPublish .changeStatus", function()
	{
		var requestedStatus = $(this).attr('data-request');
		if(requestedStatus === 'publish')
		{
			// requset saving data with change status
			requestSavingData(true, requestedStatus);
		}
		else
		{
			// directly change it to draft
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
				var newStatus = '';
				if(e.msg && e.msg.new_status)
				{
					newStatus = e.msg.new_status;
				}
				// set new status and change all elements depending it change
				setStatusText(newStatus);
				// if we dont have any error
				if(e.status && e.status != 0)
				{
					console.log('successfully change status!');
				}
				else
				{
					// error on change
					console.log('error on changing status; fail!');
				}
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
	var totalEl      = $('#prPerson .pr');
	var basePrice    = parseInt(totalEl.attr('data-basePrice'));
	var totalPerson  = getRangeSliderValue('person');
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
function calcTotalPrice(_delay)
{
	if(!_delay)
	{
		_delay = 0
	}

	setTimeout(function()
	{
		var totalPrice   = 0;
		// get filter data
		var filters      = calcFilterPrice();
		var tblFactor    = $('#totalPrice');
		var prAdd        = $('#prAdd');
		var prPerson     = $('#prPerson');
		var prFilter     = $('#prFilter');
		var prBrand      = $('#prBrand');
		var prTotal      = $('#prTotal');
		var prCash       = $('#prCash');
		var prBalance    = $('#prBalance');
		var prHideResult = $('#prHideResult')

		var selectedPrivacy = $('input[name="filter_privacy"]:checked').val();
		if(selectedPrivacy === 'public')
		{
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
				// prAdd.removeClass('hide');
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
		}
		else
		{
			prPerson.addClass('hide');
			prFilter.addClass('hide');
		}

		// send hidden result
		if($('#hide_result').is(":checked"))
		{
			prHideResult.removeClass('hide');
			var HideResultFactor = parseInt(prHideResult.attr('data-val'));
			totalPrice += HideResultFactor;
		}
		else
		{
			prHideResult.addClass('hide');
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
		if(!myCash)
		{
			myCash = 0;
		}
		setCompleteVal(prCash.find('.pr'), myCash);
		// calc final balance and set
		var finalBalance = myCash - totalPrice;
		// if data-free is exist dont calc price of poll
		if(tblFactor.attr('data-free') !== undefined)
		{
			finalBalance += totalPrice;
		}
		setCompleteVal(prBalance.find('.pr'), finalBalance);
		// save link of charge for use next time
		if($('.stepPublish .charge').data('hrefBase') === undefined)
		{
			$('.stepPublish .charge').data('hrefBase', $('.stepPublish .charge').attr('href'));
		}
		// if do not have money
		if(finalBalance < 0)
		{
			prBalance.addClass('isHighlight');
			// calc needed price and link for charge
			var neededMoney = $('.stepPublish .charge').data('hrefBase') + "?amount=" + Math.abs(finalBalance);
			// show btn and change url for charge
			$('.stepPublish .charge').fadeIn().css("display","inline-block").attr('href', neededMoney).removeClass('hide');
			// hide publish
			$('.stepPublish .changeStatus').fadeOut();
		}
		else
		{
			prBalance.removeClass('isHighlight');
			// hide charge
			$('.stepPublish .charge').fadeOut();
			// show publish
			$('.stepPublish .changeStatus').fadeIn().css("display","inline-block").removeClass('hide');
		}

		// show on topbox

		var elCost    = $('#financial-box .cost');
		var elCostVal = $('#financial-box .cost .value');

		if(elCostVal.attr('data-val') != totalPrice)
		{
			elCostVal.attr('data-val', totalPrice).text(fitNumber(totalPrice));
			elCost.addClass('isCurrent');
			// highlight cost
			highlightIt(elCost);
		}

		return totalPrice;
	}, _delay);
}


/**
 * [setCustomTimeout description]
 * @param {[type]} _el    [description]
 * @param {[type]} _delay [description]
 * @param {[type]} _func  [description]
 */
function setCustomTimeout(_el, _delay, _func)
{

}


/**
 * change elemetns depending on new status
 * @param {[type]} _newStatus [description]
 */
function setStatusText(_newStatus)
{
	var questionBox     = $('#question-add');
	var changeStatusBtn = $('.stepPublish .changeStatus');
	var txtDraft        = changeStatusBtn.attr('data-draft');
	var txtPublish      = changeStatusBtn.attr('data-publish');
	var oldStatus       = changeStatusBtn.attr('data-request');
	var lockIt          = false;
	// if we dont have status give it from main box
	if(!_newStatus)
	{
		var _newStatus = questionBox.attr('data-status');
	}
	switch (_newStatus)
	{
		case 'awaiting':
		case 'publish':
			changeStatusBtn.text(txtDraft);
			changeStatusBtn.attr('data-request', 'draft');
			lockIt = true;
			break;

		default:
		case 'draft':
			changeStatusBtn.text(txtPublish);
			changeStatusBtn.attr('data-request', 'publish');
			lockIt = false;
			break;
	}
	if(questionBox.attr('data-admin') !== undefined)
	{
		lockIt = false;
	}
	if(_newStatus)
	{
		// after load completely lock elements
		$(function() {
			lockAllElements(lockIt);
		});
	}


	// show btn after change text
	// changeStatusBtn.fadeIn().css("display","inline-block").removeClass('hide');

	// if is the same return false
	if(oldStatus === _newStatus)
	{
		return false;
	}
	// else return true
	return true;
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


/**
 * [ucFirst description]
 * @return {[type]} [description]
 */
String.prototype.ucFirst = function()
{
	return this.charAt(0).toUpperCase() + this.slice(1);
}


/**
 * [lockAllElements description]
 * @param  {[type]} _status [description]
 * @return {[type]}         [description]
 */
function lockAllElements(_lock, _timing)
{
	var questionBox = $('#question-add');
	if(questionBox.attr('data-admin') !== undefined)
	{
		_lock = false;
	}
	switch (_lock)
	{
		case true:
			// lock
			// change elements
			questionBox.find('input').attr('disabled', 'disabled').attr('data-lock', '');
			questionBox.find('textarea').attr('disabled', 'disabled').attr('data-lock', '');
			// lock all range
			changeLockStatusOfRanges(true);
			questionBox.find('.sync').attr('data-lock', '');
			break;

		case false:
			// unlock
			// change elements
			questionBox.find('input').attr('disabled', null).attr('data-lock', null);
			questionBox.find('textarea').attr('disabled', null).attr('data-lock', null);
			// unloack all range
			changeLockStatusOfRanges(false);
			questionBox.find('.sync').attr('data-lock', null);
			break;
	}
}

function changeLockStatusOfRanges(_newStatus, _timing)
{
	if(!_timing)
	{
		_timing = 1000;
	}

	setTimeout(function()
	{
		if(_newStatus !== true)
		{
			_newStatus = false;
		}

		$(".rangeSlider").each(function()
		{
			var myRange = $(this).data("ionRangeSlider");
			if(myRange !== undefined)
			{
				myRange.update(
				{
					disable: _newStatus,
				});
			}
		});
	}, _timing);
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
	else if($('.sync').attr('data-manual') != undefined)
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
function sendQuestionData(_status, _asyncAjax)
{
	// change status to syncing
	syncing(true);

	var myPollData = {};
	myQuestionBox  = $('#question-add');
	myPollData     = prepareQuestionData();
	$isAdd         = !$('#question-add').attr('data-id');
	if($isAdd && $.isEmptyObject(myPollData))
	{
		syncing(null, 0);
		return false;
	}

	// request to save request!
	// change array to json
	var myPoll       = JSON.stringify(myPollData);
	var checkWithOld = saveSavedData(myPoll);
	if(_asyncAjax === false)
	{
		if(!$('#question-add').attr('data-id'))
		{
			console.log('on unload if not saved until now skip it and return false');
			return false;
		}
		_asyncAjax = false;
	}
	else
	{
		_asyncAjax = true;
	}

	if(checkWithOld === 'duplicate' && !_status)
	{
		// after a short delay show synced
		syncing(null, true);
		return false;
	}
	// if need to sync with server, sync it!
	// change status of title
	if(myPollData.title)
	{
		$('#title').removeClass('isError')
	}
	else
	{
		$('#title').addClass('isError');
	}


	myQuestionBox.ajaxify(
	{
		ajax:
		{
			data: {data : myPoll},
			// data: myPoll,
			// dataType: "json",
			// contentType:"application/json; charset=utf-8",
			abort: true,
			async: _asyncAjax,
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

						// change language if transfered to new location after a short delay
						setTimeout(function()
						{
							setLanguageURL();
							detectStep();
						}, 300);
					}
					// save successfully saved
					saveSavedData(null, true);
					// change status if wanna to change it
					if(_status)
					{
						changePollStatus(_status);
					}
					var limit = null;
					if(e.msg && e.msg.member_exist !== undefined)
					{
						limit = parseInt(e.msg.member_exist);
						console.log('limit is: ' + limit);
					}
					if(limit < 100)
					{
						lockStep();
					}
					// update range person
					updateRangeSliderPerson(limit);
				}
				// after a short delay show synced
				syncing(null, true);
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

function updateRangeSliderPerson(_limit)
{
	var newLimit      = false;
	var myPersonCount = $('#rangepersons').data("ionRangeSlider");
	var currentLimit  = null;
	// if has old limit, get it
	if(myPersonCount.options && myPersonCount.options.from_max)
	{
		currentLimit = myPersonCount.options.from_max;
	}
	// check conditions
	if(_limit === currentLimit)
	{
		// do nothing!
	}
	else if(_limit === myPersonCount.result.max)
	{
		if(currentLimit === null)
		{
			// do nothing
		}
		else
		{
			// remove limit by set it as null
			newLimit = null;
		}
	}
	else
	{
		newLimit = _limit;
	}

	// if need change limit
	if(newLimit !== false)
	{
		myPersonCount.update(
		{
			from_max: newLimit,
		});
	}
}


/**
 * [getRangeSliderValue description]
 * @param  {[type]} _name [description]
 * @return {[type]}       [description]
 */
function getRangeSliderValue(_name)
{
	var selectorEl  = _name;
	var myRangeData = '';
	if(_name == 'person')
	{
		selectorEl = $('#rangepersons');
		myRangeData = selectorEl.data("ionRangeSlider");
		if(myRangeData)
		{
			return myRangeData.result.from;
		}
		else
		{
			return false;
		}
	}
	else if(_name == 'multiple')
	{
		selectorEl = $('#multiple-range');
	}
	// get data of range
	myRangeData = selectorEl.data("ionRangeSlider");
	// return result
	return myRangeData.result;
}


/**
 * [saveSavedData description]
 * @param  {[type]} _data   [description]
 * @param  {[type]} _result [description]
 * @return {[type]}         [description]
 */
function saveSavedData(_data, _result)
{
	// get mybox for save
	var myBox       = $('#question-add');
	var myBoxLastId = myBox.data('lastId');
	var lastRecord  = null;
	// if exist before this request
	if(myBoxLastId !== undefined && myBox.data('send')[myBoxLastId])
	{
		lastRecord = myBox.data('send')[myBoxLastId];
	}
	// save result only if pass result
	if(_result)
	{
		if(lastRecord)
		{
			console.log(myBox.data('send')[myBoxLastId]);
			myBox.data('send')[myBoxLastId].result = _result;
		}
		// savingData.result
		return true;
	}
	// if result of last is true and duplicate, accept it as duplicate
	if(lastRecord && lastRecord.result === true && lastRecord.data === _data)
	{
		return 'duplicate';
	}
	// fill sena as empty array
	if(myBox.data('send') === undefined)
	{
		myBox.data('send', []);
	}
	// create variable to send for save
	var savingData    = {};
	savingData.data   = _data;
	savingData.result = '';
	// add to array of box
	if(myBox.data('send') === undefined)
	{
		return false;
	}
	myBox.data('send').push(savingData);
	// add count of last id
	var myBoxCurrentId = myBox.data('send').length - 1;
	myBox.data('lastId', myBoxCurrentId);
	// get count of try for send

	// return id
	return myBoxCurrentId;
}


/**
 * [lockStep description]
 * @return {[type]} [description]
 */
function lockStep()
{
	// console.log('lock step!');
}


/**
 * [syncing description]
 * @param  {[type]} _status   [description]
 * @param  {[type]} _progress [description]
 * @return {[type]}           [description]
 */
function syncing(_status, _delay,  _progress)
{
	// set delay to change status to false
	if(_delay === true)
	{
		_delay = 300;
	}
	else if(_delay === false)
	{
		_delay = 0;
	}
	// if is set to true then show progress
	if(_status === true)
	{
		// change status to syncing
		$('.sync').attr('data-syncing', true);
	}
	else if(_status === false)
	{
		$('.sync').attr('data-syncing', false);
	}
	else
	{
		setTimeout(function()
		{
			// change status to syncing
			$('.sync').attr('data-syncing', null);
		}, _delay);
	}
}


/**
 * [prepareQuestionData description]
 * @return {[type]} [description]
 */
function prepareQuestionData()
{
	var myQuestion     = {};
	// if has title
	if($('#title').val())
	{
		myQuestion.title   = $('#title').val();
	}
	// send title file
	var titleFileId = $('#title').parents('.ultra').find('.preview').attr('data-file-id');
	console.log(titleFileId);
	if(titleFileId)
	{
		myQuestion.file = titleFileId;
	}
	// myQuestion.type = 'poll';
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
		// uncomment after change -------------------
		// if($('#answer'+row).val())
		{
			thisOpt.title   = $('#answer'+row).val();
		}
		// set file id if exist
		var fileId = $this.find('.preview').attr('data-file-id');
		if(fileId)
		{
			thisOpt.file = fileId;
		}

		// complete profile
		if($('#complete-profile').is(":checked"))
		{
			var myProfile = $this.find('.profile-module').attr('data-val');
			if(myProfile)
			{
				thisOpt.profile = JSON.parse(myProfile);
			}
		}
		// type of opt
		thisOpt.type = $this.attr('data-type');
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
				// if option is empty remove it
				// uncomment after change -------------------
				// if($.isEmptyObject(thisOpt.select))
				// {
				// 	delete thisOpt.select;
				// }
				break;
		}
		// only if has title or file save this item
		if(!thisOpt.title && !thisOpt.file)
		{
			// uncomment after change -------------------
			// thisOpt = null;
		}

		// add to total array of this question
		if(thisOpt)
		{
			myQuestion.answers[_e] = thisOpt;
		}
	});
	// if answers is empty remove it
	// uncomment after change -------------------
	// if($.isEmptyObject(myQuestion.answers))
	// {
	// 	delete myQuestion.answers;
	// }

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
	// uncomment after change -------------------
	// if($('#summary').val())
	{
		myQuestion.summary     = $('#summary').val();
	}
	// description
	// uncomment after change -------------------
	// if($('#description').val())
	{
		myQuestion.description = $('#description').val();
	}
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
		var multiRangeEl  = $('#multiple-range');
		var myMultipe     = multiRangeEl.data("ionRangeSlider");
		var myMultipeData = myMultipe.result;
		// if now to is in the last, after update set it as last
		myQuestion.options.multi.min = parseInt(myMultipeData.from);
		myQuestion.options.multi.max = parseInt(myMultipeData.to);
	}

	// save randomSort for multiple selection
	// uncomment after change -------------------
	// if($('#random_sort').is(":checked"))
	{
		myQuestion.options.random_sort = $('#random_sort').is(":checked");
	}
	// send prize
	if($('#poll_prize').val()>0)
	{
		myQuestion.options.prize = $('#poll_prize').val();
	}
	// send hidden result
	if($('#hide_result').is(":checked"))
	{
		myQuestion.options.hide_result = $('#hide_result').is(":checked");
	}

	// get poll password
	if($('#password_radio').is(":checked") && $('#poll-password').val())
	{
		myQuestion.options.password = $('#poll-password').val();
	}

	// get poll members
	if($('#members_radio').is(":checked") && $('#poll-members').val())
	{
		myQuestion.options.members = $('#poll-members').val();
	}

	// get poll members
	if($('#send-sms').is(":checked"))
	{
		myQuestion.options.send_sms = true;
	}

	// get poll members
	if($('#send-sms').is(":checked") && $('#sms-text').val())
	{
		myQuestion.options.sms_text = $('#sms-text').val();
	}

	// get poll members
	if($('#poll-class').val())
	{
		myQuestion.options.class = $('#poll-class').val();
	}
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
	// uncomment after change -------------------
	// if($('#article').attr('data-val'))
	{
		myQuestion.articles = $('#article').attr('data-val');
		if(myQuestion.articles)
		{
			myQuestion.articles = JSON.parse(myQuestion.articles);
		}
	}
	// tags
	// uncomment after change -------------------
	// if($('#tags').attr('data-val'))
	{
		myQuestion.tags = $('#tags').attr('data-val');
		if(myQuestion.tags)
		{
			myQuestion.tags = JSON.parse(myQuestion.tags);
		}
	}
	// myQuestion.inHomepage = $('#inHomepage').is(":checked");

	// if option is empty remove it
	if($.isEmptyObject(myQuestion.options))
	{
		delete myQuestion.options;
	}
	else
	{
		// finally add language
		myQuestion.language = $('input[name="ui-language"]:checked').val();
		// and fill step2 data
		myQuestion.privacy  = $('input[name="filter_privacy"]:checked').val();
		if(myQuestion.privacy === 'public')
		{
			myQuestion.from = prepareQuestionFilter();
		}
	}

	console.log(myQuestion);
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
	myFilters.count = getRangeSliderValue('person');
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
 * [handleSyncProcess description]
 * @return {[type]} [description]
 */
function handleSyncProcess()
{
	// ---------------------------------------------------- sync
	$('.sync:not([data-syncing])').on('click', function(e)
	{
		if($(this).attr('data-lock') === '')
		{
			// it's lock. do nothing
			detectStep('step3');
			highlightIt($('.stepPublish .changeStatus'), 1000);
		}
		else
		{
			requestSavingData(true);
		}
	});
	// add data-manul to sync manual, not auto
	$('.sync').on('contextmenu', function(_e)
	{
		_e.preventDefault();
		if($(this).attr('data-manual') === undefined)
		{
			$(this).attr('data-manual', '');
		}
		else
		{
			$(this).attr('data-manual', null);
		}
	});
	// if($('html').attr('data-develop') !== undefined)
	// {
	// 	$('.sync').attr('data-manual', '');
	// }
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
			addNewOpt('other', $(this).attr('data-title'), $(this).attr('data-pl'), $(this).attr('data-defaultVal'));
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
	$import('lib/ionrangeSlider/ion.rangeSlider.min.js', 'runRangeSliderNew', null, 0);
	$import('lib/sortable/Sortable.min.js', 'setSortable', null, 200);
	$import('lib/awesomplete/awesomplete.min.js', 'fillAuto', null, 300);
	$import('lib/tagDetector/tagDetector.js', 'runTagDetector', null, 400);

	// simulateTreeNavigation();
	checkNextStep();
	// // draw factor and fill total price on start
	// calcTotalPrice(200);
	// draw media for each item contain media
	drawMedia();
	// check status of question and if needed lock it
	setStatusText();
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
	// handle sync
	handleSyncProcess();
	// on init
	detectStep();


	var myPersonCounter = $('#rangepersons');
	// on init calc price
	myPersonCounter.bind('range:start', function(_e, _min, _max)
	{
		// ready to send data
		calcTotalPrice();
	});

	myPersonCounter.bind('change', function(_e, _min, _max)
	{
		calcTotalPrice();

	});

	myPersonCounter.on('range:finish', function(_e, _values, _max)
	{
		// ready to send data
		requestSavingData();

		// if value isset to zero hide filters
		if(_values.from == 0 && $('.badge.active').length < 1)
		{
			$('.stepFilter #filter-conditions').fadeOut();
		}
		else
		{
			$('.stepFilter #filter-conditions').fadeIn();
		}
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
		if($(this).hasClass('rangeSlider'))
		{
			return false;
		}
		requestSavingData();
	});
	// before close page save request on the air!
	// for chrome
	$(window).on('beforeunload', function()
	{
		// sendQuestionData(null, false);
	});
	//this will work for other browsers
	// $(window).on("unload", function ()
	// {
	// 	sendQuestionData(null, false);
	// });
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
	_preview.attr('data-file-temp', null);
	_preview.attr('data-file-local', null);
	_preview.attr('data-modal', null);
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
	// --------------------------------------------------------------------------------- Search
	$(document).off('input', '.search-box input');
	$(document).on('input', '.search-box input', function(event)
	{
		var search_timeout = $(this).attr('data-search-timeout');
		if(search_timeout)
		{
			clearTimeout(search_timeout);
		}
		var timeout = setTimeout(function()
		{
			var path = location.pathname;
			var newPath = '';
			if(path.indexOf('/$') >= 0)
			{
				newPath = path.substr(0, path.indexOf('/$')+2);
			}
			else if(path.indexOf('/@/$') >= 0)
			{
				newPath = path.substr(0, path.indexOf('/@/$')+4);
			}
			else if(path.indexOf('/admin/knowledge') >= 0)
			{
				newPath = path.substr(0, path.indexOf('/admin/knowledge')+16);
			}
			// add search text
			_search  = $('.search-box input').val();
			if(_search)
			{
				newPath = newPath+ '/search='+ _search;
			}

			Navigate({ url: newPath, ajax:{method:'get', data:{'onlySearch' : true}}});
			// Navigate({ url: path});
		}, 500);
		$(this).attr('data-search-timeout', timeout);
	});
}


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
					var multiMin   = answerBox.attr('data-multi-min');
					var multiMax   = answerBox.attr('data-multi-max');
					var multiSelectedLen = checkedAns.length;
					if(multiMin && multiSelectedLen < multiMin)
					{
						highlightIt($('.poll-container .poll-hint'));
						return 'min';
					}
					if(multiMax && multiSelectedLen > multiMax)
					{
						highlightIt($('.poll-container .poll-hint'));
						return 'max';
					}

					data.answer = {};
					if(multiSelectedLen)
					{
						checkedAns.each(function()
						{
							var myVal = $(this).val();
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
	// if answer isset then enable next
	if(data)
	{
		$('.poll-actions .next').removeClass('disabled')
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
				callFunction('redrawPollChart');
			},
			error: function(e, data, x)
			{
				console.log('error on saving answer!');
			}
		},
		// lockForm: false,
	});
}


/**
 * [highlightIt description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function highlightIt(_selector, _delay)
{
	if(!_delay)
	{
		_delay = 700;
	}
	// add highlight class
	_selector.addClass('isHighlight');
	var saveTimeout = _selector.attr('data-timeout-highlight');
	if(saveTimeout)
	{
		clearTimeout(saveTimeout);
	}
	var savingTimeout = setTimeout(function()
	{
		_selector.attr('data-timeout-highlight', null);
		_selector.removeClass('isHighlight');
	}, _delay);
	_selector.attr('data-timeout-highlight', savingTimeout);
}


/**
 * [uncheckRadio description]
 * @param  {[type]} _this [description]
 * @return {[type]}       [description]
 */
function uncheckRadio(_this)
{
	var radioEl = $(_this).parent().find('input');
	if($(radioEl).is(':checked') && $(radioEl).attr('disabled') === undefined)
	{
		$(radioEl).attr('checked', false);
	}
}


/**
 * [hashChanged description]
 * @return {[type]} [description]
 */
function hashChanged()
{
	$(window).bind( 'hashchange', function(e)
	{
		setLanguageURL();
		detectStep();
	});
}

function setCustomValidityMsg()
{
	$('input[data-validity]').off('invalid');
	$('input[data-validity]').on('invalid', function()
	{
		var ValidityText = $(this).attr('data-validity');
	    ValidityText = ValidityText.replace(':val', this.value);
	    // if has validity set custom message
		if(ValidityText)
		{
			this.setCustomValidity(ValidityText);
		}
		else
		{
			this.setCustomValidity("Dude '" + this.value + "' is not a valid. Enter something nice!!");
		}

	});
}


function inestimable()
{
	$(document).on('input', '#inestimable .field input', function()
	{
		var field = $(this).parents('.field');
		var len = $(this).val().length;
		if (len >= 7)
		{
			field.find('button').removeClass('disabled');
		}
		else
		{
			field.find('button').addClass('disabled');
		}
	});
}


function loading_page(_status)
{
	// console.log('page.......');
}


function loading_form(_status)
{
	// console.log('form.......');
}


/**
 * [handleSidebar description]
 * @return {[type]} [description]
 */
function handleSidebar()
{
	$('header #menu-bar').on('click', function()
	{
		openSidebar(true);
	});

	$('header').on('click', function(_e)
	{
		if($(_e.target).is($('header.open')))
		{
			openSidebar(false);
		}
	});

	$('header a').on('click', function(_e)
	{
		if($('header').hasClass('open'))
		{
			openSidebar(false);
		}
	});
}


/**
 * [openSidebar description]
 * @param  {[type]} _status [description]
 * @return {[type]}         [description]
 */
function openSidebar(_status)
{
	if(_status === false)
	{
		$('header').removeClass('open');
		$('header #menu-bar').fadeIn();
	}
	else
	{
		$('header').addClass('open');
		$('header #menu-bar').fadeOut();
	}
}


/**
 * [panelText description]
 * @return {[type]} [description]
 */
function panelText()
{
	$(document).on('input', 'textarea[data-limit]', function(e)
	{
		var id      = $(this).attr('id');
		var len     = $(this).val().length;
		var max     = $(this).attr('maxlength');
		var limitOf = $('[data-limit-of=' + id + ']');
		limitOf.text(max - len);
	});
}


/**
 * [welcomeToErmile description]
 * @return {[type]} [description]
 */
function welcomeToErmile()
{
	var resize =
	{
		calls: 0,
		total: 0,
		width: 0,
		height: 0,
		oldWidth: window.innerWidth,
		oldHeight: window.innerHeight,
		step:0,
	};

	// window.resize callback function
	function getDimensions()
	{
		resize.calls  += 1;
		resize.width  += Math.abs(resize.oldWidth - window.innerWidth);
		resize.height += Math.abs(resize.oldHeight - window.innerHeight);
		resize.total  = resize.width + resize.height;

		resize.oldWidth  = window.innerWidth;
		resize.oldHeight = window.innerHeight;
		if(resize.step === 0 && resize.calls > 10 && resize.total > 1000)
		{
			resize.step = 1;
			console.log('Hey Boy;)');
		}
		else if(resize.step === 1 && resize.calls > 20 && resize.total > 7000)
		{
			resize.step = 2;
			console.log('Common...');
		}
		else if(resize.step === 2 && resize.calls > 50 && resize.total > 1000)
		{
			resize.step = 3;
			console.log('Ermile is waiting for you baby))' );
		}
	}
	//lListen for window.resize
	window.addEventListener('resize', getDimensions);
	// call once to initialize page
	// getDimensions();
}


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
	disabledLink();
	// allow to set fav
	setProperty('favorite');
	setProperty('heart');
	// check if hash is changd, call something
	hashChanged();
	// load needed js file
	loadFiles();
	// inestimable
	inestimable();
	// panel text
	panelText();
	// handle sidebar
	handleSidebar();
	// handle resize of window
	welcomeToErmile();
}


/**
 * [loadFiles description]
 * @return {[type]} [description]
 */
function loadFiles()
{
	$import('lib/dataResponse/dataResponse.js', 'runDataResponse', null, 50);
	// $import('lib/contextmenu/contextmenu.js', 'runMyMenu', null, 50);
}