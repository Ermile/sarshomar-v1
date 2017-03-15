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
	$('#usermobile').attr('disabled', '');

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

$(document).ready(function()
{
	$('#usermobile').on('keyup', function(_e)
	{
		if(_e.which === 13)
		{
			$('#go').click();
		}
	});

	$('#go').click(function(e)
	{
		// check for mobile
		var myMobile = $('#usermobile').val();
		if(myMobile.length >= 7 && myMobile.length <=15)
		{
			wait(1);
			sendToBigBrother(1);
		}
		else
		{
			// show invalid mobile error
			// for lenght
		}
	});

	$('#code').on('input', function()
	{
		if ($(this).val().length === 4)
		{
			$(this).attr('disabled', '');
			verify();
		}
	});
});


/**
 * [sendToBigBrother description]
 * @param  {[type]} _step [description]
 * @return {[type]}       [description]
 */
function sendToBigBrother(_step)
{
	var blackBox      = {};
	blackBox.mobile   = $('#usermobile').val();
	blackBox.password = $('#password').val();
	blackBox.pin      = $('#pin').val();
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
		// lockForm: false,
	});
}