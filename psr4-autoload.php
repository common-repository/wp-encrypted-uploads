<?php
spl_autoload_register( function ( $class ) {
	$prefix   = 'ANCENC\\';
	$root_dir = __DIR__ . '/server/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$class_without_prefix = substr( $class, $len );

	$class_path = $root_dir . str_replace( '\\', '/', $class_without_prefix ) . '.php';

	if ( file_exists( $class_path ) ) {
		require $class_path;
	}
} );
