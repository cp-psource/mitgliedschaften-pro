<?php
/**
 * Base class for all import handlers.
 *
 * @since  1.1.2
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Import_User extends MS_Model_Import {

	/**
	 * Process the import
	 * Parse the uploaded csv file and import the data
	 *
	 * @since 1.1.2
	 *
	 * @return Array
	 */
	public function prepare() {
		self::_message( 'preview', false );
		
		if ( empty( $_FILES ) || ! isset( $_FILES['upload'] ) ) {
			self::_message( 'error', __( 'Es wurde keine Datei hochgeladen. Bitte versuche es erneut.', 'membership2' ) );
			return false;
		}

		$file = $_FILES['upload'];
		if ( empty( $file['name'] ) ) {
			self::_message( 'error', __( 'Bitte lade eine CSV-Datei hoch.', 'membership2' ) );
			return false;
		}
		if ( empty( $file['size'] ) ) {
			self::_message( 'error', __( 'Die hochgeladene Datei ist leer. Bitte versuche es erneut.', 'membership2' ) );
			return false;
		}

		if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
			self::_message( 'error', __( 'Hochgeladene Datei nicht gefunden, bitte versuche es erneut.', 'membership2' ) );
			return false;
		}

		$membership = false;
		if ( isset( $_POST['users-membership'] )  && !empty( $_POST['users-membership'] ) && intval( $_POST['users-membership'] ) > 0 ) {
			$membership = $_POST['users-membership'];
		}

		$status = $_POST['users-status'];
		$start 	= $_POST['users-start'];
		$expire = $_POST['users-expire'];

		$csv = array_map( 'str_getcsv', file( $file['tmp_name'] ) );
		array_walk( $csv, function( &$a ) use ( $csv ) {
			$a = array_combine( $csv[0], $a );
		});
		array_shift( $csv ); # remove column header

		if ( empty( $csv ) ) {
			self::_message( 'error', __( 'Es wurde keine gültige Benutzer-CSV-Datei hochgeladen. Bitte versuche es erneut.', 'membership2' ) );
			return false;
		}

		$this->source 	= array(
			'membership' 	=> $membership,
			'status' 		=> $status,
			'start' 		=> $start,
			'expire' 		=> $expire,
			'users'			=> $csv
		);
		return true;
	}
}
?>