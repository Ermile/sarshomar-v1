<?php
namespace lib\utility;
/**
 * setting of money
 */
trait money
{
	/**
	 * the rank poll ranks value
	 *
	 * @var        array
	 */
	private static $poll_ranks_value =
	[
		// valur of member
		'member'    => [ true	, 	5	 		],
		// valur of filter
		'filter'    => [ true	, 	2	 		],
		// valur of report
		'report'    => [ false	, 	10	 		],
		// valur of vot
		'vot'       => [ true	, 	4	 		],
		// valur of like
		'like'      => [ true	, 	5	 		],
		// valur of faiv
		'faiv'      => [ true	, 	6	 		],
		// valur of skip
		'skip'      => [ false	, 	1	 		],
		// valur of comment
		'comment'   => [ true	, 	8	 		],
		// valur of view
		'view'      => [ true	, 	1	 		],
		// valur of other
		'other'     => [ true	, 	10	 		],
		// valur of sarshomar
		'sarshomar' => [ true	, 	100 * 1000	],
		// valur of ago
		'ago'       => [ true	, 	3	  		],
		// valur of vip
		'vip'       => [ true 	, 	4			],
		// valur of public
		'public'    => [ true 	, 	4			],
		// valur of admin
		'admin'     => [ true 	, 	4			],
		// valur of money
		'money'     => [ true 	, 	4			],
		// valur of ad
		'ad'        => [ true 	, 	4			],
	];


	/**
	 * the userranrank user ranks value
	 *
	 * @var        array
	 */
	private static $user_ranks_value =
	[
		// the user was reported
		'reported'       => [ false,	100			],
		// user use spam words
		'usespamword'    => [ false,	5			],
		// user change the profile
		'changeprofile'  => [ false,	5			],
		// user improve her profile
		'improveprofile' => [ true,		5			],
		// the user report a poll
		'report'         => [ true,		5			],
		// the report poll of the user is wrong
		'wrongreport'    => [ false,	5			],
		// user skip the poll
		'skip'           => [ false,	5			],
		// user forger her password
		'resetpassword'  => [ false,	5			],
		// account of user is verification (bit 0|1)
		'verification'   => [ true,		50			],
		// the user is valid user (bit 0|1)
		'validation'     => [ true,		5			],
		// the VIP users
		'vip'            => [ true,		100 * 100	],
		// hated <> vip , nobody user, not vip user
		'hated'          => [ false,	5			],
		// the other
		'other'          => [ true,		5			],

	];

	/**
	* the money of filters
	*/
	private static $money_filter =
	[
		'gender'           => 10,
		'marrital'         => 20,
		'internetusage'    => 30,
		'graduation'       => 40,
		'degree'           => 50,
		'course'           => 60,
		'age'              => 70,
		'range'            => 80,
		'country'          => 90,
		'province'         => 10,
		'city'             => 20,
		'employmentstatus' => 30,
		'housestatus'      => 40,
		'religion'         => 50,
		'language'         => 60,
		'industry'         => 70,
	];
}
?>