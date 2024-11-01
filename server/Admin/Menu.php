<?php

namespace ANCENC\Admin;

use ANCENC\PublicDependencies\Javascript;
use ANCENC\PublicDependencies\Style;
use ANCENC\UI\Renderer;

class Menu {
	private $public_css_deps;
	private $public_js_deps;

	public function __construct( Style $public_css_deps, Javascript $public_js_deps ) {
		$this->public_css_deps = $public_css_deps;
		$this->public_js_deps = $public_js_deps;

		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
		add_filter( 'plugin_action_links_' . ANCENC_PLUGIN_BASENAME, [$this, 'filter_plugin_action_links'] );

	}

	public function register_menus() {
		add_submenu_page(
			'upload.php',
			__( 'Encrypted Uploads', 'ancenc' ),
			__( 'Encrypted Uploads', 'ancenc' ),
			'manage_options',
			'ancenc',
			array( &$this, 'render_menu' )
		);
	}

	public function enqueue_admin_scripts() {
		$this->public_js_deps->load_admin_dependencies();
	}

	public function render_menu() {

		$this->public_css_deps->load_admin_dependencies();


		$renderer         = new Renderer();
		$settings_manager = new Settings();
		$settings_manager->autoload_options();

		$settings = $settings_manager->object;

		$renderer->render( 'menu', array(
			'available_settings' => $settings_manager->available_settings(),
			'update_nonce' => $settings_manager->settings_page_nonce()
		));
	}

	public function filter_plugin_action_links($links) {
		if ( ! is_array( $links ) ) {
			$links = [];
		}

		$links[] = 	'<a href="' . esc_url( admin_url( 'upload.php?page=ancenc' ) ) . '">' . __( 'Settings', 'ancenc' ) . '</a>';

		return $links;
	}

}