<?php

namespace ANCENC\Helpers;

class Logger {
	private static $errors = [];

	public static function error( $message ) {
		self::$errors[] = $message;
	}
}