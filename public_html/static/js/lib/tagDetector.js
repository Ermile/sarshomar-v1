/**
 * v1.1
 */

/**
 * [runTagDetector description]
 * @return {[type]} [description]
 */
function runTagDetector()
{
	// handle enter
	$(document).on('keypress', '.tagDetector .tagInput', function(e)
	{
		// if Enter pressed disallow it and run add func
		if (e.which == 13)
		{
			addNewTags(this);
			return false;
		}
	});
	// handle click on btn
	$(document).on('click', '.tagDetector .tagAdd' , function ()
	{
		addNewTags(this);
		$(this).closest('.tagDetector').find('.tagInput').focus();
		return false;
	});
	// on click on existing tag, show it for edit
	$(document).on('click', '.tagDetector .tagBox span' , function ()
	{
		// get value of clicked tag
		var $this      = $(this);
		var clickedTag = $this.text().trim();
		var myDetector = $this.closest('.tagDetector');
		var attrData   = getTagLists(myDetector);
		// remove from array of data
		attrData.splice(attrData.indexOf(clickedTag), 1);
		// set taglist
		setTagList(myDetector, attrData);
		// fill text in input and set focus
		myDetector.find('.tagInput').val(clickedTag).focus();
		// remove element
		$this.remove();
	});
}


/**
 * [addNewTags description]
 * @param {[type]} _elChilds [description]
 */
function addNewTags(_elChilds)
{
	// detect parent tag detector
	var myDetector = $(_elChilds).closest('.tagDetector');

	// if tag is not finded return false
	if(myDetector.length != 1)
	{
		return null;
	}
	// get detector main elements
	var elInput = myDetector.find('.tagInput');
	var elBox   = myDetector.find('.tagBox');
	// get bind vals
	var attrBindInput     = myDetector.attr('data-bind-input');
	var attrBindBox       = myDetector.attr('data-bind-box');
	var attrBindBoxFormat = myDetector.attr('data-box-format');
	var attrLimit         = parseInt(myDetector.attr('data-limit'));
	var attrData          = getTagLists(myDetector);

	if(attrData.length >= attrLimit)
	{
		return 'reach limit';
	}

	// if wanna bind box to specefic content, set it
	if(attrBindInput)
	{
		elInput = myDetector.find(attrBindInput);
	}
	// if wanna bind box to specefic content, set it
	if(attrBindBox)
	{
		elBox = myDetector.find(attrBindBox);
	}
	// get value of tag input
	var valInput = elInput.val().trim();
	// empty value of tag
	elInput.val('');
	// if isnot set return false
	if(!valInput)
	{
		return false;
	}
	// if box format isnt setm use default format
	if(!attrBindBoxFormat)
	{
		attrBindBoxFormat = "<span>:tag</span>";
	}
	// if exist in old list
	if(attrData.indexOf(valInput) >= 0)
	{
		// get element of exist tag
		var elTagExist = elBox.find('[data-val=' + valInput + ']');
		elTagExist.addClass("isExist");
		setTimeout(function () { elTagExist.removeClass("isExist") }, 500);
	}
	else
	{
		// replace :tag with real value
		var elNewTag = attrBindBoxFormat.replace(':tag', valInput);
		// add data-val for detecting for add on duplicate
		elNewTag     = $(elNewTag).attr('data-val', valInput);
		// append to boxes
		elBox.append(elNewTag);
		// append to array of tags
		attrData.push(valInput);
		// set tagList
		setTagList(myDetector, attrData);
	}
}

/**
 * [getTagLists description]
 * @param  {[type]} _detector [description]
 * @return {[type]}           [description]
 */
function getTagLists(_detector)
{
	var attrData = _detector.attr('data-val');
	// set data until now
	if(attrData)
	{
		attrData = JSON.parse(attrData);
	}
	else
	{
		attrData = [];
	}

	return attrData;
}


/**
 * [setTagList description]
 * @param {[type]} _detector [description]
 * @param {[type]} _data     [description]
 */
function setTagList(_detector, _data)
{
	var elVals      = _detector.find('.tagVals');
	var attrBindVal = _detector.attr('data-bind-val');
	// if wanna bind vals to specefic content, set it
	if(attrBindVal)
	{
		elVals = _detector.find(attrBindVal);
	}
	// add to textarea of elements
	elVals.val(_data.join());
	// add vals to detector as json
	_detector.attr('data-val', JSON.stringify(_data));
}