<?php
namespace content\home;
use \lib\debug;

class model extends \mvc\model
{
	public function male_female_chart()
	{
		$chart = \lib\utility\stat_polls::gender_chart();
		if($chart)
		{
			return $chart;
		}
		else
		{
			return
			[
				'categories' => '["-13","14-17","18-24","25-30","31-44","45-59","60+"]',
				'series'     => '[
									{"name":"'. T_("male"). '","data":[10,20,30,40,30,20,10]},
									{"name":"'. T_("female").'","data":[-10,-20,-30,-40,-30,-20,-10]}
								]'
			];
		}
	}


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

		$this->redirector()->set_url($url);
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

		$this->redirector()->set_url($next_url);
		$this->controller->display = false;
		$this->_processor(['force_stop' => true, 'force_json' => true]);
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


		// remove Above code

		if($lang == "fa")
		{
			$random =
			[
				'title' => $title,
				'data' =>
				[
					[
					 'key' => 'استقلال',
					 'value' => 100
					],
					[
					 'key' => 'پرسپولیس',
					 'value' => 10
					],
					[
					 'key' => 'تراکتورسازی',
					 'value' => 500
					],
					[
					 'key' => 'نفت تهران',
					 'value' => 70
					],
					[
					 'key' => 'سپاهان',
					 'value' => 100
					],
					[
					 'key' => 'ملوان',
					 'value' => 60
					],
				]
			];
		}
		else
		{
			$random =
			[
				'title' => $title,
				'data' =>
				[
					[
					 'key' => 'Manchester',
					 'value' => 100,
					 "color" =>  "#e21b22",

					],
					[
					 'key' => 'Liverpool',
					 'value' => 20,
					 "color" =>  "#e26e83",
					],
					[
					 'key' => 'Real Madrid',
					 'value' => 40,
					 "color" =>  "#fdbd24",
					],
					[
					 'key' => 'Barcelona',
					 'value' => 70,
					 "color" =>  "#85063b",
					],
					[
					 'key' => 'Juventus',
					 'value' => 100,
					 "color" =>  "#13160e",
					],
					[
					 'key' => 'Bayern',
					 'value' => 60,
					 "color" =>  "#ed1248",
					],
				]
			];
		}
		$random['data'] = json_encode($random['data']);
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