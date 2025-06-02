<?php

class MS_Gateway_Stripe_View_Card extends MS_View {

	public function to_html() {
		$fields = $this->prepare_fields();
		$publishable_key = $this->data['publishable_key'];
		ob_start();
		?>
		<div class="ms-wrap ms-card-info-wrapper">
			<h2><?php _e( 'Kreditkarteninformationen', 'membership2' ); ?> </h2>
			<table class="ms-table">
				<tbody>
					<tr>
						<th><?php _e( 'Kartennummer', 'membership2' ); ?></th>
						<th><?php _e( 'Kartenablaufdatum', 'membership2' ); ?></th>
					</tr>
					<tr>
						<td><?php echo '**** **** **** '. $this->data['stripe']['card_num']; ?></td>
						<td><?php echo '' . $this->data['stripe']['card_exp']; ?></td>
					</tr>
				</tbody>
			</table>
			<form id="ms-stripe-payment-form" action="" method="post">
				<?php
					foreach ( $fields as $field ) {
						MS_Helper_Html::html_element( $field );
					}
				?>
				<div id="ms-stripe-card-element"></div>
				<div id="ms-stripe-card-errors" role="alert"></div>
				<button id="ms-stripe-submit" type="submit"><?php _e( 'Kreditkarte ändern', 'membership2' ); ?></button>
			</form>
			<script src="https://js.stripe.com/v3/"></script>
			<script>
			document.addEventListener('DOMContentLoaded', function() {
				var stripe = Stripe('<?php echo esc_js( $publishable_key ); ?>');
				var elements = stripe.elements();
				var card = elements.create('card');
				card.mount('#ms-stripe-card-element');

				card.on('change', function(event) {
					var displayError = document.getElementById('ms-stripe-card-errors');
					if (event.error) {
						displayError.textContent = event.error.message;
					} else {
						displayError.textContent = '';
					}
				});

				var form = document.getElementById('ms-stripe-payment-form');
				form.addEventListener('submit', function(event) {
					event.preventDefault();
					stripe.createPaymentMethod({
						type: 'card',
						card: card,
					}).then(function(result) {
						if (result.error) {
							document.getElementById('ms-stripe-card-errors').textContent = result.error.message;
						} else {
							var hiddenInput = document.createElement('input');
							hiddenInput.setAttribute('type', 'hidden');
							hiddenInput.setAttribute('name', 'stripePaymentMethod');
							hiddenInput.setAttribute('value', result.paymentMethod.id);
							form.appendChild(hiddenInput);
							form.submit();
						}
					});
				});
			});
			</script>
			<div class="clear"></div>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	private function prepare_fields() {
		$fields = array(
			'gateway' 			=> array(
				'id' 	=> 'gateway',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $this->data['gateway']->id,
			),

			'ms_relationship_id' => array(
				'id' 	=> 'ms_relationship_id',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $this->data['ms_relationship_id'],
			),

			'_wpnonce' 			=> array(
				'id' 	=> '_wpnonce',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => wp_create_nonce( 'update_card' ),
			),

			'action' 			=> array(
				'id' 	=> 'action',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => 'update_card',
			),
		);

		return $fields;
	}
}