<?php

namespace ANCENC\Admin;

use ANCENC\Files\MIME_Types;

class Settings {

	private $option_prefix = 'ancenc_';
	private $autoload_options = [
		'settings' => [
			'name'    => 'enabled_types',
			'default' => []
		]
	];

	private $allowed_sections = [
		'general'
	];

	public $object = [];

	public function register_filters() {
		add_filter( 'ancenc_settings_checked_for_section', array( &$this, 'setting_checked' ) );
	}

	public function load_section_settings( $section ) {
		return $this->get_option( 'settings_' . $section );
	}

	public function setting_checked( $query ) {
		$settings = $this->load_section_settings( $query['section'] );
		if ( $settings !== false ) {
			if ( isset( $settings[ $query['name'] ] ) && is_array( $settings[ $query['name'] ] ) ) {
				return in_array( $query['value'], $settings[ $query['name'] ] );
			} else {
				return in_array( $query['name'], $settings );
			}
		}

		return false;
	}

	public function register_ajax_actions() {
		add_action( 'wp_ajax_ancenc_update_settings', array( &$this, 'update_settings_ajax' ) );
	}

	public function update_settings_ajax() {
		parse_str( $_POST['data'], $output );

		if ( isset( $output['settings_section'] ) && wp_verify_nonce( $_POST['nonce'], 'ancenc_update_settings' ) ) {
			$update = $this->update_settings_object( $output );
			if ( $update ) {
				wp_send_json_success( [
					'message' => 'Settings updated'
				] );
				wp_die();
			}
		}

		wp_send_json_error( [
			'message' => 'Invalid update request'
		] );
		wp_die();
	}

	public function get_general_setting_option( $option_name ) {
		$settings_object = $this->get_option( 'settings_general' );
		if ( array_key_exists( $option_name, $settings_object ) ) {
			return $settings_object[ $option_name ];
		}

		return false;
	}

	public function update_settings_object( $new ) {
		if ( in_array( $new['settings_section'], $this->allowed_sections ) ) {
			$update_object = array_map( 'esc_sql', $new );
			$section       = $update_object['settings_section'];
			unset( $update_object['settings_section'] );
			$this->set_option( 'settings_' . $section, $update_object, true );

			return true;
		}

		return false;
	}

	public function set_option( $name, $value, $autoload = false ) {
		update_option( $this->option_prefix . $name, $value, $autoload );
	}

	public function available_settings() {
		global $wp_roles;

		$mime                   = new MIME_Types();
		$roles_options_children = [];

		foreach ( $wp_roles->roles as $role ) {
			$roles_options_children[] = [
				'name'     => 'enabled_roles[]',
				'disabled' => false,
				'value'    => $role['name'],
				'label'    => __( $role['name'] ),
				'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
					'section' => 'general',
					'name'    => 'enabled_roles',
					'value'   => $role['name']
				] )
			];
		}

		$roles_options = [
			'title'       => 'Enabled Roles',
			'description' => "Enabled roles will have access to the encrypted files, and will be able to view and download them.",
			'options'     => [
				[
					'type'       => 'checkbox',
					'name'       => 'enabled_roles',
					'single'     => false,
					'disabled'   => false,
					'value'      => null,
					'label'      => null,
					'check_type' => true,
					'children'   => $roles_options_children
				]
			]
		];

		return [
			[
				'title'       => 'Enabled File Types',
				'description' => "Encryption is only enabled for the checked file types, it's recommended that you don't enable encryption for public images, as this may significantly affect the website performance.",
				'options'     => [
					[
						'type'       => 'checkbox',
						'name'       => 'enabled_types',
						'single'     => false,
						'disabled'   => false,
						'value'      => null,
						'label'      => null,
						'check_type' => true,
						'children'   => [
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'image',
								'label'    => __( 'Image Files' ),
								'hint'     => __( 'Files with extensions: ' ) . implode( ' | ', array_keys( $mime->get_image_types() ) ),
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'image'
								] )
							],
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'audio',
								'label'    => __( 'Audio Files' ),
								'hint'     => __( 'Files with extensions: ' ) . implode( ' | ', array_keys( $mime->get_audio_types() ) ),
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'audio'
								] )
							],
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'video',
								'label'    => __( 'Video Files' ),
								'hint'     => __( 'Files with extensions: ' ) . implode( ' | ', array_keys( $mime->get_video_types() ) ),
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'video'
								] )
							],
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'zip',
								'label'    => __( 'Zip Files' ),
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'zip'
								] )
							],
							[
								'name'     => 'enabled_types[]',
								'disabled' => false,
								'value'    => 'pdf',
								'label'    => __( 'PDF Files' ),
								'checked'  => apply_filters( 'ancenc_settings_checked_for_section', [
									'section' => 'general',
									'name'    => 'enabled_types',
									'value'   => 'pdf'
								] )
							]
						]
					]
				]
			],
			[
				'title'       => 'Upload Path',
				'description' => "The custom path where WP Encrypted Uploads stores the encrypted files.",
				'options'     => [
					[
						'type'       => 'text',
						'name'       => 'upload_path',
						'single'     => true,
						'disabled'   => true,
						'value'      => apply_filters( 'ancenc_get_upload_path', '' ),
						'label'      => null,
						'check_type' => false,
						'size'       => 100
					]
				]
			],
			[
				'title'       => 'Force Download',
				'description' => "Force file types that could be viewed in the browser (images, PDFs, etc...) to be downloaded when requested.",
				'options'     => [
					[
						'type'       => 'checkbox',
						'name'       => 'force_download',
						'single'     => true,
						'disabled'   => false,
						'value'      => 'force_download',
						'label'      => null,
						'check_type' => true,
						'checked'    => apply_filters( 'ancenc_settings_checked_for_section', [
							'section' => 'general',
							'name'    => 'force_download',
							'value'   => 'force_download'
						] )
					]
				]
			],
			$roles_options
		];


	}

	public function get_option( $name, $default = false ) {
		return get_option( $this->option_prefix . $name, $default );
	}

	public function autoload_options() {
		foreach ( $this->autoload_options as $option ) {
			$this->object[ $option['name'] ] = get_option( $this->option_prefix . $option['name'], $option['default'] );
		}
	}

	public function settings_page_nonce() {
		return wp_create_nonce( 'ancenc_update_settings' );
	}
}
