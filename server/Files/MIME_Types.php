<?php

namespace ANCENC\Files;

class MIME_Types {

	private $mimes;

	public function __construct() {
		$this->mimes = wp_get_mime_types();
	}

	public function get_image_types() {
		return array_filter( $this->mimes, function ( $item ) {
			return ( strpos( $item, 'image' ) !== false );
		} );
	}

	public function get_video_types() {
		return array_filter( $this->mimes, function ( $item ) {
			return ( strpos( $item, 'video' ) !== false );
		} );
	}

	public function get_audio_types() {
		return array_filter( $this->mimes, function ( $item ) {
			return ( strpos( $item, 'audio' ) !== false );
		} );
	}

}