<?php

namespace ANCENC\PublicDependencies;

class Style {

	public function register_assets() {
		wp_register_style( 'ancenc_admin_style', ANCENC_URL . 'public/css/admin.css', ['dashicons'], ANCENC_VER );
	}

	public function load_admin_dependencies() {
		wp_enqueue_style( 'ancenc_admin_style' );
	}

}
