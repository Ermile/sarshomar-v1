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


	/**
	 * notify mony
	 */
	private static $notify =
	[
		'poll'         => 10,
		'survey'       => 5,
	];


	/**
	 * branding money
	 *
	 * @var        integer
	 */
	private static $branding = 100;


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


	/**
	 * the gift tilt
	 * process by self::gift_tilt()
	 */
	private static $gift_tilt = [];


	/**
	 * process gift tilt
	 * tilt[=M] in math
	 * y
	 * |
	 * |
	 * |        _/
	 * |     b_/
	 * |   a_/
	 * | __/
	 * |/______________ x
	 * 0
	 * ********
	 * M (a --> b) = (yb - ya) / (xb - xa);
	 * y - ya = M ( x - xa)
	 * ********
	 * lenght of [a --> b] = sqr((xb-xa)^2 + (yb-ya)^2)
	 * ********
	 *
	 */
	private static function gift_tilt()
	{
		$end = end(self::$gift);

		foreach (self::$gift as $x => $y)
		{
			if($y === $end)
			{
				break;
			}

			$next = next(self::$gift);
			$x1 = $x;
			$x2 = array_search($next, self::$gift);

			$y1 = $y;
			$y2 = $next;

			$tilt = 0;
			if(($x2 - $x1) > 0)
			{
				$tilt = ($y2 - $y1) / ($x2 - $x1);
			}

			$tilt = round($tilt, 5);
			self::$gift_tilt[$x1] = $tilt;
		}

		$y2 = end(self::$gift);
		$y1 = prev(self::$gift);

		$x1 = array_search($y1, self::$gift);
		$x2 = array_search($y2, self::$gift);

		$tilt = 0;
		if(($x2 - $x1) > 0)
		{
			$tilt = ($y2 - $y1) / ($x2 - $x1);
		}

		$tilt = round($tilt, 5);
		self::$gift_tilt[$x2] = $tilt;
	}


	/**
	 * process gift tilt
	 * tilt[=M] in math
	 * y
	 * |
	 * |
	 * |        _/
	 * |     b_/
	 * |   a_/
	 * | __/
	 * |/______________ x
	 * 0
	 * ********
	 * M (a --> b) = (yb - ya) / (xb - xa);
	 * y - ya = M ( x - xa)
	 * ********
	 * lenght of [a --> b] = sqr((xb-xa)^2 + (yb-ya)^2)
	 * ********
	 *
	 * @param      <type>  $_money  The money
	 */
	public static function gift($_money)
	{
		$_money = floatval($_money);

		if(empty(self::$gift_tilt))
		{
			self::gift_tilt();
		}

		$prev_tilt = 1;
		reset(self::$gift);
		$start = key(self::$gift);
		$x1    = end(self::$gift);
		$y1    = array_search($x1, self::$gift);
		$end   = $y1;
		$y     = $_money;

		foreach (self::$gift_tilt as $money => $tilt)
		{
			if(($_money < $money || $money >= $end) && $_money >= $start)
			{

				$y = $prev_tilt * ($_money - $x1) + $y1;
				break;
			}
			$x1        = $money;
			$y1        = self::$gift[$money];
			$prev_tilt = $tilt;
		}
		return $y;
	}
}
?>