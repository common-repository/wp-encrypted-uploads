<?php

namespace ANCENC\Files;

use ANCENC\Admin\Settings;
use ANCENC\Helpers\Str;

class Manager {

	private $upload_path;
	private $upload_dir;
	private $settings_manager;

	public function __construct( Settings $settings ) {
		$this->settings_manager = $settings;
		$this->upload_dir       = get_option( 'ancenc_custom_directory', 'wp_ancenc' );
		$this->upload_path      = trailingslashit( WP_CONTENT_DIR ) . $this->upload_dir;

		add_filter( 'ancenc_get_upload_dir', [ $this, 'get_upload_dir' ] );
		add_filter( 'ancenc_get_upload_path', [ $this, 'get_upload_path' ] );
		add_filter( 'ancenc_can_handle_type', [ $this, 'can_handle_type' ] );
		add_filter( 'wp_get_attachment_image_attributes', [ $this, 'filter_wp_get_attachment_image_attributes' ], 10 );
		add_filter( 'wp_get_attachment_url', [ $this, 'filter_wp_get_attachment_url' ], 10 );
	}

	public function register_handlers() {
		add_filter( 'wp_handle_upload', array( $this, 'handle_uploaded_file' ) );
	}

	public function handle_uploaded_file( $file ) {

		if ( $this->can_handle_type( $file ) ) {
			$file['file'] = $this->move_uploaded_file( $file['file'] );
			$this->rewrite_encrypted_file( $file['file'] );
		}

		return $file;
	}

	public function filter_wp_get_attachment_image_attributes( $attr ) {

		if ( $this->is_encrypted_file( $attr['src'] ) ) {
			if ( ! $this->can_download() ) {
				$attr['src'] = ANCENC_URL . 'public/images/file_icon.png';
			}

			$attr['style'] = 'background-image: url(' . $attr['src'] . ') no-repeat; width: 50px; height: auto;';
		}

		return $attr;
	}

	public function modify_attachment_url( $url, $id = null ) {

		if ( $this->is_encrypted_file( $url ) ) {
			$start_position = strpos( $url, ANCENC_DIR_PREFIX );
			$path           = substr( $url, $start_position );

			return content_url( $path );
		}

		return $url;
	}


	public function can_handle_type( $file ) {
		$file_path = $file['file'];
		$settings  = $this->settings_manager->get_option( 'settings_general' );
		$filename  = $this->get_file_name( $file_path );
		$check     = wp_check_filetype_and_ext( $file_path, $filename );

		if ( $check['type'] !== false ) {
			$mime = explode( '/', $check['type'] );
			if ( count( $mime ) > 0 && ! empty( $settings['enabled_types'] ) && is_array( $settings['enabled_types'] ) && in_array( $mime[0],
					$settings['enabled_types'] ) ) {
				return true;
			}
		}

		if ( $check['ext'] !== false && ! empty( $settings['enabled_types'] ) && is_array( $settings['enabled_types'] ) ) {
			return in_array( $check['ext'], $settings['enabled_types'] );
		}

		return false;
	}

	public function get_upload_dir( $dir = '' ) {
		return $this->upload_dir;
	}

	public function get_upload_path( $path = '' ) {
		return $this->upload_path;
	}

	public function file_exists( $filename ) {
		return file_exists( $this->upload_path . DIRECTORY_SEPARATOR . $filename );
	}

	public function get_file_name( $path ) {
		return basename( $path );
	}

	private function is_encrypted_file( $url ) {
		return false !== strpos( $url, ANCENC_DIR_PREFIX ) || false !== strpos( $url, 'ancenc_file' ) || $this->file_exists( $url );
	}

	private function get_file_ext( $filename ) {
		$exploded_name = explode( '.', $filename );

		return end( $exploded_name );
	}

	private function move_uploaded_file( $path ) {
		$filename     = $this->get_file_name( $path );
		$ext          = $this->get_file_ext( $filename );
		$dated_path   = $this->get_dated_path();
		$new_filename = $filename;

		if ( ! file_exists( $dated_path ) ) {
			mkdir( $dated_path, 0755, true );
			touch( $dated_path . DIRECTORY_SEPARATOR . 'index.php' );
		}

		if ( file_exists( $dated_path . DIRECTORY_SEPARATOR . $new_filename ) ) {
			$new_filename = Str::random( 16 ) . '.' . $ext;
		}

		$new_path = $dated_path . DIRECTORY_SEPARATOR . $new_filename;

		rename( $path, $new_path );

		return $new_path;
	}

	public function rewrite_encrypted_file( $path ) {
		$crypto   = new Crypto();
		$file     = $crypto->encrypt( $path );
		$tmp_path = stream_get_meta_data( $file )['uri'];
		unlink( $path );
		copy( $tmp_path, $path );
		fclose( $file );

		return true;
	}

	public function filter_wp_get_attachment_url( $url ) {
		if ( $this->is_encrypted_file( $url ) ) {
			$baseurl  = $this->upload_path;
			$filepath = str_replace( $this->get_wp_uploads_dir_url(), '', $url );
			$filepath = str_replace( $baseurl, '', $filepath );
			return sprintf( '%sindex.php?ancenc_action=ancenc_get_file&ancenc_file=%s', trailingslashit( site_url() ),
				$filepath );
		}

		return $url;
	}

	public function get_wp_uploads_dir_url() {
		$upload_dir = wp_get_upload_dir();
		if ( isset( $upload_dir['baseurl'] ) ) {
			return $upload_dir['baseurl'];
		}

		return null;
	}

	public function can_download() {
		if ( is_user_logged_in() ) {
			$roles = wp_get_current_user()->roles;
			$roles = array_map( function ( $item ) {
				return ucfirst( $item );
			}, $roles );

			$enabled_roles = $this->settings_manager->get_general_setting_option( 'enabled_roles' );

			if ( ! is_array( $enabled_roles ) ) {
				return false;
			}

			$intersect = array_intersect( $roles, $enabled_roles );
			if ( ! empty( $intersect ) ) {
				return true;
			}
		}

		return false;
	}

	private function get_dated_path() {
		return $this->upload_path . DIRECTORY_SEPARATOR . date( 'Y' ) . DIRECTORY_SEPARATOR . date( 'm' );
	}
}