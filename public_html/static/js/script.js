var TEMP = null;

// Range Slider

/* HTML5 Sortable jQuery Plugin -http://farhadi.ir/projects/html5sortable */
!function(t){var e,r=t();t.fn.sortable=function(a){var n=String(a);return a=t.extend({connectWith:!1},a),this.each(function(){if(/^(enable|disable|destroy)$/.test(n)){var i=t(this).children(t(this).data("items")).attr("draggable","enable"==n);return void("destroy"==n&&i.add(this).removeData("connectWith items").off("dragstart.h5s dragend.h5s selectstart.h5s dragover.h5s dragenter.h5s drop.h5s"))}var s,d,i=t(this).children(a.items),o=t("<"+(/^(ul|ol)$/i.test(this.tagName)?"li":"div")+' class="sortable-placeholder">');i.find(a.handle).mousedown(function(){s=!0}).mouseup(function(){s=!1}),t(this).data("items",a.items),r=r.add(o),a.connectWith&&t(a.connectWith).add(this).data("connectWith",a.connectWith),i.attr("draggable","true").on("dragstart.h5s",function(r){if(a.handle&&!s)return!1;s=!1;var n=r.originalEvent.dataTransfer;n.effectAllowed="move",n.setData("Text","dummy"),d=(e=t(this)).addClass("sortable-dragging").index()}).on("dragend.h5s",function(){e&&(e.removeClass("sortable-dragging").show(),r.detach(),d!=e.index()&&e.parent().trigger("sortupdate",{item:e}),e=null)}).not("a[href], img").on("selectstart.h5s",function(){return this.dragDrop&&this.dragDrop(),!1}).end().add([this,o]).on("dragover.h5s dragenter.h5s drop.h5s",function(n){return i.is(e)||a.connectWith===t(e).parent().data("connectWith")?"drop"==n.type?(n.stopPropagation(),r.filter(":visible").after(e),e.trigger("dragend.h5s"),!1):(n.preventDefault(),n.originalEvent.dataTransfer.dropEffect="move",i.is(this)?(a.forcePlaceholderSize&&o.height(e.outerHeight()),e.hide(),t(this)[o.index()<t(this).index()?"after":"before"](o),r.not(o).detach()):r.is(this)||t(this).children(a.items).length||(r.detach(),t(this).append(o)),!1):!0})})}}(jQuery);
// data-response library




/**
 * [$import description]
 * @param  {[type]} src [description]
 * @return {[type]}     [description]
 */
function $import(src)
{
	var scriptElem = document.createElement('script');
	scriptElem.setAttribute('src',src);
	scriptElem.setAttribute('type','text/javascript');
	document.getElementsByTagName('head')[0].appendChild(scriptElem);
}


/**
 * import with a random query parameter to avoid caching
 * @param  {[type]} src [description]
 * @return {[type]}     [description]
 */
function $importNoCache(src)
{
	var ms = new Date().getTime().toString();
	var seed = "?" + ms;
	$import(src + seed);
}


/**
 * allow textarea to be resizable
 * @return {[type]} [description]
 */
function resizableTextarea()
{
	$(document).on('keyup', 'textarea[data-resizable]', function()
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
				console.log("Need help?");
				e.preventDefault();
				introJs().start();
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
		$(_output).parents('li').find('.audio-module').html('');

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
			$(_output).parents('li').find('.audio-module').html(audioEl);
		}
		else if(f.type == 'video/mp4')
		{
			fileType = 'video';
			// show image of audio
			// fileUrl = '/static/images/file/video.svg';
			// // add element of audio
			// var audioEl = '<video controls><source src="'+ window.URL.createObjectURL(f)+ '" type="video/mp4"></audio>';
			// $(_output).parents('li').find('.audio-module').html(audioEl);
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
	var num      = $('.input-group.sortable>li').length + 1;
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
	// set file
	template.find('.element .file input').attr('name', 'file' + num);
	template.find('.element .file input').attr('id', 'file' + num);
	template.find('.element .file input').val('');
	template.find('.element .file label').attr('for', 'file' + num);
	template.find('.element .file img').remove();


	// set score
	template.find('.element .score input').attr('name', 'score' + num);
	template.find('.element .score input').attr('id', 'score' + num);
	template.find('.element .score input').val('');
	// template.find('.element').attr('data-row', num);

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
		$('.sortable').sortable({items: ':not(.fix)'},{handle: '.title'}).bind('sortupdate', function(e, ui)
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
		$(this).find('.element').attr('data-row', row);
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
	changeStep(sthis);
}


/**
 * [changeStep description]
 * @param  {[type]} _name [description]
 * @return {[type]}       [description]
 */
function changeStep(_name)
{
	switch(_name)
	{
		default:
		case 'step-add':
		case 'step1':
			$('.stepAdd').slideDown();
			$('.stepFilter').slideUp();
			$('.stepPublish').slideUp();
			window.location.hash = 'step1';
			break;

		case 'step-filter':
		case 'step2':
			$('.stepAdd').slideUp();
			$('.stepFilter').slideDown();
			$('.stepPublish').slideUp();
			window.location.hash = 'step2';
			break;

		case 'step-publish':
		case 'step3':
			$('.stepAdd').slideUp();
			$('.stepFilter').slideUp();
			$('.stepPublish').slideDown();
			window.location.hash = 'step3';
			break;
	}

	// window.location.hash = _name;
	_name = _name.substr(5);
	$('.page-progress').attr('data-current', _name);
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

	setSortable();
	detectPercentage();

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

	// on keyup and press shift+del remove current record
	$(this).on('keyup', '.input-group.sortable li input', function(e)
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

	$(this).on('change', '#title', function()
	{
		detectPercentage();
	});

	// on press delete on each opt
	$(this).on('click', '.input-group.sortable li .delete', function()
	{
		deleteQuestionOpts(this);
	}).on('keyup', '.input-group.sortable li .delete', function(e)
	{
		if((e.shiftKey && e.keyCode === 46) || e.keyCode === 13)
		{
			$(this).closest('li').find('.delete').click();
		}
	});

	// --------------------------------------------------------------------------------- Tree
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


	$(this).on('click','button', function()
	{
		detectPercentage(true);
		$('#submit-form').attr("value", $(this).attr("send-name"));
		$('#submit-form').attr("name", $(this).attr("send-name"));
	});
}).once(function()
{
	simulateTreeNavigation();

	$('.page-progress input').on('click', function(e)
	{
		detectStep($(this).attr('name'));
		// e.stopPropagation();
		// return false;
	});
	// on init
	detectStep();


	// on open tree load content to it
	$(window).off("response:open");
	$(window).on("response:open", function(_obj, _name, _value)
	{
		// if open tree then fill with last qustions
		if(_name == 'tree' && _value == 'open')
		{
			treeSearch.call(null, null, true);
		}
	});


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
		var output = $(this).parents('li').find('.preview');
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
	$(this).bind('range-slider::change', '#rangepersons', function(_e, _min, _max)
	{
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
	setTimeout(function()
	{
		$import('/static/js/data-response.js');
		$import('/static/js/rangeSlider.js');
		$import('/static/js/introJs.js');
	}, 200);

}

