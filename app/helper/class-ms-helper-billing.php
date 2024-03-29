<?php

/**
 * Billing Helper class
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Helper
 */
class MS_Helper_Billing extends MS_Helper {

	const BILLING_MSG_ADDED 		= 1;
	const BILLING_MSG_DELETED 		= 2;
	const BILLING_MSG_UPDATED 		= 3;
	const BILLING_MSG_NOT_ADDED 	= -1;
	const BILLING_MSG_NOT_DELETED 	= -2;
	const BILLING_MSG_NOT_UPDATED 	= -3;
	const BILLING_MSG_NOT_A_MEMBER 	= -5;

	public static function get_admin_message( $msg = 0 ) {
		$messages = apply_filters(
			'ms_helper_billing_get_admin_messages',
			array(
				self::BILLING_MSG_ADDED 		=> __( 'Neue Rechnung hinzugefügt.', 'membership2' ),
				self::BILLING_MSG_DELETED 		=> __( 'Rechnung(en) entfernt.', 'membership2' ),
				self::BILLING_MSG_UPDATED 		=> __( 'Rechnungsdetails aktualisiert.', 'membership2' ),
				self::BILLING_MSG_NOT_ADDED 	=> __( 'Rechnung konnte nicht hinzugefügt werden.', 'membership2' ),
				self::BILLING_MSG_NOT_DELETED 	=> __( 'Rechnung(en) konnten nicht entfernt werden.', 'membership2' ),
				self::BILLING_MSG_NOT_UPDATED 	=> __( 'Rechnung konnte nicht aktualisiert werden.', 'membership2' ),
				self::BILLING_MSG_NOT_A_MEMBER 	=> __( 'Die Rechnung wurde nicht hinzugefügt: Der Benutzer ist kein Mitglied der ausgewählten Mitgliedschaft.', 'membership2' ),
			)
		);

		if ( array_key_exists( $msg, $messages ) ) {
			return $messages[ $msg ];
		}
		return null;
	}

	public static function print_admin_message() {
		$msg 		= ! empty( $_GET['msg'] ) ? (int) $_GET['msg'] : 0;
		$class 		= ( $msg > 0 ) ? 'updated' : 'error';
		$contents 	= self::get_admin_message( $msg );

		if ( $contents ) {
			mslib3()->ui->admin_message( $contents, $class );
		}
	}

	/**
	 * Formats a number to display a valid price.
	 *
	 * @since  1.0.0
	 * @param  numeric $price
	 * @return numeric
	 */
	static public function format_price( $price ) {
		$formatted = number_format( (float) $price, 2, '.', '' );

		return apply_filters(
			'ms_format_price',
			$formatted,
			$price
		);
	}

}