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
	console.log('step verify');
}

$(document).ready(function()
{
	$('#go').click(function(e)
	{
		wait(1);
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



// function show_error(status)
// {
// 	if (status === undefined)
// 	{
// 		status = 'wrong_code';
// 	}
// 	switch(status)
// 	{
// 		case 'wrong_code':
// 			$('.notif.correct').hide().removeClass('hidden').fadeIn(300);
// 			break;
// 		case 'short_block':
// 			break;
// 		case 'long_block':
// 			break;
// 		default:
// 			break;
// 	}
// }


// function step_code()
// {
// 	$('.notif.patient').fadeOut(300, function(e)
// 	{
// 		$(this).addClass('hidden');
// 		$('.field.code').hide().removeClass('hidden').fadeIn(300, function(e)
// 		{
// 			setTimeout(function()
// 			 {
// 			$('.other-ways').hide().removeClass('hidden').fadeIn(300);
// 			}, 3000);
// 		});
// 	});
// }


// function step_wait(time)
// {
// 	// initial validation
// 	if (time === undefined)
// 	{
// 		time = 1000;
// 	}
// 	else
// 	{
// 		time *= 1000;
// 	}

// 	// disable mobile field
// 	$('#mobile').attr('disabled', '');

// 	// hide button and show patient notif
// 	$('button').fadeOut(300, function(e) {
// 	$('.notif.patient').hide().removeClass('hidden').fadeIn(300, function()
// 	{
// 		setTimeout(function()
// 		{
// 		step_code();
// 		}, time);
// 	});
// 	});
// }


// $(document).on('click', 'button', function(e)
// {
// 	step_wait(1);
// });


// $(document).on('input', '#code', function(e)
// {
// 	var len = $(this).val().length;
// 	if (len === 4)
// 	{
// 		$('#code').attr('disabled', '');
// 		setTimeout(function()
// 		{
// 			show_error('wrong_code');
// 		}, 1000);
// 	}
// });
