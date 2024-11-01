<?php

namespace ANCENC\UI;

class Renderer {
	private $engine;

	public function __construct() {
		$this->engine = new \Mustache_Engine( array(
			'template_class_prefix' => '__ANCENC_Mustache_',
			'entity_flags'          => ENT_QUOTES,
			'loader'                => new \Mustache_Loader_FilesystemLoader( ANCENC_PATH . '/static/views' ),
		) );
	}

	public function render( $page, $data = [] ) {
		echo $this->engine->render( $page, $data );
	}
}