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

	function restart()
	{
		$(this).rangeSlider('destroy');
		$(this).rangeSlider();
	}

	var optionMethod = Object();

	optionMethod.type = function(_name, _set)
	{
		var data_type = $(this).attr('data-type');

		if (_set)
		{
			var data_type = _set;
			if (data_type != 'vertical')
			{
				data_type = 'horizontal';
			}
			$(this).attr('data-type',data_type);
			return data_type;
		}

		var data_type = $(this).attr('data-type');
		if (data_type != 'vertical')
		{
			data_type = 'horizontal';
			$(this).attr('data-type',data_type);
		}
		return data_type;
	}

	optionMethod.step = function(_name, _set, _json)
	{
		var result;
		var data_step;
		var my_step;
		if (_set)
		{
			my_step = Number(_set);
			if (isNaN(my_step))
			{
				my_step = 1;
			}
			$(this).attr('data-step', my_step);
			return my_step;
		}

		var data = $(this).attr("data-step");
		try {
	    my_step = $.parseJSON(data);

		} catch (e) {
			my_step = Number(data);
			if (isNaN(my_step))
			{
				my_step = 1;
			}
			$(this).attr('data-step', my_step);
		}

		if (Array.isArray(my_step))
		{
			var jason_step = data;
			$(this).attr("save_jason", jason_step);
			var my_step = 0;
			if ($(this).attr("data-step"))
			{
				var ends = [];
				var multi_steps = JSON.parse($(this).attr("data-step"));
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
			}
		}
		return Math.round(my_step);
	}

	optionMethod.min = function(_name, _set)
	{
		if (_set)
		{
			var data_min = _set;
			var json_string = $(this).attr("save_jason");
			if (json_string)
			{
				var json_steps = jQuery.parseJSON( json_string );
				var json_steps_details = json_steps[0];
				var start  = parseInt(json_steps_details["start"]);
				data_min = start;
				$(this).attr('data-min', data_min)
			}
			
			if (isNaN(data_min))
			{
				data_min = 0;
			}

			$(this).attr('data-min',data_min);
			// console.log($(this).attr('data-min'))
		return data_min;
		}

		var data_min = parseInt($(this).attr('data-min'));
		var json_string = $(this).attr("save_jason");
		if (json_string)
		{
			$(this).attr('data-min',data_min);
			var data_min_unit = $(this).rangeSlider('option', 'multi_level_value_to_real', data_min);
			var my_step = $(this).rangeSlider('option','step');
			data_min = Math.round(my_step * data_min_unit);
		}

		if (isNaN(data_min))
		{
			data_min = 0;
			$(this).attr('data-min',data_min);
		}
// console.log(data_min)
		return data_min;
	}

	optionMethod.max = function(_name, _set)
	{
		var data_min = parseInt($(this).attr('data-min'));
		if (_set)
		{
			var data_max = _set;

			if(isNaN(data_max) || data_min >= data_max)
			{
				data_max = data_min + 100;
var json_string = $(this).attr("save_jason");
				if (json_string)
				{
					console.log(11111111111)
					// data_max = 100000;
				}
			}
			$(this).attr('data-max',data_max);
			return data_max;
		}
		var data_max = parseInt($(this).attr('data-max'));


		var my_step = 0;
		var json_string = $(this).attr("save_jason");
		if (json_string)
		{
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
		}

		if(isNaN(data_max) || data_min >= data_max)
		{
			data_max = data_min + 100;
			$(this).attr('data-max',data_max);
		}

		return data_max;
	}

	optionMethod.min_unit = function(_name, _set)
	{
		if (_set)
		{
			var data_min_unit = _set;
			if(isNaN(data_min_unit))
			{
				data_min_unit = 0;
			}
			$(this).attr('data-min-unit',data_min_unit);
			return data_min_unit;
		}

		var data_min_unit = Number($(this).attr('data-min-unit'));
		if(isNaN(data_min_unit))
		{
			data_min_unit = 0;
			$(this).attr('data-min-unit',data_min_unit);
		}
		return data_min_unit;
	}

	optionMethod.unit = function(_name, _set)
	{
		var data_unit;
		var data_max = Number($(this).attr("data-max"));
		var data_min = Number($(this).attr('data-min'));
		var data_unit = data_max - data_min;
		return data_unit;
	}

	optionMethod.min_title = function(_name, _set)
	{
		var min_title = $(this).attr("data-min-title");
		if (_set)
		{
			var min_title = _set;
			$(this).attr('data-min-title',min_title);
		}
		return min_title;
	}

	optionMethod.max_title = function(_name, _set)
	{
		var max_title = $(this).attr("data-show-title");
		if (_set)
		{
			var max_title = _set;
			$(this).attr('data-show-title',max_title);
		}
	}


	optionMethod.max_limit = function(_name, _set, _multi)
	{
		var max_limit;
		max_limit = parseInt($(this).attr("data-max-limit"));
		if(_set)
		{
			max_limit = parseInt(_set);

			if (_multi)
			{
				// agar bekhaahim ba ejraye function lhodemaan meghdaare max_limit ro taghyeer bedim bayad meghdare 3om ro set konim
				// $('#a').rangeSlider('option', 'max_limit', 500, 1);
				// 
				var json_string = $(this).attr("save_jason");
				if (json_string)
				{
					var my_step = $(this).rangeSlider('option','step');
					var real_limit_unit = $(this).rangeSlider('option','multi_level_value_to_real', _set);
					var max_limit = real_limit_unit * my_step;
				}
			}
			if(max_limit && !( max_limit <= ($(this).rangeSlider('option','min'))) )
			{
				$(this).attr("data-max-limit", max_limit);
			}
			else
			{
				$(this).removeAttr("data-max-limit");
			}


			var limit_value = max_limit - $(this).rangeSlider('option','min');

			var json_string = $(this).attr("save_jason");
			if (json_string)
			{
				var limit_value = $(this).rangeSlider('option','max_limit') - $(this).rangeSlider('option','min');
			}

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
			}
			else
			{
				$(this).find(".max_limit").css('left', limit_value_percent + "%")
			}

			var show_max_limit = max_limit;
			var json_string = $(this).attr("save_jason");
			if (json_string)
			{
				var my_step = $(this).rangeSlider('option','step');
				show_max_limit = $(this).rangeSlider('option','multi_level_real_to_value', Math.round(show_max_limit/my_step));
			}
			$(this).attr("data-max-limit-first", show_max_limit);
			$(this).find(".max_limit .mount").attr("data-value-show", $(this).attr('data-max-limit-first'));
		}

		if(isNaN(max_limit))
		{
			max_limit = undefined;
		}

		return max_limit;
	}


	optionMethod.min_default = function(_name, _set)
	{
		var _self = $(this).parents("range-slider");
		if ($(this).attr("data-infinity") != 'min')
		{
			var data_min = parseInt($(this).attr('data-min'));
			if (_set)
			{
				var data_min_default = _set;
				if(isNaN(data_min_default) || data_min > data_min_default)
				{
					data_min_default = 0;
				}
				$(this).attr('data-min-default',data_min_default);
				return data_min_default;
			}

			var data_min_default = Number($(this).attr('data-min-default'));
			if(isNaN(data_min_default) || data_min > data_min_default)
			{
				data_min_default = 0;
				$(this).attr('data-min-default',data_min_default);
			}
			return data_min_default;
		}
		else
		{
			return 0
		}
	}

	optionMethod.max_default = function(_name, _set)
	{
		if ($(this).attr("data-infinity") != 'max')
		{
			var data_max = Number($(this).attr("data-max"));
			var data_min = Number($(this).attr('data-min'));
			var data_unit = data_max - data_min;

			if (_set)
			{
				var data_max_default = _set;
				if(isNaN(data_max_default) || data_max < data_max_default)
				{
					data_max_default = data_unit;
				}
				$(this).attr('data-max-default',data_max_default);
				return data_max_default;
			}

			var data_max_default = Number($(this).attr('data-max-default'));
			if(isNaN(data_max_default) || data_max < data_max_default)
			{
				data_max_default = data_unit;
				$(this).attr('data-max-default',data_max_default);
			}
			return data_max_default;
		}
		else
		{
			return $(this).rangeSlider('option','max');
		}
	}


	optionMethod.margin = function(_name, _set)
	{
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var margin = parseInt($(this).find(".dynamic-margin").css(margin_type));
		return margin;
	}


	optionMethod.depth = function(_name, _set)
	{
		var data_max = Number($(this).attr("data-max"));
		var depth = data_max;
		return depth;
	}

	optionMethod.unit_to_pixel = function(_name, _set)
	{
		var total_unit = $(this).rangeSlider('option', 'max') - $(this).rangeSlider('option', 'min');
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var pixel_width = parseInt($(this).css(margin_type));


		var json_string = $(this).attr("save_jason");
		if (json_string)
		{
			if ($(this).rangeSlider('option', 'max_limit'))
			{
				var my_limit = $(this).rangeSlider('option', 'max_limit');
				var max_limit = $(this).rangeSlider('option', 'multi_level_value_to_real', my_limit)
			}
			else
			{
				unit_to_pixel = $(this).rangeSlider('option', 'max') - $(this).rangeSlider('option', 'min');
			}
		}
		var unit_to_pixel = parseInt((_set*pixel_width)/total_unit);


		return unit_to_pixel;
	}


	optionMethod.multi_level_value_to_real = function(_name, _set)
	{
		var json_string = $(this).attr("save_jason");
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
			return 0
		}
	}


	/**
	* [multi_level_real_to_value description]
	* @param  {[type]} _name [description]
	* @param  {[type]} _set  [description]
	* @return {[type]}       [description]
	*/
	optionMethod.multi_level_real_to_value = function(_name, _set)
	{
		var json_string = $(this).attr("save_jason");
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
	 * @param  {[type]} _name [description]
	 * @param  {[type]} _set  [description]
	 * @return {[type]}       [description]
	 */
	optionMethod.range_width = function(_name, _set)
	{
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var range_width = parseInt($(this).find(".dynamic-range").css(margin_type));
		return range_width;
	}

	optionMethod.total_width = function(_name, _set)
	{
		var margin_type = $(this).rangeSlider('option', 'type')=='vertical' ? 'height' : 'width';
		var total_width = parseInt($(this).css(margin_type));
		return total_width;
	}


	optionMethod.range = function(_name, _set, _option, _value)
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
			if ($(this).attr('data-infinity') == 'min')
			{
				return false;
			}
			else
			{
				return $(this).range(_value, null, _option);
			}
		}
	}


	function option(_name)
	{
		var _args = Array.prototype.slice.call(arguments);
		return optionMethod[_name].apply(this, _args);
	}

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
			$(this).addClass('range-slider');
			var id = $(this).attr('id');
			if(id)
			{
				$('<input type="hidden" name="'+id+'-max" data-range-bind="'+id+'" data-range-value="max">').appendTo(this);
				$('<input type="hidden" name="'+id+'-min" data-range-bind="'+id+'" data-range-value="min">').appendTo(this);
			}


			var data_infinity = $(this).attr("data-infinity");


			$(this).rangeSlider('option', 'type', $(this).attr("data-type"));
			// $(this).rangeSlider('option', 'step', $(this).attr("data-step"));
			$(this).rangeSlider('option', 'min', $(this).attr("data-min"));
			$(this).rangeSlider('option', 'max', $(this).attr("data-max"));
			$(this).rangeSlider('option', 'unit', $(this).attr("data-unit"));
			$(this).rangeSlider('option', 'min_default', $(this).attr("data-min-default"));
			$(this).rangeSlider('option', 'max_default', $(this).attr("data-max-default"));
			// $(this).rangeSlider('option', 'min_unit', $(this).attr("data-min-unit"));
			// $(this).rangeSlider('option', 'min_title', $(this).attr("data-min-title"));
			// $(this).rangeSlider('option', 'max_title', $(this).attr("data-min-title"));
			// $(this).rangeSlider('option', 'max_limit', $(this).attr("data-max-limit"));



			$(this).data('range-slider', { });

			$(this).init.prototype.range = function(_from, _to, _option){
				var from = _from;
				var to = _to;
				var data = $(this).data('range-slider');

				data.type          = this.rangeSlider('option', 'type');
				data.step          = this.rangeSlider('option', 'step');
				data.min           = this.rangeSlider('option', 'min');
				data.max           = this.rangeSlider('option', 'max');
				data.unit          = this.rangeSlider('option', 'unit');
				data.min_default   = this.rangeSlider('option', 'min_default');
				data.max_default   = this.rangeSlider('option', 'max_default');
				data.margin        = this.rangeSlider('option', 'margin');
				data.depth         = this.rangeSlider('option', 'depth');
				data.min_unit      = this.rangeSlider('option', 'min_unit');
				data.min_title     = this.rangeSlider('option', 'min_title');
				data.max_title     = this.rangeSlider('option', 'max_title');
				data.max_limit     = this.rangeSlider('option', 'max_limit');
				data.unit_to_pixel = this.rangeSlider('option', 'unit_to_pixel');
				data.range_width   = this.rangeSlider('option', 'range_width');
				data.total_width   = this.rangeSlider('option', 'total_width');
				// data.set_limit     = this.rangeSlider('option', 'set_limit');

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


				var json_string = $(this).attr("save_jason");
				if (json_string)
				{
					var my_step = $(this).rangeSlider('option','step');
					var real_limit_unit = $(this).rangeSlider('option','multi_level_value_to_real', data.max_limit);
					var real_limit = real_limit_unit * my_step;
					$(this).rangeSlider('option', 'max_limit', real_limit);
				}


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

					if ($(this).attr('data-max-limit'))
					{
						$(this).rangeSlider('option', 'max_limit', $(this).attr('data-max-limit'));
					}
					var min_unit = $(this).rangeSlider('option', 'min_unit');
					$(this).find(".dynamic-range .min .mount").attr("data-value-show", parseInt(data.min + from_step));
					$(this).find(".dynamic-range .max .mount").attr("data-value-show", parseInt(data.min + to_step));

					if ($(this).rangeSlider('option','max_limit'))
					{
						$(this).find(".max_limit .mount").attr("data-value-show", $(this).rangeSlider('option','max_limit'));
						if ($(this).attr('data-max-limit-first'))
						{
							$(this).find(".max_limit .mount").attr("data-value-show", $(this).attr('data-max-limit-first'));
						}
					}

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

				var json_string = $(this).attr("save_jason");
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
				}


				var data_value_max = $(this).find(".dynamic-range .max .mount").attr("data-value-show");
				var data_value_min = $(this).find(".dynamic-range .min .mount").attr("data-value-show");

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
									key = 'min'
								}
								else if (key == data.max && str_max ) 
								{
									var key_mount = key;
									key = 'max'
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
					this.trigger("range-slider::change", [data.min + from_step, data.min + to_step]);
				}
			}

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

				dynamic_range.find('div.min, div.max').append("<span class='mount'></span>");
				dynamic_range.find('div.min, div.max').append("<svg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px' viewBox='0 0 8 9' style='enable-background:new 0 0 8 9;' xml:space='preserve'><style type='text/css'>.st1{fill:#E5A428;}</style><path class='st1' d='M3.2,0C6.7-0.1,5.4,3.5,8,4.5C5.4,5.5,6.7,9.1,3.2,9L2.7,9V7.1L0,4.5l2.7-2.6V0L3.2,0z'/></svg>");
				var my_mount = dynamic_range.find('div.min span.mount, div.max span.mount');
				$(this).find('.dynamic-range span.mount').show(); //design*********
				var data_fix_mount = $(this).attr("data-fix-mount");


				$(this).attr("data-max-limit-first", $(this).rangeSlider('option','max_limit')-$(this).rangeSlider('option','min'));

				my_mount.hide();
				if (data_fix_mount == 'on')
				{
					my_mount.show();
					$(this).addClass("margin-range");
				}
				margin_range.hide();
				dynamic_range.hide();

				margin_range.appendTo(this);
				dynamic_range.appendTo(this);

				if ($(this).attr("data-infinity") == 'max')
				{
					$(this).range($(this).rangeSlider('option', 'min_default')-$(this).rangeSlider('option', 'min'), $(this).rangeSlider('option', 'max'));
				}
				else
				{
					$(this).range($(this).rangeSlider('option', 'min_default'), $(this).rangeSlider('option', 'max_default'));
				}

				add_selection.call(this, 'min');

				margin_range.show();
				dynamic_range.show();
							$(this).trigger("range-slider::init::after");
						});
			}

var add_selection = function(_name)
{
	if (!$(this).attr("data-infinity"))
	{
		$(this).unbind('mousemove.dynamic-range');
		$(this).bind('mousemove.dynamic-range', function(){
			var dynamic_range = $(this).find(".dynamic-range");

		$(dynamic_range).bind('mousedown.dynamic-range', function(){
			var _self          = $(this).parents(".range-slider");
			var mouse_position = data.type == 'vertical' ? event.pageY : event.pageX;
			var ziro_point     = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;
			var ziro_on_click  = mouse_position - ziro_point;
			var range_width    = $(_self).rangeSlider('option','range_width');
			var margin         = $(_self).rangeSlider('option','margin');
			var on_click_to    = $(_self).rangeSlider('option', 'range', 'to', {type:'pixel'});
			var on_click_from  = $(_self).rangeSlider('option', 'range', 'from', {type:'pixel'});

		$(document).unbind("mousemove.dynamic-range");
		$(document).bind("mousemove.dynamic-range", function(event){


			$(_self).find('.dynamic-range div.min , .dynamic-range div.max').addClass("active"); //design*********
			$(_self).find('.dynamic-range span.mount').show(); //design*********
			var mouse_position    = data.type == 'vertical' ? event.pageY : event.pageX;
			var ziro_point        = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;
			var mouse_selection   = mouse_position - ziro_point;
			mouse_selection       = data.type == 'vertical' ? $(_self).height() - mouse_selection : mouse_selection;
			var move              = mouse_selection - ziro_on_click;
			var my_max_limit 	  = $(_self).rangeSlider('option', 'max_limit');
			if(my_max_limit)
			{

				var my_max_limit = $(_self).rangeSlider('option', 'max_limit');
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

				$(_self).find('.dynamic-range div.min , .dynamic-range div.max').removeClass("active"); //design*********
				if (data_fix_mount != "on")
				{
					$(_self).find('.dynamic-range span.mount').hide(); //design*********
				}

				$(document).unbind("mouseup.dynamic-range");
				$(document).unbind("mousemove.dynamic-range");
			});
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

		var _self          = $(this);
		if (target.is(".dynamic-range"))
		{
			var mouse_position = data.type == 'vertical' ? e.originalEvent.touches[0].pageY : e.originalEvent.touches[0].pageX;
			var ziro_point     = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;
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
			var mouse_selection = mouse_position - ziro_point;
			mouse_selection     = data.type == 'vertical' ? $(_self).height() - mouse_selection : mouse_selection;
			var move            = mouse_selection - ziro_on_click;

			var my_max_limit 	  = $(_self).rangeSlider('option', 'max_limit');
			if(my_max_limit)
			{
				var my_max_limit = $(_self).rangeSlider('option', 'max_limit');
			}
			else
			{
				var my_max_limit = $(_self).rangeSlider('option', 'max');	
			}

			var total_width_unit   = my_max_limit - $(_self).rangeSlider('option', 'min');
			var total_width_pixel  = $(_self).rangeSlider('option', 'unit_to_pixel', total_width_unit);
			var final_from =margin+move;
			var final_to   =range_width+margin+move;

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

	}).bind('touchend.dynamic-range',function(e){
			$(_self).find('.dynamic-range div.min , .dynamic-range div.max').removeClass("active"); //design*********
			if (data_fix_mount != "on")
			{
				$(_self).find('.dynamic-range span.mount').hide(); //design*********
			}
			$(document).unbind('touchend');
			$(document).unbind('touchstart');
			$(document).unbind('touchmove');
		});
	}

	var data_fix_mount = $(this).attr("data-fix-mount");
	var data = $(this).data('range-slider');
	var _self = this;
	var selection = $("<div class='"+_name+"'></div>");
	$(this).trigger("range-slider::selection", [selection, _name]);
	selection.attr('tabindex', '0');
	selection.unbind('mousedown.range-slider');
	selection.bind('mousedown.range-slider', function(){
		var _selection = this;

		$(document).unbind("mousemove.range-slider");
		$(document).bind("mousemove.range-slider", function(event){

			$(_self).find('.dynamic-range .'+ _name).addClass("active"); //design*********
			if (data_fix_mount != "on")
			{
				$(_self).find('.dynamic-range .'+ _name +' span.mount').show(); //design*********
			}
			var mouse_position = data.type == 'vertical' ? event.pageY : event.pageX;
			var ziro_point = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;
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
		}).bind("mouseup.range-slider", function(){

			$(_self).find('.dynamic-range .'+ _name).removeClass("active"); //design*********
			if (data_fix_mount != "on")
			{
				$(_self).find('.dynamic-range .'+ _name +' span.mount').hide(); //design*********
			}
			$(document).unbind("mouseup.range-slider");
			$(document).unbind("mousemove.range-slider");
		});
		return false;
	}).bind("mouseup", function(){
		$(document).unbind("mousemove.range-slider");
	}).bind('keydown.range-slider', function(event){

		var from, to, type;

		change_by_key = 1;
		if (event.shiftKey)
		{
			var change_by_key = 5;
		}

		if($(this).is('.max'))
		{
			if (data_fix_mount != "on")
			{
				$(_self).find('.dynamic-range .max span.mount').show(); //design*********
			}
			if(event.keyCode == 38 || event.keyCode == 39)
			{
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'to', {type:'step_plus'}, change_by_key);
				return false;
			}
			else if(event.keyCode == 37 || event.keyCode == 40)
			{
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'to', {type:'step_plus'}, -change_by_key);
				return false;
			}
		}

		else
		{
			$(_self).find('.dynamic-range .min span.mount').show(); //design*********
			if(event.keyCode == 38 || event.keyCode == 39)
			{
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'from', {type:'step_plus'}, change_by_key);
				return false;
			}
			else if(event.keyCode == 37 || event.keyCode == 40)
			{
				$(this).parents('.range-slider').rangeSlider('option', 'range', 'from', {type:'step_plus'}, -change_by_key);
				return false;
			}
		}

		if (data_fix_mount != "on")
		{
			$(_self).find('.dynamic-range .max span.mount').hide(); //design*********
			$(_self).find('.dynamic-range .min span.mount').hide(); //design*********
		}
	}).bind('touchmove',function(e){
	      e.preventDefault();
			$(_self).find('.dynamic-range .'+ _name).addClass("active"); //design*********
			if (data_fix_mount != "on")
			{
				$(_self).find('.dynamic-range .'+ _name +' span.mount').show(); //design*********
			}
			var mouse_position = data.type == 'vertical' ? e.originalEvent.touches[0].pageY : e.originalEvent.touches[0].pageX;
			var ziro_point = data.type == 'vertical'? $(_self).offset().top : $(_self).offset().left;
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

	}).bind('touchend',function(e){
			e.preventDefault();
			$(_self).find('.dynamic-range .'+ _name).removeClass("active"); //design*********
			if (data_fix_mount != "on")
			{
				$(_self).find('.dynamic-range .'+ _name +' span.mount').hide(); //design*********
			}
			$(document).unbind("mouseup.range-slider");
			$(document).unbind("mousemove.range-slider");
	});


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
