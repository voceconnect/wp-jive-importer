<?php

class Jive_Import_Author {

	protected $user;

	/**
	 * @param $user
	 */
	public function __construct( $user ) {
		$this->user = (object) $user;
	}

	/**
	 * @return bool|int
	 */
	public function user_exists() {
		$id = email_exists( $this->user->email );
		if ( $id ) {
			return $id;
		}
		return false;
	}

	/**
	 * @return array
	 */
	protected function format_user() {
		return array(
			'user_email'      => $this->user->email,
			'first_name'      => $this->user->firstName,
			'last_name'       => $this->user->lastName,
			'user_registered' => date( 'Y-m-d H:i:s', $this->user->creationDate ),
			'role'            => 'author',
		);
	}

	/**
	 * @return int|WP_Error
	 */
	public function create_user() {
		return wp_insert_user( $this->format_user() );
	}
}