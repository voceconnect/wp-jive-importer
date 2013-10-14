<?php

/**
 * Create a singleton to handle requests to the Jive API
 *
 */

class Jive_Import_Connection {

	private static $instance;
	protected $jive_settings;
	protected $request_args = array(
		'timeout'     => 5,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(),
		'cookies'     => array(),
		'body'        => null,
		'compress'    => false,
		'decompress'  => true,
		'sslverify'   => true,
		'stream'      => false,
		'filename'    => null,
	);

	public static function GetInstance() {

		if ( !isset( self::$instance ) ) {
			self::$instance = new Jive_Import_Connection();
		}

		return self::$instance;
	}

	protected function __construct() {
		$this->jive_settings = get_option( 'jive_import_settings' );
		$this->request_args[ 'headers' ] = array(
			'Authorization' => 'Basic ' . base64_encode( $this->jive_settings[ 'username' ] . ':' . $this->jive_settings[ 'password' ] ),
		);
	}

	public function get_posts() {
		$url = sprintf( 'https://%s/api/openclient/v3/blogs/%s/blogposts', $this->jive_settings[ 'domain' ], $this->jive_settings[ 'blog_id' ] );
		$request = wp_remote_get( $url, $this->request_args );
		return $request;
	}
}