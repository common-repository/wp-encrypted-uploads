<?php

namespace ANCENC;

use Auryn\Injector;

class DicLoader{
	/** @var DicLoader */
	private static $instance;

	/** @var \Auryn\Injector */
	private $dic;


	/**
	 * @returns DicLoader
	 */
	public static function get_instance() {
		return self::$instance = self::$instance ?: new self();
	}

	public function __construct() {
		$this->dic = new Injector();
	}

	/**
	 * @return \Auryn\Injector
	 */
	public function get_dic() {
		return $this->dic;
	}

}

// Routes
require_once( ANCENC_PATH . '/server/routes.php' );

