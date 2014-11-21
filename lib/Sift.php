<?php
abstract class Sift
{
	/**
	 *	@var string the API key to be used for requests if none is provided to the constructor.
	 */
	public static $apiKey = null;

	/**
	 *	@var string the partnership id to use if none is specified to the constructor.
	 */
	public static $partnerId = null;

	const VERSION = '0.0.2';

	public static function setApiKey($apiKey)
	{
		self::$apiKey = $apiKey;
	}

	public static function setPartnerId($partnerId)
	{
		self::$partnerId = $partnerId;
	}
}