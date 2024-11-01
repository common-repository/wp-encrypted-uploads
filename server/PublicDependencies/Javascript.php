<?php

namespace ANCENC\PublicDependencies;

class Javascript {
	public function register_assets() {
		wp_register_script( 'ANCENC_admin', ANCENC_URL . 'public/js/admin.js', [ 'jquery' ], ANCENC_VER, true );

		wp_localize_script( 'ANCENC_admin', 'ANCENC',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	public function load_admin_dependencies() {
		wp_enqueue_script( 'ANCENC_admin' );
	}

}
