// on start
$(document).ready(function()
{
	// add event for click on go btn
	clickOnGo();
	changeUsername();
	verifyInput('username');
	verifyInput('code');
	verifyInput('pin');

	setTimeout(function()
	{
		$('#username').attr('readonly', null).focus();
	}, 70);

	// handle enter on username
	$('#username').on('keyup', function(_e)
	{
		if(_e.which === 13)
		{
			$('#go').click();
		}
	});

	$('.usernameBox.disabled input').on("dblclick", '*', function(_e)
	{
		console.log('unlock mobile!');
		gotoStep('mobile');
	});
});


/**
 * by click on go btn
 * @return {[type]} [description]
 */
function clickOnGo()
{
	$('#go').click(function(e)
	{
		// check mobile
		var myMobile         = $('#username');
		var myMobileVal      = myMobile.val();
		var invalidMobileMsg = myMobile.attr('data-invalid');
		if(myMobileVal)
		{
			if(validateUsername())
			{
				gotoStep('wait');
				setNotif();
				sendToBigBrother('mobile');
			}
			else
			{
				// show invalid mobile error for lenght
				setNotif(invalidMobileMsg);
			}
		}
		else
		{
			setNotif(invalidMobileMsg);
		}
	});

	$('.usernameBox:not(.disabled) #go2').click(function(e)
	{
		$('#go').click();
	});
}



function changeUsername()
{
	var myUsername = $('#username');

	$('.usernameBox label').on('click', function()
	{
		if(myUsername.attr('type') === 'tel')
		{
			if(!myUsername.data('placeholderDefault'))
			{
				myUsername.data('placeholderDefault', myUsername.attr('placeholder'));
			}
			myUsername.attr('type', 'text');
			myUsername.attr('placeholder', myUsername.attr('data-pl-user'));
			myUsername.val('');
			$(this).find('.icon-mobile2').removeClass('icon-mobile2').addClass('icon-star');
		}
		else
		{
			myUsername.attr('type', 'tel');
			myUsername.attr('placeholder', myUsername.data('placeholderDefault'));
			myUsername.val('');
			$(this).find('.icon-star').removeClass('icon-star').addClass('icon-mobile2');
		}
		changer('go');
	});
}


/**
 * [verifyCode description]
 * @return {[type]} [description]
 */
function verifyInput(_name)
{
	$('#' + _name).on('input', function()
	{
		// username
		switch(_name)
		{
			case 'code':
				if($(this).val().length === 6)
				{
					changer('code');
					sendToBigBrother(_name);
				}
				break;

			case 'pin':
				if($(this).val().length === 4)
				{
					changer('pin');
					sendToBigBrother(_name);
				}
				break;

			case 'username':
				validateUsername();
				break;
		}
	});
}


/**
 * [validateUsername description]
 * @return {[type]}      [description]
 */
function validateUsername()
{
	var userNameBox = $('.usernameBox');
	var userNameEl  = $('#username')
	var userVal     = userNameEl.val();
	var status      = false;

	if(userNameEl.attr('type') === 'tel')
	{
		if(validateMobile(userVal))
		{
			console.log(233);
			status = true;
			changer('go', true);
		}
		else
		{
			changer('go');
		}
	}
	else
	{
		if(userVal.length >= 2 && userVal.length <= 20)
		{
			status = true;
			changer('go', true);
		}
		else
		{
			changer('go');
		}
	}
	return status;
}


/**
 * [validateMobile description]
 * @param  {[type]} _number [description]
 * @return {[type]}         [description]
 */
function validateMobile(_number)
{
	// parse as integer to remove zero from start of number
	// _number = parseInt(_number);
	// convert to string for continue
	_number    = _number.toString();
	// define variables
	var result = true;
	var numLen = _number.length;
	// if len is true then check another filters
	if(numLen >= 7 && numLen <= 15)
	{
		// this is iranian number
		if(validateIranMobile(_number, true))
		{
			if($('html').attr('lang') === 'fa')
			{
				result = validateIranMobile(_number);
			}
			else
			{

			}
		}
	}
	else
	{
		result = false;
	}

	return result;
}


function validateIranMobile(_number, _onlyCheck)
{

	// var status = _number.match(/^((\+|00)?98|0)9[0-3](\d{0,8})$/);
	// var status = !!_number.match(/^((\+|00)?98|0)9[0-3](\d{0,8})$/);
	var status = null
	if(_onlyCheck === true)
	{
		status = !!_number.match(/^((\+|00)?98|0)?9[0-3](\d{0,15})$/);
	}
	else
	{
		status = !!_number.match(/^((\+|00)?98|0)?9[0-3](\d{8})$/);
	}


	console.log(status);
	return status;

	// var threeDigit = _number.substr(0, 3);
	// var result = true;
	// switch (threeDigit)
	// {
	// 	case '090':
	// 	case '091':
	// 	case '092':
	// 	case '093':
	// 		iranNumber = true;
	// 		break;
	// }
}


/**
 * change element status to new condition
 * @param  {[type]} _name [description]
 * @param  {[type]} _enable [description]
 * @return {[type]}       [description]
 */
function changer(_name, _enable, _delay)
{
	var el = $('#' + _name);
	var elField = el.parents('.' + _name + 'Box');

	switch (_name)
	{
		case 'username':
			if(_enable === undefined)
			{
				elField.addClass('disabled');
				el.attr('disabled', true);
			}
			else if(_enable === true)
			{
				elField.removeClass('disabled');
				el.attr('disabled', null).focus();
			}
			break;

		case 'password':
			break;

		case 'pin':
		case 'code':
			if(_enable === undefined)
			{
				// disable it
				el.attr('disabled', true);
				elField.addClass('disabled');
			}
			else if(_enable === true)
			{
				if(_delay === undefined)
				{
					_delay = 0;
				}
				setTimeout(function()
				{
					// enable it
					elField.fadeIn();
					elField.removeClass('disabled');
					$('.enter').addClass('large');
					el.val('').attr('disabled', null).focus();
				}, _delay);
			}
			else if(_enable === false)
			{
				// hide it
				elField.fadeOut(100);
				$('.enter').removeClass('large');
			}
			else if(_enable === null)
			{
				// hide and remove value of it
				el.val(null);
				elField.fadeOut(100);
				$('.enter').removeClass('large');
			}
			break;

		case 'go':
			if(_enable === undefined)
			{
				// disable it
				elField.fadeOut(100);
				el.attr('disabled', true);
				$('.enter').removeClass('large');
			}
			else if(_enable === true)
			{
				// enable it
				$('.enter').addClass('large');
				elField.fadeIn();
				el.attr('disabled', null);
			}
			else if(_enable === false)
			{

			}
			break;
	}
}


/**
 * [gotoWait description]
 * @return {[type]} [description]
 */
function gotoStep(_step, _delay)
{
	console.log(_step);
	switch(_step)
	{
		case 'mobile':
			$('.enter').removeClass('large');
			changer('username', true);
			changer('go');
			changer('code', null);
			changer('pin', null);
			break;

		case 'wait':
			changer('username');
			changer('go');
			changer('code');
			changer('pin');
			break;

		case 'pin':
			changer('username');
			changer('go');
			changer('code');
			changer('pin', true, _delay);
			break;

		case 'code':
			changer('username');
			changer('go');
			changer('code', true, _delay);
			changer('pin');
			break;
	}
}


/**
 * [getBlackBox description]
 * @return {[type]} [description]
 */
function getBlackBox()
{
	var blackBox      = {};
	blackBox.mobile   = $('#username').val();
	blackBox.password = $('#passcode').val();
	blackBox.pin      = $('#pin').val();
	blackBox.code     = $('#code').val();

	return blackBox;
}


/**
 * [sendToBigBrother description]
 * @param  {[type]} _step [description]
 * @return {[type]}       [description]
 */
function sendToBigBrother(_step)
{
	var blackBox      = getBlackBox();
	blackBox.step     = _step;


	$('.sidebox').ajaxify(
	{
		ajax:
		{
			data: blackBox,
			// abort: true,
			method: 'post',
			success: function(_data)
			{
				if(_data.status && _data.status === 0)
				{
					// error on change
					setNotif('ERROR on return !!!');
					gotoStep('mobile');
				}
				else
				{
					// set callback title into notif
					setNotif(_data.title);
					// give callback
					var callback = _data.msg;

					switch(callback.step)
					{
						case 'mobile':
						case 'pin':
						case 'code':
							gotoStep(callback.step, callback.wait);
							break;

						case 'block':
						default:
							// setNotif('WHAT HAPPEN !!');
							break;
					}

				}
			},
			error: function(e, data, x)
			{
				setNotif('ERROR on ajax !!!');
				gotoStep('mobile');
			}
		},
		lockForm: false,
	});
}



/**
 * [setNotif description]
 * @param {[type]} _txt [description]
 */
function setNotif(_txt)
{
	var notif = $('.notif');
	if(!_txt)
	{
		_txt = notif.attr('data-wait');
	}

	notif.animate({opacity:0},500,"linear",function()
	{
		notif.text(_txt);
		$(this).animate({opacity:1},500);
	});
}


function newTyped()
{
	console.log("22");
 /* A new typed object */
}

function foo()
{
	console.log("Callback");
}