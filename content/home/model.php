<?php
namespace content\home;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * Gets the random result.
	 */
	public function get_random()
	{
		$random = \lib\db\polls::get_random();
		$url = '$';
		if(isset($random['url']))
		{
			$url = $random['url'];
		}

		$this->redirector()->set_url(trim($this->url('prefix').'/'. $url, '/'))->redirect();
		debug::msg('direct', true);
		return;
	}


	/**
	 * Gets the ask.
	 * the user click on ask button
	 */
	public function get_ask()
	{
		// cehck login
		if(!$this->login())
		{
			$this->redirector(null, false)->set_domain()->set_url('login')->redirect();
			return;
		}

		$user_id  = $this->login("id");
		$next_url = \lib\db\polls::get_next_url($user_id);
		if($next_url == null)
		{
			$next_url = '$';
		}

		$this->redirector()->set_url(trim($this->url('prefix').'/'. $next_url, '/'))->redirect();
		debug::msg('direct', true);
		return;
	}


	/**
	 * get random result from post has tag #homepage
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function random_result()
	{
		$random_result = \lib\utility\stat_polls::get_random_poll_result();
		if(!$random_result || $random_result == '[]')
		{
			$random_result = $this->random("main");
		}
		return $random_result;
	}


	/**
	 * make a fake result if we have not result
	 *
	 * @param      string  $_type  The type
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public function random($_type = "drilldown")
	{
		$lang = substr(\lib\define::get_language('name'), 0, 2);

		if($_type == "drilldown")
		{
			if($lang == "fa")
			{
				$title  = 'آیا با جراحی‌های زیبایی، مخصوصا بینی موافق هستید؟';
				$male   = "مرد";
				$female = "زن";
			}
			else
			{
				$title  = T_('Which team are you a fan of?');
				$male   = T_("male");
				$female = T_("female");
			}
			$random = [
				'title' => $title,
				'data' =>
				[
					[
						'name'       => $male,
						'y'          => 145,
						'drilldown'  => 'Male'
					],
					[
						'name'       => $female,
						'y'          => 165,
						'drilldown'  => 'Female'
			        ]
			    ],

				'series' =>
				[
						[
						'name' => 'Male',
						'id'   => 'Male',
						'data' =>
						[
								['v1.0', 25],
								['v8.0', 35],
								['v9.0', 55],
								['v6.0', 95],
								['v7.0', 15]
							]
						],
						[
						'name' => 'Female',
						'id'   => 'Female',
						'data' =>
						[
								['v40.0', 35],
								['v41.0', 5],
								['v42.0', 15],
								['v39.0', 85],
								['v36.0', 55],
								['v30.0', 95]
						]
					]
				]
	        ];
	        $result = [];
	        foreach ($random as $key => $value)
	        {
	        	$result[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
	        }
       		return $result;
		}
		else
		{
			var_dump($lang);
			if($lang == "fa")
			{
				$title  = "طرفدار کدام تیم هستید؟";
				$categories =
				[
					'استقلال',
					'پرسپولیس',
					'تراکتورسازی',
					'نفت تهران',
					'سپاهان',
					'ملوان',
				];
				$name   = "تیم ها";

			}
			else
			{
				$title  = "Which team are you a fan of?";
				$categories =
				[
					'Manchester',
					'Liverpool',
					'Real Madrid',
					'Barcelona',
					'Juventus',
				];
				$name   = "teams";

			}
			$random =
			[
				'title' => $title,
				'categories' => json_encode($categories,JSON_UNESCAPED_UNICODE),
				'basic' => json_encode(
				[
					[
						'name' => $name,
						'data' =>
						[
							19,
							35,
							10,
							50,
							14,
						],
					]
				], JSON_UNESCAPED_UNICODE)
			];
		}
		return $random;
	}

	function get_tg_session($_args)
	{
		$user_id = $_args->get_url(0)[2];
		$type = $_args->get_url(0)[1];
		\lib\db\tg_session::start($user_id);
		if($type == 'json')
		{
			header('Content-Type: application/json');
			echo \lib\db\tg_session::$data_json;
		}
		else{
			echo "<pre>";
			print_r(\lib\db\tg_session::get());
		}
		exit;
	}
}
?>