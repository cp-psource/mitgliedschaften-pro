<?php

class MS_Gateway_Paypalstandard_View_Settings extends MS_View {

	public function to_html() {
		$fields = $this->prepare_fields();
		$gateway = $this->data['model'];

		ob_start();
		// Render tabbed interface.
		?>
		<form class="ms-gateway-settings-form ms-form">
			<?php
			$description = sprintf(
				'%s<br />&nbsp;<br />%s<br />&nbsp;<br />%s <strong>%s</strong><br /><a href="%s" target="_blank">%s</a>',
				__( 'Dieses erweiterte PayPal-Gateway verarbeitet alle Zahlungsarten, einschließlich Testzeiträumen und wiederkehrender Zahlungen. Es sollte jedoch nicht für dauerhafte Mitgliedschaften verwendet werden, da hier beim Auschecken "Nach 5 Jahren wieder zahlen" angezeigt wird.', 'membership2' ),
				__( 'Damit Mitgliedschaften ordnungsgemäß funktioniert, musst Du mit PayPal eine IPN-Überwachungs-URL einrichten. Stelle sicher, dass Du diesen Schritt ausführst. Andernfalls werden wir nicht benachrichtigt, wenn ein Mitglied sein Abonnement kündigt.', 'membership2' ),
				__( 'Deine IPN-Abhör-URL lautet:', 'membership2' ),
				$this->data['model']->get_return_url(),
				'https://developer.paypal.com/docs/classic/ipn/integration-guide/IPNSetup/',
				__( 'Anleitung &raquo;', 'membership2' )
			);

			MS_Helper_Html::settings_box_header( '', $description );
			foreach ( $fields as $field ) {
				MS_Helper_Html::html_element( $field );
			}
			MS_Helper_Html::settings_box_footer();
			?>
		</form>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	protected function prepare_fields() {
		$gateway 	= $this->data['model'];
		$action 	= MS_Controller_Gateway::AJAX_ACTION_UPDATE_GATEWAY;
		$nonce 		= wp_create_nonce( $action );

		$fields 	= array(
			'merchant_id' 			=> array(
				'id' 			=> 'merchant_id',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'title' 		=> __( 'PayPal-Händlerkonto-ID', 'membership2' ),
				'desc' 			=> sprintf(
					__( 'Hinweis: Dies ist <i>nicht die E-Mail-Adresse</i>, sondern die Händler-ID in %sDeinem PayPal-Profil%s. (Verwende im Sandbox-Modus Deine Sandbox-E-Mail-Adresse.)', 'membership2' ),
					'<a href="https://www.paypal.com/webapps/customerprofile/summary.view" target="_blank">',
					'</a>'
				),
				'value' 		=> $gateway->merchant_id,
				'placeholder' 	=> 'SGGGX43FAKKXN',
				'class' 		=> 'ms-text-large',
				'ajax_data' 	=> array( 1 ),
			),

			'paypal_site' 			=> array(
				'id' 			=> 'paypal_site',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'title' 		=> __( 'PayPal Seite', 'membership2' ),
				'field_options' => $gateway->get_paypal_sites(),
				'value' 		=> $gateway->paypal_site,
				'class' 		=> 'ms-text-large',
				'ajax_data' 	=> array( 1 ),
			),

			'mode' 					=> array(
				'id' 			=> 'mode',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'title' 		=> __( 'PayPal Modus', 'membership2' ),
				'value' 		=> $gateway->mode,
				'field_options' => $gateway->get_mode_types(),
				'class' 		=> 'ms-text-large',
				'ajax_data' 	=> array( 1 ),
			),

			'pay_button_url' 		=> array(
				'id' 			=> 'pay_button_url',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'title' 		=> apply_filters(
					'ms_translation_flag',
					__( 'Etikett oder URL der Zahlungsschaltfläche', 'membership2' ),
					'gateway-button' . $gateway->id
				),
				'value' 		=> $gateway->pay_button_url,
				'class' 		=> 'ms-text-large',
				'ajax_data' 	=> array( 1 ),
			),
		);

		// Process the fields and add missing default attributes.
		foreach ( $fields as $key => $field ) {
			if ( ! empty( $field['ajax_data'] ) ) {
				$fields[ $key ]['ajax_data']['field'] 		= $fields[ $key ]['id'];
				$fields[ $key ]['ajax_data']['_wpnonce'] 	= $nonce;
				$fields[ $key ]['ajax_data']['action'] 		= $action;
				$fields[ $key ]['ajax_data']['gateway_id'] 	= $gateway->id;
			}
		}

		return $fields;
	}

}