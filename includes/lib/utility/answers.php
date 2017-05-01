<?php
namespace lib\utility;

class answers
{

	public static $old_answer;
	public static $must_insert;
	public static $must_remove;

	public static $validation  = 'invalid';
	public static $user_verify = null;

	public static $IS_ANSWERED = [];

	use answer\money;
	use answer\access;
	use answer\delete;
	use answer\is_answered;
	use answer\save;
	use answer\update;
	use answer\validation;
}
?>