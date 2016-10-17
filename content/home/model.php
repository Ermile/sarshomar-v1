<?php
namespace content\home;
use \lib\debug;

class model extends \mvc\model
{

	public function get_tags()
	{

	}

	/**
	 * get random result from post has tag #homepage
	 *
	 * @return     array  ( description_of_the_return_value )
	 */
	public function random_result()
	{
		$random_result = \lib\db\stat_polls::get_random_poll_result();
		if(!$random_result)
		{
			$random_result = $this->random("main");
		}
		return ['random_result' => $random_result, 'malefemale' => $this->random()];
	}


	public function random_title()
	{
		$title =
		[
			'شما به عنوان یک ایرانی مایل به روابط با ایالات متحده امریکا هستید؟',
			'آیا با تنبیه دانش‌آموزان در مدارس موافق هستید؟',
			'آیا موافق اینکه تک فرزندی وضعیت عمومی جامعه ما را تهدید می‌کند، هستید؟',
			'آیا با جراحی‌های زیبایی، مخصوصا بینی موافق هستید؟',
			'آیا با رایگان‌شدن بلیط بازی تیم محبوب‌تان موافق هستید؟',
			'آیا تاکنون در زندگی خود اشتباه بزرگ انجام داده‌اید؟',
			'آیا شما در زندگی خود فرصت‌ طلایی‌ای از دست داده‌ایدکه باعث پیشمانی شده باشد؟',
			'آیا موافق فیلترشدن شبکه‌های اجتماعی هستید؟',
			'به‌نظر شما سیستم ایمنی بدن، رفتارهای ما را کنترل می‌کند؟',
			'آیا روز اول مهر را دوست داشتید؟'
		];
		$id = array_keys($title);
		$random_key = array_rand($id);
		return $title[$random_key];
	}


	public function random($_type = "drilldown")
	{
		if($_type == "drilldown")
		{

			$random = [
				'title' => $this->random_title(),

				'data' => [
					[
						'name'       => 'مرد',
						'y'          => $this->rnd(),
						'drilldown' => 'Male'
					],
					[
						'name'       => 'زن',
						'y'          => $this->rnd(),
						'drilldown'  => 'Female'
			        ]
			    ],

				'series' => [
						[
						'name' => 'Male',
						'id'   => 'Male',
						'data' => [
								['v11.0', $this->rnd() ],
								['v8.0', $this->rnd() ],
								['v9.0', $this->rnd() ],
								['v10.0', $this->rnd() ],
								['v6.0', $this->rnd() ],
								['v7.0', $this->rnd() ]
							]
						],
						[
						'name' => 'Female',
						'id'   => 'Female',
						'data' => [
								['v40.0', $this->rnd() ],
								['v41.0', $this->rnd() ],
								['v42.0', $this->rnd() ],
								['v39.0', $this->rnd() ],
								['v36.0', $this->rnd() ],
								['v43.0', $this->rnd() ],
								['v31.0', $this->rnd() ],
								['v35.0', $this->rnd() ],
								['v38.0', $this->rnd() ],
								['v32.0', $this->rnd() ],
								['v37.0', $this->rnd() ],
								['v33.0', $this->rnd() ],
								['v34.0', $this->rnd() ],
								['v30.0', $this->rnd() ]
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
			$random = [
				'title' => $this->random_title(),
				'categories' => json_encode(['بلی', 'خیر'],JSON_UNESCAPED_UNICODE),
				'data' => json_encode([
					[
						'name' => 'پاسخ',
						'data' => [$this->rnd(), $this->rnd()]
					]
			        ], JSON_UNESCAPED_UNICODE)
				];
		}
		return $random;
	}

	public function rnd()
	{
		return rand(13,70);
	}
}
?>