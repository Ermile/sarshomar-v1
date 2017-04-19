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
	public static $poll_ranks_value =
	[
		// valur of member
		'member'    => [ true	, 	5	 		],
		// valur of filter
		'filter'    => [ true	, 	2	 		],
		// valur of report
		'report'    => [ false	, 	10	 		],
		// valur of vot
		'vote'      => [ true	, 	4	 		],
		// valur of like
		'like'      => [ true	, 	5	 		],
		// valur of favo
		'fav'       => [ true	, 	6	 		],
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
	public static $user_ranks_value =
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
	 * lock user if set this method for this time
	 */
	private static $userranrank_method_time =
	[
		'usespamword' =>
		[
			60 * 60 * 1,
			60 * 60 * 2,
			60 * 60 * 3,
			-1,
		],

		'reported'    =>
		[
			60 * 60 * 72,
			-1,
		],

		'wrongreport' =>
		[
			60 * 60 * 2,
			60 * 60 * 4,
			60 * 60 * 6,
			60 * 60 * 8,
		],
	];


	/**
	* the money of filters
	*/
	public static $money_filter =
	[
		'gender'           => 20,
		'marrital'         => 35,
		'graduation'       => 25,
		'degree'           => 60,
		'range'            => 40,
		'employmentstatus' => 30,


		'internetusage'    => 30,
		'course'           => 60,
		'age'              => 70,
		'country'          => 90,
		'province'         => 10,
		'city'             => 20,
		'housestatus'      => 40,
		'religion'         => 50,
		'language'         => 60,
		'industry'         => 70,
	];


	/**
	 * give 10 sarshomar for one person in member
	 *
	 * @var        integer
	 */
	public static $member_per_person = 10;


	/**
	 * price of one poll if have brandin * 10
	 *
	 * @var        integer
	 */
	public static $add_poll_brand = 10;


	/**
	 * give 1000 sarshomar for hide result of poll
	 *
	 * @var        integer
	 */
	public static $add_poll_hide_result = 1000;


	/**
	 * notify mony
	 */
	public static $notify =
	[
		'poll'         => 10,
		'survey'       => 5,
	];


	/**
	 * gift value
	 *
	 * @var        array
	 */
	private static $gift =
	[
		100    => 105,
		1000   => 1100,
		5000   => 10000,
		10000  => 13000,
		20000  => 28000,
		50000  => 75000,
		100000 => 160000,
	];
}
?>