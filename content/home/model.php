<?php
namespace content\home;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{

	public $poll_code       = null;
	public $poll_id         = null;
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
		\lib\db\logs::set('user:request:random', $this->login('id'), ['meta'=>['login' => $this->login('all'), 'url' => $url]]);

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
			\lib\db\logs::set('user:request:ask:notlogin');
			$this->redirector(null, false)->set_domain()->set_url('login')->redirect();
			return;
		}

		$user_id  = $this->login("id");
		$ask_url = \lib\db\polls::ask_me($user_id);
		if($ask_url == null)
		{
			$_SESSION['user_answered_to_all_poll'] = true;
			$ask_url = '$';
		}
		$this->redirector()->set_url($ask_url);
		$this->controller->display = false;
		debug::msg("redirect", true);
		$this->_processor(['force_stop' => true, 'force_json' => true]);
	}


	/**
	 * Gets the ask.
	 * the user click on ask button
	 */
	public function get_next()
	{
		// cehck login
		if(!$this->login())
		{
			\lib\db\logs::set('user:request:next:notlogin');
			$this->redirector(null, false)->set_domain()->set_url('login')->redirect();
			return;
		}

		$user_id         = $this->login("id");
		$current         = utility::get('current');
		$current_post_id = $this->check_url(true, $current);

		if($this->poll_id)
		{
			if(utility\answers::is_answered($user_id, $this->poll_id))
			{
				$next_url = \lib\db\polls::get_next_url($user_id, $this->poll_id);
			}
			else
			{
				$next_url = \lib\db\polls::get_next_url($user_id);
			}
		}
		else
		{
			$next_url = \lib\db\polls::get_next_url($user_id);
		}
		\lib\db\logs::set('user:request:next', $this->login('id'), ['meta'=>['login' => $this->login('all'), 'current' => $current, 'current_pol' => $current_post_id, 'url' => $next_url]]);

		if($next_url == null || $next_url == utility::get('current'))
		{
			\lib\db\logs::set('user:request:next', $this->login('id'), ['meta'=>['login' => $this->login('all'), 'current' => $current, 'current_pol' => $current_post_id, 'url' => $next_url]]);
			return $this->get_ask();
		}

		$this->redirector()->set_url($next_url);
		$this->controller->display = false;
		debug::msg('redirect', true);
		$this->_processor(['force_stop' => true, 'force_json' => true]);
	}


	/**
	 * Gets the ask.
	 * the user click on ask button
	 */
	public function get_prev()
	{
		// cehck login
		if(!$this->login())
		{
			\lib\db\logs::set('user:request:prev:notlogin');
			$this->redirector(null, false)->set_domain()->set_url('login')->redirect();
			return;
		}

		\lib\db\logs::set('user:request:prev', $this->login('id'), ['meta'=>['login' => $this->login('all')]]);
		$user_id         = $this->login("id");
		$current         = utility::get('current');
		$current_post_id = $this->check_url(true, $current);

		if($this->poll_id)
		{
			if(utility\answers::is_answered($user_id, $this->poll_id))
			{
				$prev_url = \lib\db\polls::get_previous_url($user_id, $this->poll_id);
			}
			else
			{
				$prev_url = \lib\db\polls::get_previous_url($user_id);
			}
		}
		else
		{
			$prev_url = \lib\db\polls::get_previous_url($user_id);
		}

		if($prev_url == null)
		{
			$prev_url = '$';
		}

		\lib\db\logs::set('user:request:prev', $this->login('id'), ['meta'=>['login' => $this->login('all'), 'current' => $current, 'current_pol' => $current_post_id, 'url' => $prev_url]]);
		$this->redirector()->set_url($prev_url);
		$this->controller->display = false;
		debug::msg('redirect', true);
		$this->_processor(['force_stop' => true, 'force_json' => true]);
	}


	/**
	 * { function_description }
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function check_url($_return = false, $_url = null)
	{

		if($_url === null)
		{
			$url     = \lib\router::get_url();
		}
		else
		{
			$url = $_url;
		}

		$poll_id = null;
		if(preg_match("/^sp\_([". utility\shortURL::ALPHABET. "]+)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}
		elseif(preg_match("/^\\$([". utility\shortURL::ALPHABET. "]+)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}
		elseif(preg_match("/^\\$\/([". utility\shortURL::ALPHABET. "]+)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}
		elseif(preg_match("/^.*\/([". utility\shortURL::ALPHABET. "]+)$/", $url, $code))
		{
			if(isset($code[1]))
			{
				$poll_id = $code[1];
			}
		}

		if($poll_id)
		{
			$this->poll_code = $poll_id;
			$this->poll_id = \lib\utility\shortURL::decode($poll_id);
		}
		else
		{
			if($_return)
			{
				return false;
			}
			else
			{
				\lib\error::page(T_("Poll not found"));
			}
		}

		if($_return)
		{
			return $poll_id;
		}

		$poll = \lib\db\polls::get_poll($this->poll_id);

		if(!$this->access('admin'))
		{
			if(isset($poll['privacy']))
			{
				if($poll['privacy'] === 'public')
				{
					// no problem to load poll
				}
				else
				{
					if(isset($poll['status']) && in_array($poll['status'], $this->controller()::$poll_status_404))
					{
						if(!$this->access('admin:admin'))
						{
							\lib\error::page(T_("Poll not found"));
						}
					}

					if(!$this->login())
					{
						\lib\error::access(T_("Can not access to load this poll"));
					}

					// if(isset($poll['user_id']) && intval($poll['user_id']) === intval($this->login('id')))
					// {
					// 	// no problem to load poll
					// }
					// else
					// {
					// 	\lib\error::access(T_("Can not access to load this poll"));
					// }
				}
			}
		}

		if(isset($poll['url']))
		{
			if($poll['url'] != $url .'/' && $poll['url'] != $url)
			{
				$language = null;
				if(isset($poll['language']))
				{
					$language = \lib\define::get_current_language_string($poll['language']);
				}
				$post_url = $poll['url'];

				$new_url = trim($this->url('root'). $language. '/'. $post_url, '/');

				$this->redirector($new_url)->redirect();
			}
			else
			{
				return $poll;
			}
		}
		else
		{
			\lib\error::page(T_("Poll not found"));
		}
	}


	public function male_female_chart()
	{
		$chart = \lib\utility\stat_polls::gender_chart();
		if($chart)
		{

			$chart['data'] = json_encode($chart);
			return $chart;
		}
		else
		{
			// return
			// [
			// 	'categories' => '["-13","14-17","18-24","25-30","31-44","45-59","60+"]',
			// 	'series'     => '[
			// 						{"name":"'. T_("male"). '","data":[10,20,30,40,30,20,10]},
			// 						{"name":"'. T_("female").'","data":[-10,-20,-30,-40,-30,-20,-10]}
			// 					]'
			// ];

			$data=
			[
				[
					"key" => "60+",
					"male" => -1.1,
					"female" => 1.3
				],
				[
					"key" => "50-59",
					"male" => -3.2,
					"female" => 4.5
				],
				[
					"key" => "45-49",
					"male" => -2.8,
					"female" => 3.0
				],
				[
					"key" => "40-44",
					"male" => -4.4,
					"female" => 3.6
				],
				[
					"key" => "35-39",
					"male" => -4.2,
					"female" => 5.1
				],
				[
					"key" => "30-34",
					"male" => -5.2,
					"female" => 4.8
				],
				[
					"key" => "25-29",
					"male" => -7.6,
					"female" => 6.1
				],
				[
					"key" => "20-24",
					"male" => -6.1,
					"female" => 5.1
				],
				[
					"key" => "15-19",
					"male" => -3.8,
					"female" => 3.8
				],
				[
					"key" => "-13",
					"male" => -4.2,
					"female" => 2.4
				],
			];




			$chart['data'] = json_encode($data);
			return $chart;
		}
	}


	/**
	 * get random result from post has tag #homepage
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function random_result()
	{
		$random_result = [];
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

		// if($_type == "drilldown")
		// {
		// 	if($lang == "fa")
		// 	{
		// 		$title  = 'آیا با جراحی‌های زیبایی، مخصوصا بینی موافق هستید؟';
		// 		$male   = "مرد";
		// 		$female = "زن";
		// 	}
		// 	else
		// 	{
		// 		$title  = T_('Which team are you a fan of?');
		// 		$male   = T_("male");
		// 		$female = T_("female");
		// 	}
		// 	$random = [
		// 		'title' => $title,
		// 		'data' =>
		// 		[
		// 			[
		// 				'name'       => $male,
		// 				'y'          => 145,
		// 				'drilldown'  => 'Male'
		// 			],
		// 			[
		// 				'name'       => $female,
		// 				'y'          => 165,
		// 				'drilldown'  => 'Female'
		// 	        ]
		// 	    ],

		// 		'series' =>
		// 		[
		// 				[
		// 				'name' => 'Male',
		// 				'id'   => 'Male',
		// 				'data' =>
		// 				[
		// 						['v1.0', 25],
		// 						['v8.0', 35],
		// 						['v9.0', 55],
		// 						['v6.0', 95],
		// 						['v7.0', 15]
		// 					]
		// 				],
		// 				[
		// 				'name' => 'Female',
		// 				'id'   => 'Female',
		// 				'data' =>
		// 				[
		// 						['v40.0', 35],
		// 						['v41.0', 5],
		// 						['v42.0', 15],
		// 						['v39.0', 85],
		// 						['v36.0', 55],
		// 						['v30.0', 95]
		// 				]
		// 			]
		// 		]
	 //        ];
	 //        $result = [];
	 //        foreach ($random as $key => $value)
	 //        {
	 //        	$result[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
	 //        }
  //      		return $result;
		// }
		// else
		// {
		// 	if($lang == "fa")
		// 	{
		// 		$title  = "طرفدار کدام تیم هستید؟";
		// 		$categories =
		// 		[
		// 			'استقلال',
		// 			'پرسپولیس',
		// 			'تراکتورسازی',
		// 			'نفت تهران',
		// 			'سپاهان',
		// 			'ملوان',
		// 		];
		// 		$name   = "تیم ها";

		// 	}
		// 	else
		// 	{
		// 		$title  = "Which team are you a fan of?";
		// 		$categories =
		// 		[
		// 			'Manchester',
		// 			'Liverpool',
		// 			'Real Madrid',
		// 			'Barcelona',
		// 			'Juventus',
		// 		];
		// 		$name   = "teams";

		// 	}
		// 	$random =
		// 	[
		// 		'title' => $title,
		// 		'categories' => json_encode($categories,JSON_UNESCAPED_UNICODE),
		// 		'basic' => json_encode(
		// 		[
		// 			[
		// 				'name' => $name,
		// 				'data' =>
		// 				[
		// 					19,
		// 					35,
		// 					10,
		// 					50,
		// 					14,
		// 				],
		// 			]
		// 		], JSON_UNESCAPED_UNICODE)
		// 	];
		// }


		// remove Above code

		if($lang == "fa")
		{
			$random =
			[
				'title' => 'رای شما به کدام کاندید انتخابات ریاست جمهوری ۹۶ خواهد بود؟',
				'data' =>
				[
					[
					 'key' => '1',
					 'title' => 'سید مصطفی میرسلیم',
					 // "color" =>  "#ff0e17",
					 'value' => 40,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/persepolis.png'
					],
					[
					 'key' => '2',
					 'title' => 'اسحاق جهانگیری',
					 // "color" =>  "#3687c8",
					 'value' => 20,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/esteghlal.png'
					],
					[
					 'key' => '3',
					 'title' => 'حسن روحانی',
					 // "color" =>  "#ee2424",
					 'value' => 100,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/tractorsazi.png'
					],
					[
					 'key' => '4',
					 'title' => 'سید ابراهیم رئیسی',
					 // "color" =>  "#ffcc00",
					 'value' => 110,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/sepahan.png'
					],
					[
					 'key' => '5',
					 'title' => 'محمد باقر قالیباف',
					 // "color" =>  "#ee2424",
					 'value' => 80,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/tractorsazi.png'
					],
					[
					 'key' => '6',
					 'title' => 'سید مصطفی هاشمی‌طبا',
					 // "color" =>  "#666",
					 'value' => 10,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/league.png'
					],
					[
					 'key' => '7',
					 'title' => 'هیچکدام، رای باطل',
					 // "color" =>  "#ee2424",
					 'value' => 30,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/tractorsazi.png'
					],
					[
					 'key' => '7',
					 'title' => 'رای نمی‌دهم',
					 // "color" =>  "#ee2424",
					 'value' => 90,
					 // 'bullet' => $this->view()->url->static.'images/chart/iran/tractorsazi.png'
					],
				]
			];
		}
		else
		{
			$random =
			[
				'title' => T_('Which team are you a fan of?'),
				'data' =>
				[
					[
					 'key' => 'Manchester',
					 'title' => 'Manchester',
					 'value' => 100,
					 // "color" =>  "#e21b22",
					 // 'bullet' => $this->view()->url->static.'images/chart/foreign/manutd.png'

					],
					[
					 'key' => 'Liverpool',
					 'title' => 'Liverpool',
					 'value' => 20,
					 // "color" =>  "#e26e83",
					 // 'bullet' => $this->view()->url->static.'images/chart/foreign/liverpool.png'
					],
					[
					 'key' => 'Real Madrid',
					 'title' => 'Real Madrid',
					 'value' => 40,
					 // "color" =>  "#fdbd24",
					 // 'bullet' => $this->view()->url->static.'images/chart/foreign/realmadrid.png'
					],
					[
					 'key' => 'Barcelona',
					 'title' => 'Barcelona',
					 'value' => 70,
					 // "color" =>  "#85063b",
					 // 'bullet' => $this->view()->url->static.'images/chart/foreign/barcelona.png'
					],
					[
					 'key' => 'Juventus',
					 'title' => 'Juventus',
					 'value' => 100,
					 // "color" =>  "#13160e",
					 // 'bullet' => $this->view()->url->static.'images/chart/foreign/juventus.png'
					],
					[
					 'key' => 'Bayern',
					 'title' => 'Bayern',
					 'value' => 60,
					 // "color" =>  "#ed1248",
					 // 'bullet' => $this->view()->url->static.'images/chart/foreign/bayern.png'
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