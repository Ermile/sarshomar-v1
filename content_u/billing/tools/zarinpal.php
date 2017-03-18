<?php
namespace content_u\billing\tools;

trait zarinpal
{
	/**
	 * zarinpay method
	 *
	 * @var        array
	 */
	private static $zarinpal =
	[
		'MerchantID'  => "669de6b4-f744-11e6-9180-005056a205be",
		'Description' => "Sarshomar",
		'CallbackURL' => 'https://sarshomar.com/@/billing/verify/zarinpal',
		'Email'       => null,
		'Mobile'      => null,
		'Amount'      => null,
	];


}
?>