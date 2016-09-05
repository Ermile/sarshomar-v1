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
		$query = "
				SELECT
					id
				FROM
					posts
				WHERE
					post_type LIKE 'poll%' AND
					post_status = 'publish'
					";
		$get_id = array_column(\lib\db\posts::select($query, "get"), "id");
		if(!empty($get_id)){

			$random_key = array_rand($get_id);
			$result = json_encode(\lib\db\polls::get_result($get_id[$random_key]), JSON_UNESCAPED_UNICODE);

		}else{
			$result = [];
		}

		$malefemale = $this->random();

		return ['random_result' => $result, 'malefemale' => $malefemale];
	}


	public function random(){
	$x = [
			'title' => 'تفاوت زن و مرد ',

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

       return json_encode($x, JSON_UNESCAPED_UNICODE);

	}

	public function rnd(){
		return rand(0,251);
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