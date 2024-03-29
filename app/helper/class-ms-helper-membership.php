<?php
/**
 * Helper for the Membership class.
 */
class MS_Helper_Membership extends MS_Helper {

	const MEMBERSHIP_ACTION_SIGNUP 	= 'membership_signup';
	const MEMBERSHIP_ACTION_MOVE 	= 'membership_move';
	const MEMBERSHIP_ACTION_CANCEL 	= 'membership_cancel';
	const MEMBERSHIP_ACTION_RENEW 	= 'membership_renew';
	const MEMBERSHIP_ACTION_PAY 	= 'membership_pay';

	const MEMBERSHIP_MSG_ADDED 					= 1;
	const MEMBERSHIP_MSG_DELETED 				= 2;
	const MEMBERSHIP_MSG_UPDATED 				= 3;
	const MEMBERSHIP_MSG_ACTIVATION_TOGGLED 	= 4;
	const MEMBERSHIP_MSG_STATUS_TOGGLED 		= 5;
	const MEMBERSHIP_MSG_BULK_UPDATED 			= 6;
	const MEMBERSHIP_MSG_NOT_ADDED 				= -1;
	const MEMBERSHIP_MSG_NOT_DELETED 			= -2;
	const MEMBERSHIP_MSG_NOT_UPDATED 			= -3;
	const MEMBERSHIP_MSG_ACTIVATION_NOT_TOGGLED = -4;
	const MEMBERSHIP_MSG_STATUS_NOT_TOGGLED 	= -5;
	const MEMBERSHIP_MSG_BULK_NOT_UPDATED 		= -6;
	const MEMBERSHIP_MSG_PARTIALLY_UPDATED 		= -8;

	public static function get_admin_messages( $msg = 0 ) {
		$messages = apply_filters(
			'ms_helper_membership_get_admin_messages',
			array(
				self::MEMBERSHIP_MSG_ADDED 					=> __( 'Du hast Deine <b>%s</b> Mitgliedschaft erfolgreich eingerichtet.', 'membership2' ),
				self::MEMBERSHIP_MSG_DELETED 				=> __( 'Mitgliedschaft gelöscht.', 'membership2' ),
				self::MEMBERSHIP_MSG_UPDATED 				=> __( 'Mitgliedschaft <b>%s</b> aktualisiert.', 'membership2' ),
				self::MEMBERSHIP_MSG_ACTIVATION_TOGGLED 	=> __( 'Die Aktivierung der Mitgliedschaft wurde umgeschaltet.', 'membership2' ),
				self::MEMBERSHIP_MSG_STATUS_TOGGLED 		=> __( 'Mitgliedschaftsstatus umgeschaltet.', 'membership2' ),
				self::MEMBERSHIP_MSG_BULK_UPDATED 			=> __( 'Bulk der Mitgliedschaften aktualisiert.', 'membership2' ),
				self::MEMBERSHIP_MSG_NOT_ADDED 				=> __( 'Mitgliedschaft nicht hinzugefügt.', 'membership2' ),
				self::MEMBERSHIP_MSG_NOT_DELETED 			=> __( 'Mitgliedschaft nicht gelöscht.', 'membership2' ),
				self::MEMBERSHIP_MSG_NOT_UPDATED 			=> __( 'Mitgliedschaft nicht aktualisiert.', 'membership2' ),
				self::MEMBERSHIP_MSG_ACTIVATION_NOT_TOGGLED => __( 'Die Aktivierung der Mitgliedschaft wurde nicht umgeschaltet.', 'membership2' ),
				self::MEMBERSHIP_MSG_STATUS_NOT_TOGGLED 	=> __( 'Mitgliedschaftsstatus nicht umgeschaltet.', 'membership2' ),
				self::MEMBERSHIP_MSG_BULK_NOT_UPDATED 		=> __( 'Der Großteil der Mitgliedschaften wurde nicht aktualisiert.', 'membership2' ),
				self::MEMBERSHIP_MSG_PARTIALLY_UPDATED 		=> __( 'Mitgliedschaften teilweise aktualisiert. Einige Felder konnten nicht geändert werden, nachdem sich Mitglieder angemeldet haben.', 'membership2' ),
			)
		);

		if ( array_key_exists( $msg, $messages ) ) {
			return $messages[ $msg ];
		} else {
			return null;
		}
	}

	public static function print_admin_message() {
		$msg 	= self::get_msg_id();
		$class 	= ( $msg > 0 ) ? 'updated' : 'error';

		if ( $msg = self::get_admin_messages( $msg ) ) {
			mslib3()->ui->admin_message( $msg, $class );
		}
	}

	public static function get_admin_message( $args = null, $membership = null ) {
		$msg 	= '';
		$msg_id = self::get_msg_id();

		if ( $msg = self::get_admin_messages( $msg_id ) ) {
			if ( ! empty( $args ) ) {
				$msg = vsprintf( $msg, $args );
			}

			// When the first membership was created show a popup to the user
			$is_first = true;
			if ( $is_first
				&& self::MEMBERSHIP_MSG_ADDED == $msg_id
				&& ! empty( $membership )
			) {
				$url = MS_Controller_Plugin::get_admin_settings_url();

				self::show_setup_note( $membership );
			}
		}

		return apply_filters(
			'ms_helper_membership_get_admin_message',
			$msg
		);
	}

	public static function get_admin_title() {
		$title 	= __( 'Mitgliedschaften', 'membership2' );
		$msg 	= self::get_msg_id();
		if ( self::MEMBERSHIP_MSG_ADDED == $msg ) {
			$title = __( 'Herzlichen Glückwünsch!', 'membership2' );
		}
		return apply_filters( 'ms_helper_membership_get_admin_title', $title );
	}

	public static function get_msg_id() {
		$msg = ! empty( $_GET['msg'] ) ? (int) $_GET['msg'] : 0;
		return apply_filters( 'ms_helper_membership_get_msg_id', $msg );
	}

	/**
	 * Displays a PopUp to the user that shows a sumary of the setup wizard
	 * including possible next steps for configuration.
	 *
	 * @since  1.0.0
	 * @param  MS_Model_Membership $membership The membership that was created.
	 */
	public static function show_setup_note( $membership ) {
		$popup 			= array();
		$setup 			= MS_Factory::create( 'MS_View_Settings_Page_Setup' );


		$popup['title'] 	= sprintf(
				'<i class="dashicons dashicons-yes"></i> %1$s<div class="subtitle">%2$s</div>',
				__( 'Gratulation!', 'membership2' ),
				sprintf(
					__( 'Du hast Deine <b>%1$s</b> Mitgliedschaft erfolgreich eingerichtet.', 'membership2' ),
					$membership->name
				)
			);
		$popup['modal'] 	= true;
		$popup['close'] 	= false;
		$popup['sticky'] 	= false;
		$popup['class'] 	= 'ms-setup-done';
		$popup['body'] 		= $setup->to_html();
		$popup['height'] 	= $setup->dialog_height();

		$popup['body'] 		.= sprintf(
			'<div class="buttons">' .
			'<a href="%s" class="button">%s</a> ' .
			'<button type="button" class="button-primary close">%s</button>' .
			'</div>',
			MS_Controller_Plugin::get_admin_url( 'protection' ),
			__( 'Richte Zugriffsebenen ein', 'membership2' ),
			__( 'Fertig', 'membership2' )
		);

		mslib3()->html->popup( $popup );

		$settings = MS_Plugin::instance()->settings;
		$settings->is_first_membership = false;
		if ( ! $membership->is_free ) {
			$settings->is_first_paid_membership = false;
		}
		$settings->save();
	}
}