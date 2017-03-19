// Typed.js | Copyright (c) 2016 Matt Boldt | www.mattboldt.com
!function(t,s,e){"use strict";var i=function(t,s){var i=this;this.el=t,this.options={},Object.keys(r).forEach(function(t){i.options[t]=r[t]}),Object.keys(s).forEach(function(t){i.options[t]=s[t]}),this.isInput="input"===this.el.tagName.toLowerCase(),this.attr=this.options.attr,this.showCursor=!this.isInput&&this.options.showCursor,this.elContent=this.attr?this.el.getAttribute(this.attr):this.el.textContent,this.contentType=this.options.contentType,this.typeSpeed=this.options.typeSpeed,this.startDelay=this.options.startDelay,this.backSpeed=this.options.backSpeed,this.backDelay=this.options.backDelay,e&&this.options.stringsElement instanceof e?this.stringsElement=this.options.stringsElement[0]:this.stringsElement=this.options.stringsElement,this.strings=this.options.strings,this.strPos=0,this.arrayPos=0,this.stopNum=0,this.loop=this.options.loop,this.loopCount=this.options.loopCount,this.curLoop=0,this.stop=!1,this.cursorChar=this.options.cursorChar,this.shuffle=this.options.shuffle,this.sequence=[],this.build()};i.prototype={constructor:i,init:function(){var t=this;t.timeout=setTimeout(function(){for(var s=0;s<t.strings.length;++s)t.sequence[s]=s;t.shuffle&&(t.sequence=t.shuffleArray(t.sequence)),t.typewrite(t.strings[t.sequence[t.arrayPos]],t.strPos)},t.startDelay)},build:function(){var t=this;if(this.showCursor===!0&&(this.cursor=s.createElement("span"),this.cursor.className="typed-cursor",this.cursor.innerHTML=this.cursorChar,this.el.parentNode&&this.el.parentNode.insertBefore(this.cursor,this.el.nextSibling)),this.stringsElement){this.strings=[],this.stringsElement.style.display="none";var e=Array.prototype.slice.apply(this.stringsElement.children);e.forEach(function(s){t.strings.push(s.innerHTML)})}this.init()},typewrite:function(t,s){if(this.stop!==!0){var e=Math.round(70*Math.random())+this.typeSpeed,i=this;i.timeout=setTimeout(function(){var e=0,r=t.substr(s);if("^"===r.charAt(0)){var o=1;/^\^\d+/.test(r)&&(r=/\d+/.exec(r)[0],o+=r.length,e=parseInt(r)),t=t.substring(0,s)+t.substring(s+o)}if("html"===i.contentType){var n=t.substr(s).charAt(0);if("<"===n||"&"===n){var a="",h="";for(h="<"===n?">":";";t.substr(s+1).charAt(0)!==h&&(a+=t.substr(s).charAt(0),s++,!(s+1>t.length)););s++,a+=h}}i.timeout=setTimeout(function(){if(s===t.length){if(i.options.onStringTyped(i.arrayPos),i.arrayPos===i.strings.length-1&&(i.options.callback(),i.curLoop++,i.loop===!1||i.curLoop===i.loopCount))return;i.timeout=setTimeout(function(){i.backspace(t,s)},i.backDelay)}else{0===s&&i.options.preStringTyped(i.arrayPos);var e=t.substr(0,s+1);i.attr?i.el.setAttribute(i.attr,e):i.isInput?i.el.value=e:"html"===i.contentType?i.el.innerHTML=e:i.el.textContent=e,s++,i.typewrite(t,s)}},e)},e)}},backspace:function(t,s){if(this.stop!==!0){var e=Math.round(70*Math.random())+this.backSpeed,i=this;i.timeout=setTimeout(function(){if("html"===i.contentType&&">"===t.substr(s).charAt(0)){for(var e="";"<"!==t.substr(s-1).charAt(0)&&(e-=t.substr(s).charAt(0),s--,!(s<0)););s--,e+="<"}var r=t.substr(0,s);i.attr?i.el.setAttribute(i.attr,r):i.isInput?i.el.value=r:"html"===i.contentType?i.el.innerHTML=r:i.el.textContent=r,s>i.stopNum?(s--,i.backspace(t,s)):s<=i.stopNum&&(i.arrayPos++,i.arrayPos===i.strings.length?(i.arrayPos=0,i.shuffle&&(i.sequence=i.shuffleArray(i.sequence)),i.init()):i.typewrite(i.strings[i.sequence[i.arrayPos]],s))},e)}},shuffleArray:function(t){var s,e,i=t.length;if(i)for(;--i;)e=Math.floor(Math.random()*(i+1)),s=t[e],t[e]=t[i],t[i]=s;return t},reset:function(){var t=this;clearInterval(t.timeout);this.el.getAttribute("id");this.el.textContent="","undefined"!=typeof this.cursor&&"undefined"!=typeof this.cursor.parentNode&&this.cursor.parentNode.removeChild(this.cursor),this.strPos=0,this.arrayPos=0,this.curLoop=0,this.options.resetCallback()}},i["new"]=function(t,e){var r=Array.prototype.slice.apply(s.querySelectorAll(t));r.forEach(function(t){var s=t._typed,r="object"==typeof e&&e;s&&s.reset(),t._typed=s=new i(t,r),"string"==typeof e&&s[e]()})},e&&(e.fn.typed=function(t){return this.each(function(){var s=e(this),r=s.data("typed"),o="object"==typeof t&&t;r&&r.reset(),s.data("typed",r=new i(this,o)),"string"==typeof t&&r[t]()})}),t.Typed=i;var r={strings:["These are the default values...","You know what you should do?","Use your own!","Have a great day!"],stringsElement:null,typeSpeed:0,startDelay:0,backSpeed:0,shuffle:!1,backDelay:500,loop:!1,loopCount:!1,showCursor:!0,cursorChar:"|",attr:null,contentType:"html",callback:function(){},preStringTyped:function(){},onStringTyped:function(){},resetCallback:function(){}}}(window,document,window.jQuery);


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

	// $('#username').on('dblclick', function(_e)
	// {
	// 	gotoStep('mobile');
	// });
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
			if(myMobileVal.length >= 7 && myMobileVal.length <= 15)
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
		}
		else
		{
			myUsername.attr('type', 'tel');
			myUsername.attr('placeholder', myUsername.data('placeholderDefault'));
			myUsername.val('');
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
					$(this).attr('disabled', true);
					sendToBigBrother(_name);
				}
				break;

			case 'pin':
				if($(this).val().length === 4)
				{
					$(this).attr('disabled', true);
					sendToBigBrother(_name);
				}
				break;

			case 'username':
				var userNameBox = $(this).parents('.usernameBox');

				if($(this).val().length >= 7 && $(this).val().length <= 15)
				{
					userNameBox.addClass('disabled');
					$(this).parents('.enter').find('.goBox').fadeIn();
					// userNameBox.find('label .icon-mobile2').fadeOut(300, function()
					// {
					// 	userNameBox.find('label').addClass('active');
					// 	userNameBox.find('label .icon-range-right').fadeIn(300);
					// });
				}
				else
				{
					userNameBox.removeClass('disabled');
					$(this).parents('.enter').find('.goBox').fadeOut();
					// userNameBox.find('label .icon-range-right').fadeOut(300,function()
					// {
					// 	userNameBox.find('label').removeClass('active');
					// 	userNameBox.find('label .icon-mobile2').fadeIn(300);
					// });
				}
				break;
		}
	});
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
				el.attr('disabled', true);
			}
			else if(_enable === true)
			{
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
					el.attr('disabled', null).focus();
				}, _delay);
			}
			else if(_enable === false)
			{
				// hide it
				elField.fadeOut();
			}
			else if(_enable === null)
			{
				// hide and remove value of it
				el.val(null);
				elField.fadeOut();
			}
			break;

		case 'go':
			if(_enable === undefined)
			{
				// disable it
				elField.fadeOut();
				el.attr('disabled', true);
			}
			else if(_enable === true)
			{
				// enable it
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
	switch(_step)
	{
		case 'mobile':
			changer('username', true);
			changer('go', true);
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
							setNotif('WHAT HAPPEN !!');
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

	Typed.new(".notif",
	{
		strings: [_txt],
		typeSpeed: 30,
		// backDelay: 500,
		// cursor:"@",
		callback: function(){ foo(); },
		resetCallback: function() { newTyped(); }
	});

	notif.text(_txt);
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