(function($)
{
	"use strict";
	/**
	 * [destroy description]
	 * @return {[type]} [description]
	 * if call this 
	 */
	function destroy()
	{
		$(this).removeClass('range-slider');
		$(this).data('range-slider', undefined);
		$(this).init.prototype.range = undefined;
		$('*', this).remove();
	}

	/**
	 * [restart description]
	 * @return {[type]} [if you call this function it will restart all the range sliders in your page]
	 * [all of your intered settings and changes will be restart and will be dissmissed]
	 * [range sliders will start by html data-values that you had set]
	 */
	function restart()
	{
		$(this).rangeSlider('destroy');
		$(this).rangeSlider();
	}

	/**
	 * [optionMethod description]
	 * @type {[object]}
	 * [all of methods are defined in this class]
	 */
	var optionMethod = Object();

	/**
	 * [type description]
	 * @param  {[type]} _name [name of the method]
	 * @param  {[type]} _set  [type that we want to set it for this method, vertical or horizontal can be set]
	 * @return {[type]}       [vertical ,horizontal]
	 *                        [if _set has value, it will be set and new value will return ]
	 *                        [but if _set value was null, type of range slider will be returned]
	 */
	optionMethod.type = function(_name, _set)
	{
		var data_type = $(this).data('data-type');

		if (!isNaN(_set))
		{
			var data_type = _set;
			if (data_type != 'vertical')
			{
				data_type = 'horizontal';
			}
			$(this).data('data-type',data_type);
			return data_type;
		}

		var data_type = $(this).data('data-type');
		if (data_type != 'vertical')
		{
			data_type = 'horizontal';
			$(this).attr('data-type',data_type);
			$(this).data('data-type',data_type);
		}
		return data_type;
	}

	/**
	 * [step description]
	 * @param  {[type]} _name [name of method]
	 * @param  {[type]} _set  [value that we want to set it for this method]
	 * @param  {[type]} _json [if you set multi step for range slider automatically your intered json will be send to method in _json]
	 * @return {[type]}       [step value]
	 *                        [positive mounts and integers only can be set, also a json ]
	 *                        [also a json can be set on data-step [{"start": 40, "end": 100, "step": 10}] ]
	 */
	optionMethod.step = function(_name, _set, _json)
	{
		var result;
		var data_step;
		var my_step;
		if (!isNaN(_set))
		{
			my_step = Number(_set);
			if (isNaN(my_step))
			{
				my_step = 1;
			}
			$(this).data('data-step', my_step);
			return my_step;
		}

		var data = $(this).data("data-step");
		try {
	    my_step = $.parseJSON(data);

		} catch (e) {
			my_step = Number(data);
			if (isNaN(my_step))
			{
				my_step = 1;
			}
			$(this).attr('data-step', my_step);
			$(this).data('data-step', my_step);
		}

		if (Array.isArray(my_step))
		{
			var jason_step = data;
			$(this).data("save_jason", jason_step);
			var my_step = 0;
			if ($(this).data("data-step"))
			{
				var ends = [];
				var multi_steps = JSON.parse($(this).data("data-step"));
				var json_steps = multi_steps;
				for (var i = 0; i < multi_steps.length; i++)
				{
					var multi_steps_details = multi_steps[i];
					var start = parseInt(multi_steps_details["start"]);
					var end   = parseInt(multi_steps_details["end"]);
					ends.push(end);

					var max = $(this).rangeSlider('option','max')

					if (max <= end && max>start)
					{
						end = max;
						var step  = parseInt(multi_steps_details["step"]);
						my_step  += ((end - start) / step);
						break
					}

					var step  = parseInt(multi_steps_details["step"]);
					my_step  += ((end - start) / step);
				}


				var _unit = $(this).rangeSlider('option', 'unit');
				$(this).attr('data-step', (_unit/my_step));
				$(this).data('data-step', (_unit/my_step));
			}
		}
		return Math.round(my_step);
	}

	/**
	 * [min range slider will start from this number]
	 * @param  {[type]} _name [name of method]
	 * @param  {[type]} _set  [value that we want to set it for min method]
	 * @return {[type]}       [min value]
	 *                        [if _set is null then method will return min value]
	 *                        [if _set had value, min will set on this new value]
	 */
	optionMethod.min = function(_name, _set)
	{
		if (!isNaN(_set))
		{
			var data_min = _set;
			if ($(this).rangeSlider('option', 'check_json'))
			{
				var json_string = $(this).data("save_jason");
				var json_steps = jQuery.parseJSON( json_string );
				var json_steps_details = json_steps[0];
				var start  = parseInt(json_steps_details["start"]);
				data_min = start;
				$(this).attr('data-min', data_min)
				$(this).data('data-min', data_min)
			}
			
			if (isNaN(data_min))
			{
				data_min = 0;
			}

			$(this).attr('data-min',data_min);
			$(this).data('data-min',data_min);
		return data_min;
		}

		var data_min = parseInt($(this).data('data-min'));
		if ($(this).rangeSlider('option', 'check_json'))
		{
			$(this).attr('data-min',data_min);
			$(this).data('data-min',data_min);
			var data_min_unit = $(this).rangeSlider('option', 'multi_level_value_to_real', data_min);
			var my_step = $(this).rangeSlider('option','step');
			data_min = Math.round(my_step * data_min_unit);
		}

		if (isNaN(data_min))
		{
			data_min = 0;
			$(this).attr('data-min',data_min);
			$(this).data('data-min',data_min);
		}
		return data_min;
	}

	/**
	 * [max range slider will set the max mount]
	 * @param  {[type]} _name [name of method]
	 * @param  {[type]} _set  [value that we want to set it for max method]
	 * @return {[type]}       [max value]
	 *                        [if _set is null then method will return max value]
	 *                        [if _set had value, min will set on this new value]
	 *                        [if in html dosen't set dta-max attribute, default max value is data-min+100]
	 */
	optionMethod.max = function(_name, _set)
	{
		var data_min = parseInt($(this).data('data-min'));
		if (!isNaN(_set))
		{
			var data_max = _set;
			if(isNaN(data_max) || data_min >= data_max)
			{
				data_max = data_min + 100;
				var json_string = $(this).data("save_jason");
				if (json_string)
				{

				}
			}

			$(this).attr('data-max',data_max);
			$(this).data('data-max',data_max);
			// return data_max;
		}
		var data_max = parseInt($(this).data('data-max'));


		var my_step = 0;

		if ($(this).rangeSlider('option', 'check_json'))
		{
			var json_string = $(this).data("save_jason");
			var ends = [];
			var multi_steps = JSON.parse(json_string);
			var json_steps = multi_steps;
			for (var i = 0; i < multi_steps.length; i++)
			{
				var multi_steps_details = multi_steps[i];
				var start = parseInt(multi_steps_details["start"]);
				var end   = parseInt(multi_steps_details["end"]);
				ends.push(end);

				var max = data_max;
				if (max <= end && max>start)
				{
					end = max;
					var step  = parseInt(multi_steps_details["step"]);
					my_step  += ((end - start) / step);
					break
				}
				var step  = parseInt(multi_steps_details["step"]);
				my_step  += ((end - start) / step);
			}
			var _unit = $(this).rangeSlider('option', 'unit');
			$(this).attr('data-step', (_unit/my_step));
			$(this).data('data-step', (_unit/my_step));
		}

		if(isNaN(data_max) || data_min >= data_max)
		{
			data_max = data_min + 100;
			$(this).attr('data-max',data_max);
			$(this).data('data-max',data_max);
		}

		if ($(this).rangeSlider('option', 'check_json'))
		{
			var max_limit = $(this).data('data-max-limit-first');
			$(this).data('max_limit', max_limit)
		}

		if(data_max < $(this).rangeSlider('option', 'max_limit'))
		{
			$(this).find('.max_limit').remove();
		}

		return data_max;
	}

	/**
	 * [min_unit minimum unit that range slider can have]
	 * @param  {[type]} _name [name of method]
	 * @param  {[type]} _set  [value that we want to set it for min_unit method]
	 * @return {[type]}       [min unit value]
	 *                        [if value that set for minimum unit was greater than max ,it will set on max mount']
	 */
	optionMethod.min_unit = function(_name, _set)
	{
		if (!isNaN(_set))
		{
			var max = $(this).rangeSlider('option', 'max_limit');
			var min = $(this).rangeSlider('option', 'min');
			if (isNaN(max))
			{
				var max = $(this).rangeSlider('option', 'max');
			}
			if (_set > max) 
			{
				_set = max - min;
			}

			var data_min_unit = _set;
			if(isNaN(data_min_unit))
			{
				data_min_unit = 0;
			}
			$(this).attr('data-min-unit',data_min_unit);
			$(this).data('data-min-unit',data_min_unit);
			return data_min_unit;
		}

		var data_min_unit = Number($(this).data('data-min-unit'));
		if(isNaN(data_min_unit))
		{
			data_min_unit = 0;
			$(this).attr('data-min-unit',data_min_unit);
			$(this).data('data-min-unit',data_min_unit);
		}
		return data_min_unit;
	}

	/**
	 * [unit all units on range slider, max - min]
	 * @param  {[type]} _name [name of method]
	 * @return {[integer]}       [the count of units on range slider, from min to max]
	 */
	optionMethod.unit = function(_name, _set)
	{
		var data_unit;
		var data_max = Number($(this).data("data-max"));
		var data_min = Number($(this).data('data-min'));

		if ($(this).rangeSlider('option', 'check_json'))
		{
			data_min = 0;
		}

		var data_unit = data_max - data_min;
		return data_unit;
	}

	optionMethod.min_title = function(_name, _set)
	{
		var min_title = $(this).data("data-min-title");
		if (!isNaN(_set))
		{
			var min_title = _set;
			$(this).attr('data-min-title',min_title);
			$(this).data('data-min-title',min_title);
		}
		return min_title;
	}

	optionMethod.max_title = function(_name, _set)
	{
		var max_title = $(this).data("data-show-title");
		if (!isNaN(_set))
		{
			var max_title = _set;
			$(this).attr('data-show-title',max_title);
			$(this).data('data-show-title',max_title);
		}
	}


	/**
	 * [max_limit maximum mount of limitation, different from max]
	 * @param  {string} _name    name of method
	 * @param  {integer} _set    value that we want to set it for min_unit method
	 * @param  {integer} _multi  to set max_limit you should put this value = 1
	 * @return {integer}         max_limit mount will be returned
	 */
	optionMethod.max_limit = function(_name, _set, _multi)
	{
		var max_limit;
		max_limit = parseInt($(this).data("data-max-limit"));
		init_max_limit = parseInt($(this).data("data-max-limit"));
		if(!isNaN(_set))
		{
			max_limit = parseInt(_set);

			if ($(this).rangeSlider('option', 'check_json'))
			{
				if ($(this).data('_status') ) 
				{
					$(this).removeData('_status')
				}
				var my_step = $(this).rangeSlider('option','step');
				var real_limit_unit = $(this).rangeSlider('option','multi_level_value_to_real', _set);
				if (!isNaN(real_limit_unit))
				{
					var real_limit_unit = $(this).rangeSlider('option', 'change_multi_level_float',_set);
					var _status = 'float';
					$(this).data('_status', _set);
				}

				var multi_max_limit  = _set;
				var init_max_limit   = _set;
				var max_limit 		 = real_limit_unit * my_step;
				$(this).data('real_max_limit',max_limit);
			}

			if (max_limit <= $(this).rangeSlider('option','max')) 
			{
				if(!isNaN(max_limit) && !( max_limit < ($(this).rangeSlider('option','min'))) )
				{
					$(this).attr("data-max-limit", max_limit);
					$(this).data("data-multi-max-limit", max_limit);
				}
				else
				{
					$(this).removeData("data-max-limit");
				}

				var limit_value = max_limit - $(this).rangeSlider('option','min');

				if ($(this).rangeSlider('option', 'check_json'))
				{
					var json_string = $(this).data("save_jason");
					var json_steps = jQuery.parseJSON( json_string );
					var json_steps_details = json_steps[0];
					var start  = parseInt(json_steps_details["start"]);
					var data_min = start;
					var data_min_unit = $(this).rangeSlider('option', 'multi_level_value_to_real', data_min);
					var my_step = $(this).rangeSlider('option','step');
					// data_min = Math.round(my_step * data_min_unit);
					data_min = my_step * data_min_unit;
					var limit_value = max_limit - data_min;
				}


// if ($(this).data("data-infinity") == 'min')
// {
// 	$(this).rangeSlider('option', 'set_range', 'to',{type:'ziro_unit'}, $(this).attr('data-max-default'));
// }


				var limit_value_percent = (limit_value * 100) / $(this).rangeSlider('option', 'unit');
				var margin_type = $(this).rangeSlider('option', 'type') == 'vertical'? "top" : "left";
				if(!$('.max_limit', this).length){
					$(this).append("<div class='max_limit'></div>");
					$(this).find(".max_limit").append("<span class='mount'></span>");
					$(this).find(".max_limit").append("<svg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 8 9' style='enable-background:new 0 0 8 9;' xml:space='preserve'><style type='text/css'>.st0{fill:#E96914;}</style><path class='st0' d='M3.2,0C6.7-0.1,5.4,3.5,8,4.5C5.4,5.5,6.7,9.1,3.2,9L2.7,9V7.1L0,4.5l2.7-2.6V0L3.2,0z'/></svg>");
				}
				if (margin_type == 'top')
				{
					$(this).find(".max_limit").css('top', 100 - limit_value_percent + "%")


// if ($(this).data("data-infinity") == 'min')
// {
// 	$(this).rangeSlider('option', 'set_range', 'to',{type:'ziro_unit'}, $(this).attr('data-max-default'));
// }
				}
				else
				{
					if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && $(this).rangeSlider('option','type')!='vertical')
					{
						limit_value_percent = (100-limit_value_percent);
					}
					$(this).find(".max_limit").css('left', limit_value_percent + "%")
				}

				var show_max_limit = max_limit;
				var json_string = $(this).data("save_jason");
				if (json_string)
				{
					var my_step = $(this).rangeSlider('option','step');
					show_max_limit = $(this).rangeSlider('option','multi_level_real_to_value', Math.round(show_max_limit/my_step));


// if (margin_type == 'top')
// {
// 	if ($(this).data("data-infinity") == 'min')
// 	{
// 		$(this).rangeSlider('option', 'set_range', 'to',{type:'ziro_unit'}, 10);
// 	}
// }

				}
				$(this).attr("data-max-limit-first", show_max_limit);
				$(this).data("data-max-limit-first", show_max_limit);
				if ($(this).data('_status')) 
				{
					$(this).data("data-max-limit-first", $(this).data('_status'));
				}
				if (!isNaN(multi_max_limit))
				{
					$(this).attr("data-max-limit", multi_max_limit);
					$(this).data("data-max-limit", multi_max_limit);
				}
				$(this).find(".max_limit .mount").attr("data-value-show", $(this).data('data-max-limit'));



			}

		}
		else if(isNaN(max_limit))
		{
			max_limit = undefined;
		}

		if (!isNaN(multi_max_limit))
		{
			return multi_max_limit;
		}
		else
		{
			return max_limit;
		}
	}
	
	/**
	 * [min_default min element default value]
	 * @param  {string} _name name of method
	 * @param  {integer} _set  default min element value can be set by setting this parameter
	 * @return {integer}       min element default mount
	 */
	optionMethod.min_default = function(_name, _set)
	{
		var _self = $(this).parents("range-slider");
		var data_min = parseInt($(this).data('data-min'));
		if ($(this).data("data-infinity") != 'min')
		{
			if (!isNaN(_set))
			{
				var data_min_default = _set;
				if(isNaN(data_min_default) || data_min > data_min_default)
				{
					data_min_default = data_min;
				}

				var json_string = $(this).data("data-step");
				var json_string_step = $(this).data("save_jason");
				if (json_string)
				{
					var unit = $(this).rangeSlider('option','unit') + $(this).rangeSlider('option','min');
					var step = $(this).rangeSlider('option','step');
					var real_step = Math.round(unit/step);
					var _set = $(this).rangeSlider('option', 'change_multi_level_float',_set);
					var real_min = $(this).rangeSlider('option','multi_level_value_to_real', _set);

					if (!isNaN(real_min))
					{
						var data_min_default = real_min*real_step;
					}
				}
				$(this).attr('data-min-default',data_min_default);
				$(this).data('data-min-default',data_min_default);
				return data_min_default;
			}

			var data_min_default = Number($(this).data('data-min-default'));
			if(isNaN(data_min_default) || data_min > data_min_default)
			{
				data_min_default = data_min;
				$(this).attr('data-min-default',data_min_default);
				$(this).data('data-min-default',data_min_default);
			}
			return data_min_default;
		}
		else
		{
			return $(this).rangeSlider('option','min');
		}
	}

	/**
	 * [max_default description]
	 * @param  {[type]} _name  [description]
	 * @param  {[type]} _set   [description]
	 * @param  {[type]} _multi [description]
	 * @return {[type]}        [description]
	 */
	optionMethod.max_default = function(_name, _set, _multi)
	{
		if ($(this).data("data-infinity") != 'max')
		{
			var data_max = Number($(this).data("data-max"));
			var data_min = Number($(this).data('data-min'));
			var data_unit = data_max - data_min;

			if (!isNaN(_set))
			{
				var data_max_default = _set;
				if(isNaN(data_max_default) || data_max < data_max_default)
				{
					data_max_default = data_unit;
				}

				if ($(this).rangeSlider('option', 'check_json'))
				{
					var unit = $(this).rangeSlider('option','unit') + $(this).rangeSlider('option','min');
					var step = $(this).rangeSlider('option','step');
					// var real_step = Math.round(unit/step);
					var real_step = unit/step;
					// if (json_string_step)
					// {
					// 	var real_step = Math.round(step);
					// }
					// var _set = $(this).rangeSlider('option', 'change_multi_level_float',_set);
					var real_max = $(this).rangeSlider('option','multi_level_value_to_real', _set);



					if (!real_max) 
					{
						var real_max = $(this).rangeSlider('option', 'change_multi_level_float',_set);
					}
					var data_max_default = real_max*real_step;
				}
				$(this).data('data-max-default',data_max_default);
				return data_max_default;
			}
	
			var data_max_default = Number($(this).data('data-max-default'));
			if(isNaN(data_max_default) || data_max < data_max_default)
			{
				data_max_default = data_unit;
				$(this).attr('data-max-default',data_max_default);
				$(this).data('data-max-default',data_max_default);
			}
			return data_max_default;
		}
		else
		{
			return $(this).rangeSlider('option','max');
		}
	}

	/**
	 * [margin left of range],
	 * @param  {string} _name [name of method]
	 * @return {integer}      [left of range will be returned, it's pixel]
	 */
	optionMethod.margin = function(_name)
	{
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var margin = parseInt($(this).find(".dynamic-margin").css(margin_type));
		return margin;
	}

	/**
	 * [depth max mount of the range]
	 * @param  {string} _name [name of method]
	 * @return {integer}      [max mount of the range]
	 */
	optionMethod.depth = function(_name)
	{
		var data_max = Number($(this).data("data-max"));
		var depth = data_max;
		return depth;
	}

	/**
	 * [unit_to_pixel description]
	 * @param  {string} _name  name of method
	 * @param  {[type]} _set   value in unit you want to change it to pixel
	 * @return {[type]}        pixel value of unit ,had been sent to function
	 */
	optionMethod.unit_to_pixel = function(_name, _set)
	{
		var total_unit = $(this).rangeSlider('option', 'max') - $(this).rangeSlider('option', 'min');
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var pixel_width = parseInt($(this).css(margin_type));


		if ($(this).rangeSlider('option', 'check_json'))
		{
			if ($(this).rangeSlider('option', 'max_limit'))
			{
				var my_limit = $(this).rangeSlider('option', 'max_limit');
				var max_limit = $(this).rangeSlider('option', 'multi_level_value_to_real', my_limit);
			}
			else
			{
				unit_to_pixel = $(this).rangeSlider('option', 'max') - $(this).rangeSlider('option', 'min');
			}
		}
		var unit_to_pixel = parseInt((_set*pixel_width)/total_unit);

		return unit_to_pixel;
	}




	/**
	 * [change_multi_level_float description]
	 * @param  {string} _name   name of method
	 * @param  {integer} _set   values that are'nt a step of multi steps, will manage here
	 * @return {integer}  		it will return a real step of multi level range      
	 */
	optionMethod.change_multi_level_float= function(_name, _set)
	{
		var steps  = [];
		var starts = [];
		var ends   = [];
		if ($(this).rangeSlider('option', 'check_json')) 
		{
			var json_string = $(this).data("save_jason");
			var json_steps = jQuery.parseJSON( json_string );
			for (var i = 0; i < json_steps.length; i++)
			{
				var json_steps_details = json_steps[i];
				var start  = parseInt(json_steps_details["start"]);
				var end    = parseInt(json_steps_details["end"]);
				var step   = parseInt(json_steps_details["step"]);
				steps.push(step);
				starts.push(start);
				ends.push(end);
			}

			var levels = [];
			var level = 0;
			for (var k = 0; k < starts.length; k++)
			{
				level += (ends[k]-starts[k])/steps[k];
				levels.push(level);
			}

			var counter = 0;
			var changed_mount = 0;
			var levels_length=0;
			for (var i = 0; i < levels.length; i++)
			{
				var move = ends[i] - starts[i];
				counter = 0;
				levels_length++;

				while(counter < ends[i])
				{
					counter += steps[i];
					changed_mount++;

					if (_set == counter) 
					{
						changed_mount = changed_mount - levels_length+1;
						return (changed_mount)
					}
					else if (_set < counter)
					{
						changed_mount = changed_mount - levels_length+1;
						return (changed_mount)
					}
				}
			}
			return 0
		}
	}




	/**
	 * [multi_level_value_to_real description]
	 * @param  {string} _name  name of method
	 * @param  {[type]} _set   set can be a mount of real steps of multi level range
	 * @return {[type]}        will return the step that mount is on it (for example; if you set=1000 , it will return 19)
	 */
	optionMethod.multi_level_value_to_real = function(_name, _set)
	{
		var json_string = $(this).data("save_jason");
		var steps  = [];
		var starts = [];
		var ends   = [];
		if (json_string)
		{
			var json_steps = jQuery.parseJSON( json_string );
			for (var i = 0; i < json_steps.length; i++)
			{
				var json_steps_details = json_steps[i];
				var start  = parseInt(json_steps_details["start"]);
				var end    = parseInt(json_steps_details["end"]);
				var step   = parseInt(json_steps_details["step"]);
				steps.push(step);
				starts.push(start);
				ends.push(end);
			}

			var levels = [];
			var level = 0;
			for (var k = 0; k < starts.length; k++)
			{
				level += (ends[k]-starts[k])/steps[k];
				levels.push(level);
			}

			var counter = 0;
			var changed_mount = 0;
			var levels_length=0;
			for (var i = 0; i < levels.length; i++)
			{
				var move = ends[i] - starts[i];
				counter = 0;
				levels_length++;

				while(counter < ends[i])
				{
					counter += steps[i];
					changed_mount++;
					if (_set == counter) 
					{
						changed_mount = changed_mount - levels_length+1;
						return (changed_mount)
					}
				}
			}
			return false
		}
	}


	/**
	* [multi_level_real_to_value description]
	* @param  {string} _name   name of method 
	* @param  {integer} _set   this value is the step that we want to know it's multi step value
	* @return {integer}        will return the multi step value (for example; if you set=19 , it will return 1000)
	*/
	optionMethod.multi_level_real_to_value = function(_name, _set)
	{
		var json_string = $(this).data("save_jason");
		var steps  = [];
		var starts = [];
		var ends   = [];
		if (json_string)
		{
			var json_steps = jQuery.parseJSON( json_string );
			for (var i = 0; i < json_steps.length; i++)
			{
				var json_steps_details = json_steps[i];
				var start  = parseInt(json_steps_details["start"]);
				var end    = parseInt(json_steps_details["end"]);
				var step   = parseInt(json_steps_details["step"]);
				steps.push(step);
				starts.push(start);
				ends.push(end);
			}

			var levels = [];
			var level  = 0;
			for (var k = 0; k < starts.length; k++)
			{
				level += (ends[k]-starts[k])/steps[k];
				levels.push(level);
			}

			var changed_mount = 0;
			var levels_length = 0;
			for (var i = 0; i < levels.length; i++)
			{
				var sum_counter = null;
				levels_length++;
				
				while(sum_counter < ends[i]){
					changed_mount++;
					sum_counter += steps[i];
					if (_set == (changed_mount-(levels_length-1)) && sum_counter)
					{
						changed_mount = changed_mount - 1;
						return (sum_counter)
					}
				}
			}
			return 0
		}
	}


	/**
	 * [range_width description]
	 * @param  {string} _name 	name of method
	 * @return {integer}       	width of range pixel width
	 */
	optionMethod.range_width = function(_name)
	{
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var range_width = parseInt($(this).find(".dynamic-range").css(margin_type));
		return range_width;
	}

	/**
	 * [total_width description]
	 * @param  {[type]} _name [name of method, is total_width]
	 * @return {[type]}       [this will return ]
	 */
	optionMethod.total_width = function(_name)
	{
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var total_width = parseInt($(this).css(margin_type));
		return total_width;
	}






	/**
	 * [total_width description]
	 * @param  {[type]} _name [name of method, is total_width]
	 * @return {[type]}       [this will return ]
	 */
	optionMethod.check_json = function(_name)
	{
		var json;
		var my_step;
		var data = $(this).data("save_jason");
		try {
	    my_step = $.parseJSON(data);
	    json = true;

		} catch (e) {
			my_step = Number(data);
			if (isNaN(my_step))
			{
				my_step = 1;
			}
			json = false;
		}
		return json;
	}









	/**
	 * [total_width description]
	 * @param  {[type]} _name [name of method, is total_width]
	 * @return {[type]}       [this will return ]
	 */
	optionMethod.lock = function(_name,_set)
	{
		var lock;
		if (_set !== undefined)
		{
			if (_set === true)
			{
				lock = 1;
				$(this).data('data-lock','on');
			}

			else
			{
				lock = 0;
				$(this).data('data-lock','off');
			}
		}

		else if(_set == undefined)
		{
			var lock_state = $(this).data('data-lock');
			if (lock_state == 'on')
			{
				lock = 1;
			}
			else
			{
				lock = 0;
			}
		}
			return lock;
	}

	optionMethod.range = function(_name, _set, _option, _value, _multi)
	{
		if (_value == null)
		{
			if (_set == 'from')
			{
				return ($(this).rangeSlider('option', 'margin'));
			}
			else if (_set == 'to')
			{
				return ($(this).rangeSlider('option', 'margin')+$(this).rangeSlider('option','range_width'));
			}
		}

		if (_set == 'to')
		{
			return $(this).range(null, _value, _option);
		}
		else if (_set == 'from')
		{
			if ($(this).data('data-infinity') == 'min')
			{
				return false;
			}
			else
			{
				return $(this).range(_value, null, _option);
			}
		}
	}




	optionMethod.set_range = function(_name, _set, _option, _value)
	{
		if (_value == null)
		{
			if (_set == 'from')
			{
				return ($(this).rangeSlider('option', 'margin'));
			}
			else if (_set == 'to')
			{
				return ($(this).rangeSlider('option', 'margin')+$(this).rangeSlider('option','range_width'));
			}
		}

		if ($(this).rangeSlider('option', 'check_json'))
		{
			var real_steps = $(this).rangeSlider('option', 'multi_level_value_to_real', _value);
			if (!real_steps) 
			{
				var real_steps = $(this).rangeSlider('option', 'change_multi_level_float',_value);
			}

			var my_step = $(this).rangeSlider('option', 'step');
			var my_steps = Math.round(real_steps*my_step);
			if (_set == 'to')
			{
				return $(this).range( null, my_steps, _option);
			}
			else if (_set == 'from')
			{
				if ($(this).data('data-infinity') == 'min')
				{
					return false;
				}
				else
				{
					return $(this).range(my_steps, null, _option);
				}
			}
		}
		else
		{
			if (_set == 'to')
			{
				return $(this).range(null, _value, _option);
			}
			else if (_set == 'from')
			{
				if ($(this).data('data-infinity') == 'min')
				{
					return false;
				}
				else
				{
					return $(this).range(_value, null, _option);
				}
			}
		}
	}



	/**
	 * [option this is a corridor that calls all methods]
	 * @param  {[type]} _name  [name of method that will be run]
	 * @return {[type]}        [returned value from method]
	 */
	function option(_name)
	{
		var _args = Array.prototype.slice.call(arguments);
		return optionMethod[_name].apply(this, _args);
	}


	/**
	 * [rangeSlider the main function of range slider that runs methods or destroy or restarts the range]
	 * @param  {[type]} _method [method we want to set on range slider, restart, destroy, option(this will call option method)]
	 * @return {[type]}         [destroy method, destroys the range slider]
	 * @return {[type]}         [restart method, restarts the range slider]
	 * @return {[type]}         [option method, calls methods and returns the returned valude from methods]
	 */
	$.fn.rangeSlider = function(_method)
	{
		var _args = Array.prototype.slice.call(arguments);
		switch(_method)
		{
			case 'destroy':
				return destroy.call(this);
			case 'restart':
				return restart.call(this);
			case 'option':
				return option.apply(this, _args.splice(1));
			break;
		}

		$(this).each(function(){
			if($(this).hasClass('range-slider'))
			{
				$(this).rangeSlider('destroy');
			}
			$(this).trigger("range-slider::init::before");


			$(this).data("data-min", $(this).attr("data-min"));
			$(this).data("data-max", $(this).attr("data-max"));
			$(this).data("data-step", $(this).attr("data-step"));
			$(this).data("data-max-default", $(this).attr("data-max-default"));
			$(this).data("data-min-default", $(this).attr("data-min-default"));
			$(this).data("data-infinity", $(this).attr("data-infinity"));
			$(this).data("data-max-limit", $(this).attr("data-max-limit"));
			$(this).data("data-unit", $(this).attr("data-unit"));
			$(this).data("data-min-unit", $(this).attr("data-min-unit"));
			$(this).data("data-show-title", $(this).attr("data-show-title"));
			$(this).data("data-fix-mount", $(this).attr("data-fix-mount"));
			$(this).data("data-lock", $(this).attr("data-lock"));
			$(this).data("data-type", $(this).attr("data-type"));
			$(this).data("data-fix-direction", $(this).attr('data-fix-direction'));



			$(this).addClass('range-slider');

			var data_infinity = $(this).data("data-infinity");
			var id = $(this).attr('id');
			if(id)
			{
				if (data_infinity == 'max')
				{
					$('<input type="hidden" name="'+id+'-min" data-range-bind="'+id+'" data-range-value="min">').appendTo(this);
				}
				else if(data_infinity == 'min')
				{
					$('<input type="hidden" name="'+id+'-max" data-range-bind="'+id+'" data-range-value="max">').appendTo(this);
				}
				else
				{
					$('<input type="hidden" name="'+id+'-max" data-range-bind="'+id+'" data-range-value="max">').appendTo(this);
					$('<input type="hidden" name="'+id+'-min" data-range-bind="'+id+'" data-range-value="min">').appendTo(this);
				}
			}




			$(this).rangeSlider('option', 'type', $(this).data("data-type"));
			// $(this).rangeSlider('option', 'step', $(this).attr("data-step"));
			$(this).rangeSlider('option', 'min', $(this).data("data-min"));
			$(this).rangeSlider('option', 'max', $(this).data("data-max"));
			$(this).rangeSlider('option', 'unit', $(this).data("data-unit"));
			$(this).rangeSlider('option', 'min_default', $(this).data("data-min-default"));
			$(this).rangeSlider('option', 'max_default', $(this).data("data-max-default"));
			// $(this).rangeSlider('option', 'min_unit', $(this).attr("data-min-unit"));
			// $(this).rangeSlider('option', 'min_title', $(this).attr("data-min-title"));
			// $(this).rangeSlider('option', 'max_title', $(this).attr("data-min-title"));
			// $(this).rangeSlider('option', 'max_limit', $(this).attr("data-max-limit"));



			$(this).data('range-slider', { });

			/**
			 * [range set]
			 * @param  {[type]} _from   [min element mount]
			 * @param  {[type]} _to     [max element mount]
			 * @param  {[type]} _option [type of range we want to move in that scale, pixel or unit or percent or ziro_point]
			 * @return {[type]}         [description]
			 */
			$(this).init.prototype.range = function(_from, _to, _option){
				var from = _from;
				var to = _to;
				var data = $(this).data('range-slider');

				data.type           = this.rangeSlider('option', 'type');
				data.step           = this.rangeSlider('option', 'step');
				data.min            = this.rangeSlider('option', 'min');
				data.max            = this.rangeSlider('option', 'max');
				data.unit           = this.rangeSlider('option', 'unit');
				data.min_default    = this.rangeSlider('option', 'min_default');
				data.max_default    = this.rangeSlider('option', 'max_default');
				data.margin         = this.rangeSlider('option', 'margin');
				data.depth          = this.rangeSlider('option', 'depth');
				data.min_unit       = this.rangeSlider('option', 'min_unit');
				data.min_title      = this.rangeSlider('option', 'min_title');
				data.max_title      = this.rangeSlider('option', 'max_title');
				data.max_limit      = this.rangeSlider('option', 'max_limit');
				data.unit_to_pixel  = this.rangeSlider('option', 'unit_to_pixel');
				data.range_width    = this.rangeSlider('option', 'range_width');
				data.total_width    = this.rangeSlider('option', 'total_width');
				data.lock    		= this.rangeSlider('option', 'lock');
				data.check_json    		= this.rangeSlider('option', 'check_json');

				data.real_max_limit = $(this).data('real_max_limit');

				var option = {
					type : 'unit'
				}

				$.extend(option, _option);
				option.from_type = option.type;
				option.to_type = option.type;
				var depth_type = data.type == 'vertical' ? 'height' : 'width';


				if(from === null || from === false || from === undefined)
				{
					from = this.find('.dynamic-margin')[depth_type]();
					if($(this).rangeSlider('option', 'type') == 'vertical')
					{
						from = this[depth_type]() - (from + this.find('.dynamic-range')[depth_type]());
					}
					option.from_type = 'pixel';
				}
				if(to === null || to === false || to === undefined)
				{
					to = this.find('.dynamic-margin')[depth_type]() + this.find('.dynamic-range')[depth_type]();
					if(($(this).rangeSlider('option', 'type')) == 'vertical')
					{
						to = this[depth_type]() - this.find('.dynamic-margin')[depth_type]();
					}
					option.to_type = 'pixel';
				}
				var base_depth = this[depth_type]();

				if(option.from_type == 'pixel')
				{
					from = (from * ($(this).rangeSlider('option', 'unit'))) / base_depth;
				}
				else if(option.from_type == 'percent')
				{
					from = (from * ($(this).rangeSlider('option', 'unit'))) / 100;
				}
				else if(option.from_type == 'ziro_unit')
				{
					from -= ($(this).rangeSlider('option', 'min'));
				}
				else if(option.from_type == 'step_plus')
				{
					from = (from * ($(this).rangeSlider('option', 'step'))) + data.from;
				}

				if(option.to_type == 'pixel')
				{
					to = (to * ($(this).rangeSlider('option', 'unit'))) / base_depth;
				}
				else if(option.to_type == 'percent')
				{
					to = (to * ($(this).rangeSlider('option', 'unit'))) / 100;
				}
				else if(option.to_type == 'ziro_unit')
				{
					to -= ($(this).rangeSlider('option', 'min'));
				}
				else if(option.to_type == 'step_plus')
				{
					to = (to * ($(this).rangeSlider('option', 'step'))) + data.to;
				}
				var from_step = Math.round(from / ($(this).rangeSlider('option', 'step'))) * ($(this).rangeSlider('option', 'step'));
				var to_step = Math.round(to / ($(this).rangeSlider('option', 'step'))) * ($(this).rangeSlider('option', 'step'));

				if ($(this).rangeSlider('option', 'check_json'))
				{
					var my_step = $(this).rangeSlider('option','step');
					var max_limit = $(this).rangeSlider('option','max_limit');

					$(this).rangeSlider('option', 'max_limit', data.max_limit,1);

					if (!isNaN(data.real_max_limit)) 
					{
						data.max_limit = data.real_max_limit;
					}
				}
// controlls the range not to exit from range
				if ((to_step) > (data.max_limit - data.min))
				{
					to_step = data.max_limit-data.min;
				}


				if ((from_step) > (data.max_limit-data.min-data.min_unit))
				{
					from_step = data.max_limit-data.min-data.min_unit;
				}
					if(to_step <  from_step)
					{
						if(data.to == to_step)
						{
							to_step = from_step;
						}
						else
						{
							from_step = to_step;
						}
					}

					if(to_step > data.unit)
					{
						to_step = data.unit;
					}

					if(from_step > data.unit)
					{
						from_step = data.unit;
					}

					if(to_step < 0)
					{
						to_step = 0;
					}

					if(from_step < 0)
					{
						from_step = 0;
					}



					if (!$(this).rangeSlider('option', 'check_json'))
					{
						if ($(this).data('data-max-limit'))
						{
							$(this).rangeSlider('option', 'max_limit', $(this).data('data-max-limit'));
						}
					}



// setting the value showed on min and max elements
					var min_unit = $(this).rangeSlider('option', 'min_unit');
					$(this).find(".dynamic-range .min .mount").attr("data-value-show", parseInt(data.min + from_step));
					$(this).find(".dynamic-range .max .mount").attr("data-value-show", parseInt(data.min + to_step));

					if ($(this).rangeSlider('option','max_limit'))
					{
						$(this).find(".max_limit .mount").attr("data-value-show", $(this).rangeSlider('option','max_limit'));
						if ($(this).data('data-max-limit-first'))
						{
							$(this).find(".max_limit .mount").attr("data-value-show", $(this).data('data-max-limit-first'));
						}
					}
// controlls range not to be less than min unit mount that had been set by user on html
				if ( to_step - from_step <= min_unit )
				{
					$(this).find(".dynamic-range .max .mount").attr("data-value-show", (min_unit+data.min+from_step));
					$(this).find(".dynamic-range .min .mount").attr("data-value-show", (to_step-min_unit+data.min));

					if (to_step <= min_unit+from_step)
					{
						if(_from == null)
						{
							from_step = to_step - min_unit;
						}
						else if(_to == null)
						{
							to_step = from_step + min_unit;
						}

						if (to_step >= ($(this).rangeSlider('option', 'unit')))
						{
							to_step = ($(this).rangeSlider('option', 'unit'));
							from_step = ($(this).rangeSlider('option', 'unit') - min_unit);
						}

					}
					if (from_step <= data.min)
					{
						$(this).find(".dynamic-range .min .mount").attr("data-value-show", (data.min));
					}
				}

// multi step ranges here will be set
// gets json steps attribute from html and convert it to a standard range slider
				var json_string = $(this).data("save_jason");
				var steps = [];
				var starts = [];
				var ends = [];
				if (json_string)
				{
					var json_steps = jQuery.parseJSON( json_string );
					for (var i = 0; i < json_steps.length; i++)
					{
						var json_steps_details = json_steps[i];
						var start  = parseInt(json_steps_details["start"]);
						var end    = parseInt(json_steps_details["end"]);
						var step   = parseInt(json_steps_details["step"]);
						steps.push(step);
						starts.push(start);
						ends.push(end);

						var min = Math.round( ($(this).rangeSlider('option','min')) / ($(this).rangeSlider('option','step')) );
						var max = ($(this).rangeSlider('option','max'));


						if (i == 0 && min>start)
						{
							$(this).rangeSlider('option','min',min)
						}


						else if (i == 0 && min<=start) 
						{
							$(this).rangeSlider('option','min',start);
							var my_max   = $(this).rangeSlider('option','multi_level_value_to_real', $(this).rangeSlider('option','max'));
							var my_start = $(this).rangeSlider('option','multi_level_value_to_real',start);
							var my_step  = $(this).rangeSlider('option','step');
							$(this).rangeSlider('option','unit',((my_max-my_start)*my_step))
						}

						var new_from_step = Math.round(from_step / ($(this).rangeSlider('option','step')));
						var new_to_step = Math.round(to_step/ ($(this).rangeSlider('option','step')));
					}

					var levels = [];
					var level = 0;
					for (var k = 0; k < starts.length; k++)
					{
						level += (ends[k]-starts[k])/steps[k];
						levels.push(level);
					}

					var counter = 0;
					for (var i = 0; i < levels.length; i++)
					{
						if (new_to_step <= levels[i])
						{
							if (isNaN (new_to_step-(levels[i-1])))
							{
								var move = new_to_step;
							}
							else
							{
								var move = new_to_step-(levels[i-1]);
							}
							var to_multi_step_value = ( starts[i] + (move*steps[i]) );
							break
						}
					}

					for (var i = 0; i < levels.length; i++)
					{
						if (new_from_step <= levels[i])
						{
							if (isNaN (new_from_step-(levels[i-1])))
							{
								var move = new_from_step;
							}
							else
							{
								var move = new_from_step-(levels[i-1]);
							}
							var from_multi_step_value = ( starts[i] + (move*steps[i]) );
							break
						}
					}

					$(this).find(".dynamic-range .min .mount").attr("data-value-show", parseInt(from_multi_step_value));
					$(this).find(".dynamic-range .max .mount").attr("data-value-show", parseInt(to_multi_step_value));

// _status is setting when a multi level range has max_limit attribute, then the real mount will be set on _status attribute
// here value show of multi level range when it has max_limit attribute

					if ($(this).data('_status')) 
					{
						var my_limit = $(this).rangeSlider('option', 'max_limit');

						if ($(this).rangeSlider('option', 'check_json'))
						{
							my_limit = $(this).data('real_max_limit');
						}

						var _step = ($(this).rangeSlider('option', 'step'));
						my_limit = my_limit / _step;
						var my_limit_mount = $(this).rangeSlider('option', 'multi_level_real_to_value',my_limit)

						if (from_multi_step_value == my_limit_mount) 
						{
							$(this).find(".dynamic-range .min .mount").attr("data-value-show", parseInt($(this).data('_status')));
							if (from_multi_step_value == to_multi_step_value) 
							{
								$(this).find(".dynamic-range .max .mount").attr("data-value-show", parseInt($(this).data('_status')));
							}
						}

						else if (to_multi_step_value == my_limit_mount) 
						{
							$(this).find(".dynamic-range .max .mount").attr("data-value-show", parseInt($(this).data('_status')));
							if (from_multi_step_value == to_multi_step_value) 
							{
								$(this).find(".dynamic-range .min .mount").attr("data-value-show", parseInt($(this).data('_status')));
							}
						}
					}

				}

				var data_value_max = $(this).find(".dynamic-range .max .mount").attr("data-value-show");
				var data_value_min = $(this).find(".dynamic-range .min .mount").attr("data-value-show");

// value of max and min element will be save on on a input for each one
// 
				var id = this.attr('id');
				if(id)
				{
					$('[data-range-bind="' + id + '"]').each(function(){
						var value_type = $(this).attr('data-range-value');
						if(value_type == 'max')
						{
							$(this).val(data_value_max);
						}
						else if(value_type == 'min')
						{
							$(this).val(data_value_min);
						}
					});
				}

// if user had set data show title, here it will set and will show it instead of the real digit

				var show_title;
				var _data = $(this).attr("data-show-title");
				try {
				show_title = $.parseJSON(_data);

				} catch (e) {
					show_title = (_data);
				}

				if (Array.isArray(show_title))
				{
					for (var i = show_title.length - 1; i >= 0; i--) {
						var show_title_details = show_title[i];
						for(var key in show_title_details)
						{
							var key_mount = key;
							if (key == 'min') 
							{
								key = data.min;
								var str_min = true;
							}
							else if (key == 'max')
							{
								key = data.max;
								var str_max = true;
							}

							var str = show_title_details[key];
							if (str) 
							{
								if (str.indexOf(":val") >= 0)
								{
									show_title_details[key] = str.replace(":val", key);
								}
							}

							if (data_value_max == key)
							{
								if (key == data.min && str_min ) 
								{
									var key_mount = key;
									key = 'min';
								}
								else if (key == data.max && str_max ) 
								{
									var key_mount = key;
									key = 'max';
								}
								$(this).find(".dynamic-range .max .mount").attr("data-value-show",show_title_details[key]);

								if(data_value_min == key_mount)
								{
									$(this).find(".dynamic-range .min .mount").attr("data-value-show",show_title_details[key]);
								}
							}
							
							else if(data_value_min == key )
							{
								if (key == data.min && str_min ) 
								{
									var key_mount = key;
									key = 'min';
								}
								else if (key == data.max && str_max ) 
								{
									var key_mount = key;
									key = 'max';
								}
								$(this).find(".dynamic-range .min .mount").attr("data-value-show",show_title_details[key]);
								if(data_value_max == key_mount)
								{
									$(this).find(".dynamic-range .max .mount").attr("data-value-show",show_title_details[key]);
								}
							}
						}
					}
				}


//converts from_step and to_step to percent, because for responsive we should to use percent, not pixel
//
				from = (from_step * 100) / ($(this).rangeSlider('option', 'unit'));
				to   = (to_step * 100) / ($(this).rangeSlider('option', 'unit'));

				var depth = to - from;
				if(data.to != to_step || data.from != from_step)
				{
					this.data('range-slider').from = from_step;
					this.data('range-slider').to = to_step;

					this.trigger("range-slider::change::before", [data.min + from_step, data.min + to_step]);


					if(data.type == 'vertical')
					{
						this.find('.dynamic-margin').css(depth_type, (100 - to) + "%");
					}
					else
					{
						this.find('.dynamic-margin').css(depth_type, from + "%");
					}
					this.find('.dynamic-range').css(depth_type, depth + "%");
					
					
					this.trigger("range-slider::everychange", [data.min + from_step, data.min + to_step]);

					if(_option)
					{
						this.trigger("range-slider::change", [data.min + from_step, data.min + to_step]);
					}
				}
			}


// creating range slider elements
// this will creat first time the elements

				var margin_range = $("<div class='dynamic-margin'></div>");
				var dynamic_range = $("<div class='dynamic-range'></div>");

				if (data_infinity == 'max')
				{
					add_selection.call(this, 'min').appendTo(dynamic_range);
				}
				else if(data_infinity == 'min')
				{
					add_selection.call(this, 'max').appendTo(dynamic_range);
				}
				else
				{
					add_selection.call(this, 'max').appendTo(dynamic_range);
					add_selection.call(this, 'min').appendTo(dynamic_range);
				}


// add support rtl class to change range slider to right_to_left on page rtl direction
// 
				if ($(this).data('data-fix-direction')===undefined) 
				{
					$(this).addClass('support-rtl')
				}

				dynamic_range.find('div.min, div.max').append("<span class='mount'></span>");
				dynamic_range.find('div.min, div.max').append("<svg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 8 9' style='enable-background:new 0 0 8 9;' xml:space='preserve'><style type='text/css'>.st1{fill:#E5A428;}</style><path class='st1' d='M3.2,0C6.7-0.1,5.4,3.5,8,4.5C5.4,5.5,6.7,9.1,3.2,9L2.7,9V7.1L0,4.5l2.7-2.6V0L3.2,0z'/></svg>");
				var my_mount = dynamic_range.find('div.min span.mount, div.max span.mount');
				$(this).find('.dynamic-range span.mount').show(); //design*********
				var data_fix_mount = $(this).data("data-fix-mount");

				$(this).data("data-max-limit-first", $(this).rangeSlider('option','max_limit')-$(this).rangeSlider('option','min'));
				my_mount.hide();
				if (data_fix_mount !== undefined)
				{
					my_mount.show();
					$(this).addClass("margin-range");
				}
				margin_range.hide();
				dynamic_range.hide();

				margin_range.appendTo(this);
				dynamic_range.appendTo(this);

				if ($(this).data("data-infinity") == 'max')
				{
					$(this).range( ($(this).rangeSlider('option', 'min_default')-$(this).rangeSlider('option', 'min')), $(this).rangeSlider('option', 'max'));
				}

				else if ($(this).data("data-infinity") == 'min')
				{
					 // $(this).range( 0, $(this).rangeSlider('option', 'max_default')-$(this).rangeSlider('option', 'min'));

					$(this).rangeSlider('option', 'set_range', 'to',{type:'ziro_unit'}, $(this).attr('data-max-default') );

				}

				else
				{
					$(this).range( ($(this).rangeSlider('option', 'min_default')-$(this).rangeSlider('option', 'min')), ($(this).rangeSlider('option', 'max_default')-$(this).rangeSlider('option', 'min')) );
				}

				// add_selection.call(this, 'min');

				margin_range.show();
				dynamic_range.show();
				$(this).trigger("range-slider::init::after");

				if ($(this).data("data-infinity") == 'max')
				{
					$(this).range( ($(this).rangeSlider('option', 'min_default')-$(this).rangeSlider('option', 'min')), $(this).rangeSlider('option', 'max'));
				}

				else if ($(this).data("data-infinity") == 'min')
				{
					$(this).rangeSlider('option', 'set_range', 'to',{type:'ziro_unit'}, $(this).attr('data-max-default') );
				}

				else
				{
					$(this).range( ($(this).rangeSlider('option', 'min_default')-$(this).rangeSlider('option', 'min')), ($(this).rangeSlider('option', 'max_default')-$(this).rangeSlider('option', 'min')) );
				}
						});
			}

// here the events for mouse and keyboard selection is set

var add_selection = function(_name)
{
	if (!$(this).data("data-infinity") && $(this).data("data-lock") == undefined)
	{
		$(this).unbind('mousemove.dynamic-range');
		$(document).unbind('mouseup.dynamic-range');

		$(this).bind('mousemove.dynamic-range', function(){
			var dynamic_range = $(this).find(".dynamic-range");
		$(dynamic_range).bind('mousedown.dynamic-range', function(){
			var _self          = $(this).parents(".range-slider");
		if ($(_self).rangeSlider('option','lock')!=1)
		{
			var mouse_position = data.type == 'vertical' ? event.pageY : event.pageX;
			var ziro_point     = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;

			if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && data.type != 'vertical') 
			{
				var _width = $(_self).width();
				var ziro_point = data.type == 'vertical'? $(_self).offset().top : _width-$(_self).offset().left;
				var mouse_position = -(mouse_position-screen.width)-1;
			}

			var ziro_on_click  = mouse_position - ziro_point;
			var range_width    = $(_self).rangeSlider('option','range_width');
			var margin         = $(_self).rangeSlider('option','margin');
			var on_click_to    = $(_self).rangeSlider('option', 'range', 'to', {type:'pixel'});
			var on_click_from  = $(_self).rangeSlider('option', 'range', 'from', {type:'pixel'});

		$(document).unbind("mousemove.dynamic-range");
		$(document).unbind('mouseup.dynamic-range');
		$(document).bind("mousemove.dynamic-range", function(event){

			$(_self).find('.dynamic-range div.min , .dynamic-range div.max').addClass("active"); //design*********
			$(_self).find('.dynamic-range span.mount').show(); //design*********
			var mouse_position    = data.type == 'vertical' ? event.pageY : event.pageX;
			var ziro_point        = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;

			if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && data.type != 'vertical')
			{
				var _width = $(_self).width();
				var ziro_point = data.type == 'vertical'? $(_self).offset().top : _width-$(_self).offset().left;
				var mouse_position = -(mouse_position-screen.width)-1;
			}

			var mouse_selection   = mouse_position - ziro_point;
			mouse_selection       = data.type == 'vertical' ? $(_self).height() - mouse_selection : mouse_selection;
			var move              = mouse_selection - ziro_on_click;
			var my_max_limit 	  = $(_self).rangeSlider('option', 'max_limit');
			if(my_max_limit)
			{
				var my_max_limit = $(_self).rangeSlider('option', 'max_limit');

				if ($(this).rangeSlider('option', 'check_json'))
				{
					my_max_limit = $(_self).data('real_max_limit');
				}
			}
			else
			{
				var my_max_limit = $(_self).rangeSlider('option', 'max');	
			}

			var total_width_unit  = my_max_limit - $(_self).rangeSlider('option', 'min');
			var total_width_pixel = $(_self).rangeSlider('option', 'unit_to_pixel', total_width_unit);

			var final_from        = margin+move;
			var final_to          = range_width+margin+move;

			if (final_to >= total_width_pixel)
			{
				final_from = total_width_pixel-range_width;
			}
			else if(final_from <= 0)
			{
				final_to  = range_width;
			}

			$(_self).rangeSlider('option', 'range', 'to',{type:'pixel'}, final_to);
			$(_self).rangeSlider('option', 'range','from',{type:'pixel'}, final_from);



			}).bind("mouseup.dynamic-range", function(){
				$(document).unbind("mousemove.dynamic-range");

				$(_self).trigger("range-slider::mouseup::margin");
				$(_self).trigger("range-slider::changeAfter");
				

				
				$(_self).find('.dynamic-range div.min , .dynamic-range div.max').removeClass("active"); //design*********
				if (data_fix_mount === undefined)
				{
					$(_self).find('.dynamic-range span.mount').hide(); //design*********
				}

				$(document).unbind("mouseup.dynamic-range");
			})
		}
			return false;
		}).bind("mouseup", function(){
			$(dynamic_range).unbind("mousedown.dynamic-range");
		});
	});

	$(document).unbind('touchend');
	$(document).unbind('touchstart');
	$(document).unbind('touchmove.dynamic-range');

	$(this).bind('touchstart',function(e){
		e.preventDefault();
		var target = $( event.target );
	if ($(this).rangeSlider('option','lock')!=1)
	{
		var _self          = $(this);
		if (target.is(".dynamic-range"))
		{
			var mouse_position = data.type == 'vertical' ? e.originalEvent.touches[0].pageY : e.originalEvent.touches[0].pageX;
			var ziro_point     = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;

			if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && data.type != 'vertical') 
			{
				var _width = $(_self).width();
				var ziro_point = data.type == 'vertical'? $(_self).offset().top : _width-$(_self).offset().left;
				var mouse_position = -(mouse_position-screen.width)-1;
			}

			var ziro_on_click  = mouse_position - ziro_point;
			var range_width    = $(_self).rangeSlider('option','range_width');
			var margin         = $(_self).rangeSlider('option','margin');
			var on_click_to    = $(_self).rangeSlider('option', 'range', 'to', {type:'pixel'});
			var on_click_from  = $(_self).rangeSlider('option', 'range', 'from', {type:'pixel'});
		}

		$(document).unbind('touchmove.dynamic-range');
		$(document).bind('touchmove.dynamic-range',function(e){
	      e.preventDefault();

		var target = $( event.target );
		if (target.is(".dynamic-range"))
		{
			$(_self).find('.dynamic-range div.min , .dynamic-range div.max').addClass("active"); //design*********
			$(_self).find('.dynamic-range span.mount').show(); //design*********
			var mouse_position  = data.type == 'vertical' ? e.originalEvent.touches[0].pageY : e.originalEvent.touches[0].pageX;
			var ziro_point      = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;

			if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && data.type != 'vertical')
			{
				var _width = $(_self).width();
				var ziro_point = data.type == 'vertical'? $(_self).offset().top : _width-$(_self).offset().left;
				var mouse_position = -(mouse_position-screen.width)-1;
			}

			var mouse_selection = mouse_position - ziro_point;
			mouse_selection     = data.type == 'vertical' ? $(_self).height() - mouse_selection : mouse_selection;
			var move            = mouse_selection - ziro_on_click;

			var my_max_limit 	  = $(_self).rangeSlider('option', 'max_limit');
			if(my_max_limit)
			{
				var my_max_limit = $(_self).rangeSlider('option', 'max_limit');

				if ($(this).rangeSlider('option', 'check_json'))
				{
					my_max_limit = $(_self).data('real_max_limit');
				}
			}
			else
			{
				var my_max_limit = $(_self).rangeSlider('option', 'max');	
			}

			var total_width_unit   = my_max_limit - $(_self).rangeSlider('option', 'min');
			var total_width_pixel  = $(_self).rangeSlider('option', 'unit_to_pixel', total_width_unit);
			var final_from = margin+move;
			var final_to   = range_width+margin+move;

			if (final_to >= total_width_pixel)
			{
				final_from = total_width_pixel-range_width;
			}
			else if(final_from <= 0)
			{
				final_to  = range_width;
			}

			$(_self).rangeSlider('option', 'range', 'to',{type:'pixel'}, final_to);
			$(_self).rangeSlider('option', 'range','from',{type:'pixel'}, final_from);
		}
		});
	}
	}).bind('touchend.dynamic-range',function(e){
			$(_self).find('.dynamic-range div.min , .dynamic-range div.max').removeClass("active"); //design*********
			if (data_fix_mount === undefined)
			{
				$(_self).find('.dynamic-range span.mount').hide(); //design*********
			}

			$(_self).trigger("range-slider::touchend::margin_touch");
			$(_self).trigger("range-slider::changeAfter");
				

			$(document).unbind('touchend');
			$(document).unbind('touchstart');
			$(document).unbind('touchmove');
		});
	}


	var data_fix_mount = $(this).data("data-fix-mount");
	var data = $(this).data('range-slider');
	var _self = this;
	var selection = $("<div class='"+_name+"'></div>");
	$(this).trigger("range-slider::selection", [selection, _name]);



	if ($(this).data("data-lock") == undefined)
	{

	selection.data('tabindex', '0');
	selection.unbind('mousedown.range-slider');

	selection.bind('mousedown.range-slider', function(){


	if ($(_self).rangeSlider('option','lock')!=1)
	{
		var _selection = this;
		var mouse_position = data.type == 'vertical' ? event.pageY : event.pageX;
		var ziro_point     = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;

		if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && data.type != 'vertical') 
		{
			var _width = $(_self).width();
			var ziro_point = data.type == 'vertical'? $(_self).offset().top : _width-$(_self).offset().left;
			var mouse_position = -(mouse_position-screen.width)-1;
		}

		var ziro_on_click  = mouse_position - ziro_point;
		var range_width    = $(_self).rangeSlider('option','range_width');
		var margin         = $(_self).rangeSlider('option','margin');

		$(document).unbind("mousemove.range-slider");
		$(document).bind("mousemove.range-slider", function(event){

			$(_self).find('.dynamic-range .'+ _name).addClass("active"); //design*********
			if (data_fix_mount === undefined)
			{
				$(_self).find('.dynamic-range .'+ _name +' span.mount').show(); //design*********
			}
			var mouse_position = data.type == 'vertical' ? event.pageY : event.pageX;
			var ziro_point = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;
			var mouse_selection = mouse_position - ziro_point;
			mouse_selection = data.type == 'vertical' ? $(_self).height() - mouse_selection : mouse_selection;

			if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && data.type != 'vertical') 
			{
				var _width = $(_self).width();
				var ziro_point = data.type == 'vertical'? $(_self).offset().top : _width-$(_self).offset().left;
				var mouse_position = -(mouse_position-screen.width)-1;
				var mouse_selection = mouse_position - ziro_point;
				mouse_selection = data.type == 'vertical' ? $(_self).height() - mouse_selection : mouse_selection;
				var move = mouse_selection - ziro_on_click;
				var final_from =margin+move;
				var final_to   =range_width+margin+move;
				mouse_selection = _name == 'min' ? final_from : final_to;
			}

			if(_name == 'max')
			{
				$(_self).rangeSlider('option', 'range', 'to',{type:'pixel'}, mouse_selection);
			}
			else if((_name == 'min'))
			{
				$(_self).rangeSlider('option', 'range','from',{type:'pixel'}, mouse_selection);
			}
		}).bind("mouseup.range-slider", function(){

			$(_self).find('.dynamic-range .'+ _name).removeClass("active"); //design*********
			if (data_fix_mount === undefined)
			{
				$(_self).find('.dynamic-range .'+ _name +' span.mount').hide(); //design*********
			}


			$(document).unbind("mousemove.range-slider");

			$(_self).trigger("range-slider::mouseup::min_max");
			$(_self).trigger("range-slider::changeAfter");
				


			$(document).unbind("mouseup.range-slider");
		});
	}
		return false;
	}).bind("mouseup", function(){
		$(document).unbind("mousemove.range-slider");
		
		
	}).bind('keydown.range-slider', function(event){

	if ($(_self).rangeSlider('option','lock')!=1)
	{
		var from, to, type;

		change_by_key = 1;
		if (event.shiftKey)
		{
			var change_by_key = 5;
		}

		if($(this).is('.max'))
		{

			if (data_fix_mount === undefined)
			{
				$(_self).find('.dynamic-range .max span.mount').show(); //design*********
			}
			if(event.keyCode == 38 || event.keyCode == 39)
			{
				if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && event.keyCode == 39)
				{
					change_by_key = change_by_key*(-1);
				}
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'to', {type:'step_plus'}, change_by_key);
				return false;
			}
			else if(event.keyCode == 37 || event.keyCode == 40)
			{
				if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && event.keyCode == 37)
				{
					change_by_key = change_by_key*(-1);
				}
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'to', {type:'step_plus'}, -change_by_key);
				return false;
			}
		}
		else
		{
			$(_self).find('.dynamic-range .min span.mount').show(); //design*********
			if(event.keyCode == 38 || event.keyCode == 39)
			{
				if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && event.keyCode == 39)
				{
					change_by_key = change_by_key*(-1);
				} 
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'from', {type:'step_plus'}, change_by_key);
				return false;
			}
			else if(event.keyCode == 37 || event.keyCode == 40)
			{
				if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && event.keyCode == 37)
				{
					change_by_key = change_by_key*(-1);
				} 
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'from', {type:'step_plus'}, -change_by_key);
				return false;
			}
		}

		if (data_fix_mount === undefined)
		{
			$(_self).find('.dynamic-range .max span.mount').hide(); //design*********
			$(_self).find('.dynamic-range .min span.mount').hide(); //design*********
		}
	}
		}).bind('keyup.range-slider',function(event){
			$(_self).trigger("range-slider::keyup");
			$(_self).trigger("range-slider::changeAfter");
		}).bind('touchmove',function(e){

	if ($(_self).rangeSlider('option','lock')!=1)
	{
		      e.preventDefault();
				$(_self).find('.dynamic-range .'+ _name).addClass("active"); //design*********
				if (data_fix_mount === undefined)
				{
					$(_self).find('.dynamic-range .'+ _name +' span.mount').show(); //design*********
				}
				var mouse_position = data.type == 'vertical' ? e.originalEvent.touches[0].pageY : e.originalEvent.touches[0].pageX;
				var ziro_point = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;

				if ($(document).find("body.rtl").hasClass('rtl') && $(_self).data('data-fix-direction')===undefined && data.type != 'vertical') 
				{
					var _width = $(_self).width();
					var ziro_point = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;
					var mouse_position = -(mouse_position-screen.width)-1;
				}

				var mouse_selection = mouse_position - ziro_point;
				mouse_selection = data.type == 'vertical' ? $(_self).height() - mouse_selection : mouse_selection;
				if(_name == 'max')
				{
					$(_self).rangeSlider('option', 'range', 'to',{type:'pixel'}, mouse_selection);
				}
				else
				{
					$(_self).rangeSlider('option', 'range','from',{type:'pixel'}, mouse_selection);
				}
	}
	}).bind('touchend',function(e){
			e.preventDefault();
			$(_self).find('.dynamic-range .'+ _name).removeClass("active"); //design*********
			if (data_fix_mount === undefined)
			{
				$(_self).find('.dynamic-range .'+ _name +' span.mount').hide(); //design*********
			}

			$(_self).trigger("range-slider::touchend::min_max_touch");
			$(_self).trigger("range-slider::changeAfter");
				


			$(document).unbind("mouseup.range-slider");
			$(document).unbind("mousemove.range-slider");
	});	

}

	return selection;
}
})(jQuery);

/**
 * [runRangeSlider description]
 * @return {[type]} [description]
 */
function runRangeSlider()
{
	$('.range-slider').rangeSlider();
}
