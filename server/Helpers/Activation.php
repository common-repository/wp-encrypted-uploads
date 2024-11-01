<?php

namespace ANCENC\Helpers;

use ANCENC\Files\Crypto;

class Activation {

	public function activation_hooks() {
		$this->create_custom_upload_directory();
		$this->add_rewrite_rules();
		$this->create_encryption_keys();
	}

	public function add_rewrite_rules() {
		$path = ABSPATH . '.htaccess';
		if ( file_exists( $path ) ) {
			$file          = fopen( $path, 'r+' );
			$content       = fread( $file, filesize( $path ) );
			$rule_position = strpos( $content, "# BEGIN WP-Encrypted-Uploads" );
			if ( $rule_position === false ) {
				$plugin_rule_file_path = ANCENC_PATH . DIRECTORY_SEPARATOR . 'static' . DIRECTORY_SEPARATOR . '.htaccess';
				$plugin_rule_file      = fopen( $plugin_rule_file_path, 'r' );
				$plugin_rule_content   = fread( $plugin_rule_file, filesize( $plugin_rule_file_path ) );
				$new_content           = $content . $plugin_rule_content;
				ftruncate( $file, 0 );
				fwrite( $file, $new_content );
				fclose( $file );
				fclose( $plugin_rule_file );
			}
		}
	}

	public function create_custom_upload_directory() {
		$created = get_option( 'ancenc_custom_directory_created', false );

		if ( $created === false ) {
			$custom_name = ANCENC_DIR_PREFIX . '_' . Str::random( 12 );
			if ( defined( 'WP_CONTENT_DIR' ) ) {
				$folder_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $custom_name;
			} else {
				$folder_path = ANCENC_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $custom_name;
			}
			$index_path = $folder_path . DIRECTORY_SEPARATOR . 'index.php';

			$mkdir      = mkdir( $folder_path, 0755 );
			$touch_file = touch( $index_path );

			if ( $mkdir ) {
				update_option( 'ancenc_custom_directory_created', true );
				update_option( 'ancenc_custom_directory', $custom_name, true );
			} else {
				Logger::error( __( "We weren't able to create the custom uploads directory, make sure wp-content has the correct permissions.", 'ancenc' ) );
			}

			if ( ! $touch_file ) {
				Logger::error( __( "We weren't able to create the index.php file in the custom uploads directory, make sure wp-content has the correct permissions.", 'ancenc' ) );
			}
		}
	}

	public function create_encryption_keys() {
		if ( get_option( 'ancenc_encryption_key', false ) === false ) {
			$key = strtoupper( Str::random( 16 ) );
			update_option( 'ancenc_encryption_key', $key );
		}
	}
}