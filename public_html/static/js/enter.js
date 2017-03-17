// Typed.js | Copyright (c) 2016 Matt Boldt | www.mattboldt.com
!function(t,s,e){"use strict";var i=function(t,s){var i=this;this.el=t,this.options={},Object.keys(r).forEach(function(t){i.options[t]=r[t]}),Object.keys(s).forEach(function(t){i.options[t]=s[t]}),this.isInput="input"===this.el.tagName.toLowerCase(),this.attr=this.options.attr,this.showCursor=!this.isInput&&this.options.showCursor,this.elContent=this.attr?this.el.getAttribute(this.attr):this.el.textContent,this.contentType=this.options.contentType,this.typeSpeed=this.options.typeSpeed,this.startDelay=this.options.startDelay,this.backSpeed=this.options.backSpeed,this.backDelay=this.options.backDelay,e&&this.options.stringsElement instanceof e?this.stringsElement=this.options.stringsElement[0]:this.stringsElement=this.options.stringsElement,this.strings=this.options.strings,this.strPos=0,this.arrayPos=0,this.stopNum=0,this.loop=this.options.loop,this.loopCount=this.options.loopCount,this.curLoop=0,this.stop=!1,this.cursorChar=this.options.cursorChar,this.shuffle=this.options.shuffle,this.sequence=[],this.build()};i.prototype={constructor:i,init:function(){var t=this;t.timeout=setTimeout(function(){for(var s=0;s<t.strings.length;++s)t.sequence[s]=s;t.shuffle&&(t.sequence=t.shuffleArray(t.sequence)),t.typewrite(t.strings[t.sequence[t.arrayPos]],t.strPos)},t.startDelay)},build:function(){var t=this;if(this.showCursor===!0&&(this.cursor=s.createElement("span"),this.cursor.className="typed-cursor",this.cursor.innerHTML=this.cursorChar,this.el.parentNode&&this.el.parentNode.insertBefore(this.cursor,this.el.nextSibling)),this.stringsElement){this.strings=[],this.stringsElement.style.display="none";var e=Array.prototype.slice.apply(this.stringsElement.children);e.forEach(function(s){t.strings.push(s.innerHTML)})}this.init()},typewrite:function(t,s){if(this.stop!==!0){var e=Math.round(70*Math.random())+this.typeSpeed,i=this;i.timeout=setTimeout(function(){var e=0,r=t.substr(s);if("^"===r.charAt(0)){var o=1;/^\^\d+/.test(r)&&(r=/\d+/.exec(r)[0],o+=r.length,e=parseInt(r)),t=t.substring(0,s)+t.substring(s+o)}if("html"===i.contentType){var n=t.substr(s).charAt(0);if("<"===n||"&"===n){var a="",h="";for(h="<"===n?">":";";t.substr(s+1).charAt(0)!==h&&(a+=t.substr(s).charAt(0),s++,!(s+1>t.length)););s++,a+=h}}i.timeout=setTimeout(function(){if(s===t.length){if(i.options.onStringTyped(i.arrayPos),i.arrayPos===i.strings.length-1&&(i.options.callback(),i.curLoop++,i.loop===!1||i.curLoop===i.loopCount))return;i.timeout=setTimeout(function(){i.backspace(t,s)},i.backDelay)}else{0===s&&i.options.preStringTyped(i.arrayPos);var e=t.substr(0,s+1);i.attr?i.el.setAttribute(i.attr,e):i.isInput?i.el.value=e:"html"===i.contentType?i.el.innerHTML=e:i.el.textContent=e,s++,i.typewrite(t,s)}},e)},e)}},backspace:function(t,s){if(this.stop!==!0){var e=Math.round(70*Math.random())+this.backSpeed,i=this;i.timeout=setTimeout(function(){if("html"===i.contentType&&">"===t.substr(s).charAt(0)){for(var e="";"<"!==t.substr(s-1).charAt(0)&&(e-=t.substr(s).charAt(0),s--,!(s<0)););s--,e+="<"}var r=t.substr(0,s);i.attr?i.el.setAttribute(i.attr,r):i.isInput?i.el.value=r:"html"===i.contentType?i.el.innerHTML=r:i.el.textContent=r,s>i.stopNum?(s--,i.backspace(t,s)):s<=i.stopNum&&(i.arrayPos++,i.arrayPos===i.strings.length?(i.arrayPos=0,i.shuffle&&(i.sequence=i.shuffleArray(i.sequence)),i.init()):i.typewrite(i.strings[i.sequence[i.arrayPos]],s))},e)}},shuffleArray:function(t){var s,e,i=t.length;if(i)for(;--i;)e=Math.floor(Math.random()*(i+1)),s=t[e],t[e]=t[i],t[i]=s;return t},reset:function(){var t=this;clearInterval(t.timeout);this.el.getAttribute("id");this.el.textContent="","undefined"!=typeof this.cursor&&"undefined"!=typeof this.cursor.parentNode&&this.cursor.parentNode.removeChild(this.cursor),this.strPos=0,this.arrayPos=0,this.curLoop=0,this.options.resetCallback()}},i["new"]=function(t,e){var r=Array.prototype.slice.apply(s.querySelectorAll(t));r.forEach(function(t){var s=t._typed,r="object"==typeof e&&e;s&&s.reset(),t._typed=s=new i(t,r),"string"==typeof e&&s[e]()})},e&&(e.fn.typed=function(t){return this.each(function(){var s=e(this),r=s.data("typed"),o="object"==typeof t&&t;r&&r.reset(),s.data("typed",r=new i(this,o)),"string"==typeof t&&r[t]()})}),t.Typed=i;var r={strings:["These are the default values...","You know what you should do?","Use your own!","Have a great day!"],stringsElement:null,typeSpeed:0,startDelay:0,backSpeed:0,shuffle:!1,backDelay:500,loop:!1,loopCount:!1,showCursor:!0,cursorChar:"|",attr:null,contentType:"html",callback:function(){},preStringTyped:function(){},onStringTyped:function(){},resetCallback:function(){}}}(window,document,window.jQuery);

// on start
$(document).ready(function()
{
	// add event for click on go btn
	clickOnGo();

	setTimeout(function()
	{
		$('#username').attr('readonly', null).focus();
	}, 50);

	$('#username').on('keyup', function(_e)
	{
		if(_e.which === 13)
		{
			$('#go').click();
		}
	});

	// $('#code').on('input', function()
	// {
	// 	if ($(this).val().length === 5)
	// 	{
	// 		$(this).attr('disabled', '');
	// 		verify();
	// 	}
	// });
});



function changer(_name, _cond)
{
	var el = $('#' + _name);


	// if(!_cond)
	// {
	// 	if(_name === 'mobile')
	// 	{
	// 		_cond = 'disabled';
	// 	}
	// 	else
	// 	{

	// 	}
	// 	_cond = 'hide';
	// }


	switch (_name)
	{
		case 'username':
			if(!_cond)
			{
				el.attr('disabled', true);
			}
			break;

		case 'password':
			break;

		case 'pin':
			break;

		case 'code':
			break;
	}



	// switch (_cond)
	// {
	// 	case 'disabled':
	// 		$(_name)
	// 		break;
	// }
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
			success: function(e, data, x)
			{
				if(e.status && e.status != 0)
				{
					console.log(e);
					console.log(data);
					console.log(x);

					var newStatus = '';
					if(e.msg && e.msg.new_status)
					{
						newStatus = e.msg.new_status;
					}
					// set new status and change all elements depending it change
					// setStatusText(newStatus);
					// if we dont have any error


					switch(e.status)
					{
						case 0:
							console.log('go back to step start');
							break;

						case 1:
							console.log('successfully change status!');
							break;

						case 2:
							break;
					}
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
		lockForm: false,
	});
}


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
		if (myMobileVal)
		{
			if (myMobileVal.length >= 7 && myMobileVal.length <= 15)
			{
				changer('username');
				stepWait(1);
				sendToBigBrother(1);
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
		typeSpeed: 10,
		backDelay: 500,
		cursor:"@",
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


/**
 * [stepWait description]
 * @return {[type]} [description]
 */
function stepWait()
{
	setNotif();
}




function start()
{

}

function wait(_duration)
{
	// check if _duration exists
	if (_duration === undefined)
	{
		_duration = 1000;
	}
	else
	{
		_duration *= 1000;
	}

	// lock usermobile field
	$('#username').attr('disabled', '');

	// show waiting message until _duration done
	$('#go').fadeOut(300, function()
	{
		$('.sidebox .waiting').hide().removeClass('hide').fadeIn(300, function()
		{
			setTimeout(function()
			{
				// hide waiting box
				$('.sidebox .waiting').fadeOut(300, function()
				{
					$(this).addClass('hide');

					// show code field
					$('.field.code').hide().removeClass('hide').fadeIn(300, function()
					{});
				});
			}, _duration);
		});
	});
}

function verify()
{
	sendToBigBrother(2);
	console.log('step verify');
}




