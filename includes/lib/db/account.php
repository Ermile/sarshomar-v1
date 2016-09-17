<?php
namespace lib\db;

class account
{

	/**
	 * Gets the account data.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function get_account_data($_args)
	{
		if(isset($_args['user_id']))
		{
			$user_id = $_args['user_id'];
		}
		else
		{
			\lib\debug::error(T_("user not found"));
			$user_id = 0;
		}

		$query =
		"
			SELECT
				options.option_key 		AS 'key',
				options.option_value 	AS 'value',
				terms.term_title,
				terms.term_url
			FROM
				options
			LEFT JOIN terms ON
					terms.id = options.option_value AND
					options.option_cat = 'favorites'
			WHERE
				options.option_cat = 'user_detail_$user_id'
		";

		$result = \lib\db::get($query, ['key', 'value']);

		return $result;
	}


	public static function set_account_data($_args)
	{

		$user_id           = $_args['user_id'];
		$displayname       = $_args['displayname'];
		$mobile            = $_args['mobile'];
		// options record
		$gender            = $_args['gender'];
		$marrital_status   = $_args['marrital_status'];
		$parental_status   = $_args['parental_status'];
		$exercise_habits   = $_args['exercise_habits'];
		$employment_status = $_args['employment_status'];
		$business_owner    = $_args['business_owner'];
		$industry          = $_args['industry'];
		$devices_owned     = $_args['devices_owned'];
		$internet_usage    = $_args['internet_usage'];
		$birthdate         = $_args['birthdate'];
		$graduation        = $_args['graduation'];
		$course            = $_args['course'];
		$country_birth     = $_args['country_birth'];
		$country           = $_args['country'];
		$province_birth    = $_args['province_birth'];
		$province          = $_args['province'];
		$birthcity         = $_args['birthcity'];
		$city              = $_args['city'];
		$favorites         = $_args['favorites'];
		$language          = $_args['language'];
		// process age and range
		$age               = $_args['age'];
		$range             = $_args['range'];

		$query =
		"
			INSERT INTO
				options
				(post_id, 	user_id, 		 option_cat, 					option_key, 			 option_value			)
				VALUES
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'gender',				 '$gender'				),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'marrital_status',		 '$marrital_status'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'parental_status',		 '$parental_status'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'exercise_habits',		 '$exercise_habits'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'employment_status',	 '$employment_status'	),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'business_owner',		 '$business_owner'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'industry',				 '$industry'			),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'devices_owned',		 '$devices_owned'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'internet_usage',		 '$internet_usage'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'birthdate',			 '$birthdate'			),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'age',				 	 '$age'					),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'range',				 '$range'				),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'graduation',			 '$graduation'			),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'course',				 '$course'				),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'country_birth',		 '$country_birth'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'country',				 '$country'				),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'province_birth',		 '$province_birth'		),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'province',				 '$province'			),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'birthcity',			 '$birthcity'			),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'city',					 '$city'				),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'favorites',			 '$favorites'			),
				(NULL, 		'$user_id',		 'user_detail_$user_id', 		'language',				 '$language'			)
			ON DUPLICATE KEY UPDATE
				options.option_value =
					CASE
						WHEN options.option_key = 'gender' 				THEN '$gender'
						WHEN options.option_key = 'marrital_status' 	THEN '$marrital_status'
						WHEN options.option_key = 'parental_status' 	THEN '$parental_status'
						WHEN options.option_key = 'exercise_habits' 	THEN '$exercise_habits'
						WHEN options.option_key = 'employment_status' 	THEN '$employment_status'
						WHEN options.option_key = 'business_owner' 		THEN '$business_owner'
						WHEN options.option_key = 'industry' 			THEN '$industry'
						WHEN options.option_key = 'devices_owned' 		THEN '$devices_owned'
						WHEN options.option_key = 'internet_usage' 		THEN '$internet_usage'
						WHEN options.option_key = 'birthdate' 			tHEN '$birthdate'
						WHEN options.option_key = 'age' 				THEN '$age'
						WHEN options.option_key = 'range' 				THEN '$range'
						WHEN options.option_key = 'graduation' 			THEN '$graduation'
						WHEN options.option_key = 'course' 				THEN '$course'
						WHEN options.option_key = 'country_birth' 		THEN '$country_birth'
						WHEN options.option_key = 'country' 			THEN '$country'
						WHEN options.option_key = 'province_birth' 		THEN '$province_birth'
						WHEN options.option_key = 'province' 			THEN '$province'
						WHEN options.option_key = 'birthcity' 			THEN '$birthcity'
						WHEN options.option_key = 'city' 				THEN '$city'
						WHEN options.option_key = 'favorites' 			THEN '$favorites'
						WHEN options.option_key = 'language' 			THEN '$language'
					END
		";
		$result = \lib\db::query($query);
		return $result;
	}
}
?>