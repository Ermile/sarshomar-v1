<?php
namespace content\home;
use \lib\debug;

class model extends \mvc\model
{
	public function get_test($object)
	{
		return 1;
	}

	public function random_result() {
		// $query = "
		// 		SELECT
		// 			id
		// 		FROM
		// 			posts
		// 		WHERE
		// 			post_type LIKE 'poll%' AND
		// 			post_status = 'publish'
		// 			";
		// $get_id = array_column(\lib\db\posts::select($query, "get"), "id");
		// if(!empty($get_id)){

		// 	$random_key = array_rand($get_id);
		// 	$result = json_encode(\lib\db\polls::get_result($get_id[$random_key]), JSON_UNESCAPED_UNICODE);

		// }else{
		// }

		$data = [
			'title' => $this->random_title(),
			'data' => [
					['name' => 'جواب اول',
					'data' => [$this->rnd()]
					],
					['name' => 'جواب دوم',
					'data' => [$this->rnd()]
					],
					['name' => 'جواب سوم',
					'data' => [$this->rnd()]
					],
					['name' => 'جواب چهارم',
					'data' => [$this->rnd()]
					]
					]
			];
		$result = [];
		foreach ($data as $key => $value) {
			$result[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
		}

		$malefemale = $this->random();

		return ['random_result' => $result, 'malefemale' => $malefemale];
	}

	public function random_title(){
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
	public function random(){
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
        foreach ($random as $key => $value) {
        	$result[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        return $result;
	}

	public function rnd(){
		return rand(0,75);
	}

	public function put_test($object)
	{
		return 3;
	}

	public function delete_test($object)
	{
		return 4;
	}

}
?>