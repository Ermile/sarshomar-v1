<?php
namespace lib\utility;

class gift
{
	use \lib\utility\money;
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

		reset(self::$gift);
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
		$start     = key(self::$gift);
		$x1        = end(self::$gift);
		$end_gift  = $x1;
		reset(self::$gift);
		$y1        = array_search($x1, self::$gift);
		$end       = $y1;
		$y         = $_money;

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