/**
 * v1.0
 */

/**
 * [runTagDetector description]
 * @return {[type]} [description]
 */
function runTagDetector()
{

	$('.tagDetector .tagInput').on('keypress', function(e)
	{
		// if Enter pressed disallow it and run add func
		if (e.which == 13)
		{
			addNewTags(this);

			// var element_id = $(this).attr('id');
			// addTag(element_id);
			return false;
		}
	});

	$('.tagDetector .tagVals').on('change', function()
	{
		console.log('valChanged!');

		// if Enter pressed disallow it and run add func
		// var element_id = $(this).attr('id');
		// addTag(element_id);
		return false;
	});

	$(document).on('click', '.tagDetector .tagAdd' , function ()
	{
		addTag(this);
		return false;
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
	var elVals  = myDetector.find('.tagVals');
	// get bind vals
	var attrBindInput     = myDetector.attr('data-bind-input');
	var attrBindBox       = myDetector.attr('data-bind-box');
	var attrBindBoxFormat = myDetector.attr('data-box-format');
	var attrBindVal       = myDetector.attr('data-bind-val');
	var attrData          = myDetector.attr('data-val');
	// set data until now
	if(attrData)
	{
		attrData = JSON.parse(attrData);
	}
	else
	{
		attrData = [];
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
	// if wanna bind vals to specefic content, set it
	if(attrBindVal)
	{
		elVals = myDetector.find(attrBindVal);
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
		// add to textarea of elements
		elVals.val(attrData.join());
		// add vals to detector as json
		myDetector.attr('data-val', JSON.stringify(attrData));
	}
}
