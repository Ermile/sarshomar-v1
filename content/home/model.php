<?php
namespace content\home;
use \lib\debug;

class model extends \mvc\model
{

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
		$this->redirector()->set_url($next_url)->redirect();
		return;
	}

	/**
	 * get random result from post has tag #homepage
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function random_result()
	{
		// $random_result = \lib\db\stat_polls::get_random_poll_result();
		// if(!$random_result)
		// {
			$random_result = $this->random("main");
		// }
		return ['random_result' => $random_result, 'malefemale' => $this->random()];
	}

	public function random($_type = "drilldown")
	{
		$lang = substr(\lib\router::get_storage('language'), 0, 2);

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
				$title  = 'Which team are you a fan of?';
				$male   = "male";
				$female = "female";
			}
			$random = [
				'title' => $title,
				'data' =>
				[
					[
						'name'       => $male,
						'y'          => $this->rnd(),
						'drilldown'  => 'Male'
					],
					[
						'name'       => $female,
						'y'          => $this->rnd(),
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
								['v1.0', $this->rnd()],
								['v8.0', $this->rnd()],
								['v9.0', $this->rnd()],
								['v6.0', $this->rnd()],
								['v7.0', $this->rnd()]
							]
						],
						[
						'name' => 'Female',
						'id'   => 'Female',
						'data' =>
						[
								['v40.0', $this->rnd()],
								['v41.0', $this->rnd()],
								['v42.0', $this->rnd()],
								['v39.0', $this->rnd()],
								['v36.0', $this->rnd()],
								['v30.0', $this->rnd()]
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
				'data' => json_encode(
				[
					[
						'name' => $name,
						'data' =>
						[
							$this->rnd(),
							$this->rnd(),
							$this->rnd(),
							$this->rnd(),
							$this->rnd(),
						],
					]
				], JSON_UNESCAPED_UNICODE)
			];
		}
		return $random;
	}

	public function rnd()
	{
		return rand(20,70);
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
			var_dump(\lib\db\tg_session::get());
		}
		exit;
	}
}
?>