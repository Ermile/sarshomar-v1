<?php
namespace content_admin\home;

class controller extends \content_admin\main\controller
{

	/**
	 * rout
	 */
	function _route()
	{
		parent::_route();

		if(!$this->access('admin'))
		{
			// \lib\error::access(T_("Access denied"));
		}

		if(\lib\utility::get('refresh_all_chart') == 'refresh_all_chart')
		{
			\lib\utility\stat_polls::refresh_all();
			if(\lib\debug::$status)
			{
				\lib\debug::true(T_("All chart data refreshed"));
			}
			else
			{
				\lib\debug::error(T_("Error in refresh chart data"));
			}
		}

		if(\lib\utility::get('refresh_chart') && preg_match("/^[". SHORTURL_ALPHABET ."]+$/", \lib\utility::get('refresh_chart')))
		{

			\lib\utility\stat_polls::refresh(\lib\utility\shortURL::decode(\lib\utility::get('refresh_chart')));
			if(\lib\debug::$status)
			{
				\lib\debug::true(T_("Poll chart data refreshed"));
			}
			else
			{
				\lib\debug::error(T_("Error in refresh chart data of this poll"));
			}
		}


		if(\lib\utility::get('insert_province'))
		{
			$province_list = \lib\utility\location\provinces::$data;
			$insert_terms = [];
			foreach ($province_list as $key => $value)
			{
				$key_slug = \lib\utility\filter::slug($key, false);
				$insert_terms[] =
				[
					'term_language' => null,
					'term_type'     => 'sarshomar',
					'term_caller'   => 'province:'. $value['id'],
					'term_title'    => $key,
					'term_slug'     => $key_slug,
					'term_url'      => '$/general/location/iran/'. $key_slug,
					'term_desc'     => null,
					'term_meta'     => "{\"translate\":{\"en\":\"general:location:$key_slug\", \"fa\":\"عمومی:آدرس:$value[localname]\"}}",
					'term_parent'   => 100031,

				];
			}

			$insert_terms[] =
			[
				'term_language' => null,
				'term_type'     => 'sarshomar',
				'term_caller'   => 'country:not-iran',
				'term_title'    => 'country is not iran',
				'term_slug'     => 'not-iran',
				'term_url'      => '$/general/location/not-iran',
				'term_desc'     => null,
				'term_meta'     => "{\"translate\":{\"en\":\"general:location:not-iran\", \"fa\":\"عمومی:آدرس:غیر ایران\"}}",
				'term_parent'   => null,

			];

			if(\lib\utility::get('run'))
			{
				\lib\db\terms::update(['term_caller' => 'country:107'], 100031);
				var_dump(\lib\db\terms::insert_multi($insert_terms));
			}
			else
			{
				var_dump($insert_terms);
			}

			exit();
		}
	}
}
?>