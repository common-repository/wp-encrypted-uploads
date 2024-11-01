<?php

namespace ANCENC;

use ANCENC\Admin\Settings;
use ANCENC\PublicDependencies\Javascript;
use ANCENC\PublicDependencies\Style;

add_action( 'admin_menu', function () {
	$dic  = DicLoader::get_instance()->get_dic();
	$menu = $dic->make( 'ANCENC\Admin\Menu' );
	$menu->register_menus();
} );

add_action( 'init', function () {
	$js_deps = new Javascript();
	$js_deps->register_assets();

	$style_deps = new Style();
	$style_deps->register_assets();

	$dic = DicLoader::get_instance()->get_dic();
	$file_manager = $dic->make('ANCENC\Files\Manager');
	$file_manager->register_handlers();

	if ( isset( $_GET['ancenc_action'] ) && $_GET['ancenc_action'] === 'ancenc_get_file' && isset( $_GET['ancenc_file'] )) {
		$dic    = DicLoader::get_instance()->get_dic();
		$server = $dic->make( 'ANCENC\Files\Server' );
		$server->handle_file_serving( $_GET['ancenc_file'] );
	}
} );

add_action( 'admin_init', function () {
	$settings_manager = new Settings();
	$settings_manager->register_ajax_actions();
	$settings_manager->register_filters();
} );