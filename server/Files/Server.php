<?php

namespace ANCENC\Files;

use ANCENC\Admin\Settings;

class Server {
	private $file_manager;
	private $settings_manager;

	public function __construct( Manager $file_manager, Settings $settings_manager ) {
		$this->file_manager     = $file_manager;
		$this->settings_manager = $settings_manager;
	}

	public function is_octet_stream( $mime ) {
		return false === strpos( $mime, 'video' ) && false === strpos( $mime, 'image' );
	}

	public function handle_file_serving( $file ) {
		$file      = sanitize_text_field( $file );
		$file_path = str_replace( $this->file_manager->get_upload_dir(), '', $file );

		if ( $this->file_manager->file_exists( $file_path ) ) {
			$this->open_file( $this->file_manager->get_upload_path() . $file_path );
		}
	}

	public function will_force_download() {
		$force_download = $this->settings_manager->get_general_setting_option( 'force_download' );

		return $force_download === 'force_download';
	}

	public function open_file( $file_path ) {
		if ( $this->file_manager->can_download() ) {
			$crypto = new Crypto();

			try {
				$decrypted_file = $crypto->decrypt( $file_path );
			} catch ( \Exception $exception ) {
				http_response_code( 404 );
				exit();
			}

			$decrypted_file_path = stream_get_meta_data( $decrypted_file )['uri'];
			$mime                = mime_content_type( $decrypted_file_path );

			rewind( $decrypted_file );

			$filesize = filesize( $decrypted_file_path );
			$start  = 0;
			$end    = $filesize - 1;
			$output_length = $filesize;
			if (isset($_SERVER['HTTP_RANGE'])) {

				$c_start = $start;
				$c_end   = $end;

				list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
				if (strpos($range, ',') !== false) {
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$filesize");
					exit;
				}
				if ($range == '-') {
					$c_start = $filesize - substr($range, 1);
				}else{
					$range  = explode('-', $range);
					$c_start = $range[0];
					$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $filesize;
				}
				$c_end = ($c_end > $end) ? $end : $c_end;
				if ($c_start > $c_end || $c_start > $filesize - 1 || $c_end >= $filesize) {
					header('HTTP/1.1 416 Requested Range Not Satisfiable');
					header("Content-Range: bytes $start-$end/$filesize");
					exit;
				}
				$start  = $c_start;
				$end    = $c_end;
				$output_length = $end - $start + 1;
				fseek($decrypted_file, $start);
				header('HTTP/1.1 206 Partial Content');
			}

			header( 'Content-Description: File Transfer' );
			header( 'Connection: Keep-Alive' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Content-Length: ' . $output_length );
			header( 'Accept-Ranges: 0-' . $filesize );
			header("Content-Range: bytes $start-$end/$filesize");

			if ( $this->is_octet_stream( $mime ) ) {
				header( 'Content-Type: application/octet-stream' );
			} else {
				header( 'Content-Type: image/png' );
			}

			if ( $this->will_force_download() || $this->is_octet_stream( $mime ) ) {
				header( "Content-Disposition: attachment; filename= " . basename( $file_path ) );
			}

			$output_stream = fopen( 'php://output', 'wb' );

			stream_copy_to_stream( $decrypted_file, $output_stream );

			fclose( $decrypted_file );
			fclose( $output_stream );
			exit;
		} else {
			http_response_code( 403 );
			exit();
		}
	}

}