<?php

class MS_Gateway_Manual_View_Settings extends MS_View {

	public function to_html() {
		$fields = $this->prepare_fields();
		$gateway = $this->data['model'];

		$msg = __(
			'Weise Deine Benutzer an, wie sie mit manuellen Zahlungen fortfahren sollen. Dies kann beispielsweise Deine Bankkontonummer und eine E-Mail-Adresse umfassen, an die die Zahlungsbestätigung gesendet werden soll.',
			'membership2'
		) . '<br />&nbsp;<br /><em>' . __(
			'Bei Verwendung dieser Zahlungsmethode werden dem Benutzer die folgenden Zahlungsanweisungen angezeigt. Da die Zahlung nicht automatisch bestätigt werden kann, wird seine Mitgliedschaft <b> nicht </ b> sofort aktiviert! Du musst manuell prüfen, ob die Zahlung getätigt wurde, und die Mitgliederrechnung auf "bezahlt" setzen, um die Zahlung abzuschließen.',
			'membership2'
		) .
		'</em>';

		ob_start();
		// Render tabbed interface.
		?>
		<form class="ms-gateway-settings-form ms-form">
			<?php
			MS_Helper_Html::settings_box_header( '', $msg );
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
		$gateway = $this->data['model'];
		$action = MS_Controller_Gateway::AJAX_ACTION_UPDATE_GATEWAY;
		$nonce = wp_create_nonce( $action );

		$fields = array(
			'payment_info' => array(
				'id' => 'payment_info',
				'title' => apply_filters(
					'ms_translation_flag',
					__( 'Zahlungsinformationen', 'membership2' ),
					'gateway-button' . $gateway->id
				),
				'type' => MS_Helper_Html::INPUT_TYPE_TEXT_AREA,
				'value' => $gateway->payment_info ?? '',
				'field_options' => array( 'editor_class' => 'ms-field-wp-editor' ),
				'class' => 'ms-text-large',
				'ajax_data' => array( 1 ),
			),

			'pay_button_url' => array(
				'id' => 'pay_button_url',
				'title' => apply_filters(
					'ms_translation_flag',
					__( 'Etikett oder URL der Zahlungsschaltfläche', 'membership2' ),
					'gateway-button' . $gateway->id
				),
				'type' => MS_Helper_Html::INPUT_TYPE_TEXT,
				'value' => $gateway->pay_button_url ?? '',
				'class' => 'ms-text-large',
				'ajax_data' => array( 1 ),
			),
		);

		// Process the fields and add missing default attributes.
		foreach ( $fields as $key => $field ) {
			if ( ! empty( $field['ajax_data'] ) ) {
				$fields[ $key ]['ajax_data']['field'] = $fields[ $key ]['id'];
				$fields[ $key ]['ajax_data']['_wpnonce'] = $nonce;
				$fields[ $key ]['ajax_data']['action'] = $action;
				$fields[ $key ]['ajax_data']['gateway_id'] = $gateway->id;
			}
		}

		return apply_filters(
			'ms_gateway_manual_view_settings_prepare_fields',
			$fields
		);
	}
}