<?php
namespace content\home;
use \lib\debug;

class model extends \mvc\model
{

	public function get_test($object)
	{
		return 1;
	}


	public function random_result()
	{
		$random_result = \lib\db\result::get_random_poll_result();
		$random_result['data'] = json_encode($random_result['data'], JSON_UNESCAPED_UNICODE);

		// $malefemale = \lib\db\result::get_random_male_female_result();
		// var_dump($malefemale);
		// var_dump($this->random());exit();
		return ['random_result' => $random_result, 'malefemale' => $this->random()];
	}


	public function random_title()
	{
		$title = [
			'تفاوت زن و مرد',
			'برابری حقوق زن و مرد',
			'اخرین اخبار در خصوص زن و مرد',
			'چرایی زن و مرد',
			'با چه وسیله ای به سفر خواهید رفت',
			'حد اقل حقوق دریافتی شما',
			'آیا تا به حال از سیستم  های فروشگاهی استفاده کرده اید'
			];
		$id = array_keys($title);
		$random_key = array_rand($id);
		return $title[$random_key];
	}


	public function random()
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

	public function rnd()
	{
		return rand(0,75);
	}
}
?>