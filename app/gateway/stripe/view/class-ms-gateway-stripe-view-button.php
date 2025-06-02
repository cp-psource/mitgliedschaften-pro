<?php

class MS_Gateway_Stripe_View_Button extends MS_View {

    public function to_html() {
        $fields 		= $this->prepare_fields();
        $subscription 	= $this->data['ms_relationship'];
        $invoice 		= $subscription->get_next_billable_invoice();
        $member 		= MS_Model_Member::get_current_member();
        $gateway 		= $this->data['gateway'];

        $action_url = apply_filters(
            'ms_gateway_stripe_view_button_form_action_url',
            '' // AJAX oder POST an aktuelle Seite
        );

        $row_class 	= 'gateway_' . $gateway->id;
        if ( ! $gateway->is_live_mode() ) {
            $row_class .= ' sandbox-mode';
        }

        $publishable_key = $gateway->get_publishable_key();

        ob_start();
        ?>
        <form id="ms-stripe-payment-form" action="<?php echo esc_url( $action_url ); ?>" method="post">
            <?php
            foreach ( $fields as $field ) {
                MS_Helper_Html::html_element( $field );
            }
            ?>
            <div id="ms-stripe-card-element"></div>
            <div id="ms-stripe-card-errors" role="alert"></div>
            <button id="ms-stripe-submit" type="submit">
                <?php echo esc_html( $gateway->pay_button_url ? $gateway->pay_button_url : __( 'Jetzt bezahlen', 'membership2' ) ); ?>
            </button>
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
                    billing_details: {
                        email: '<?php echo esc_js( $member->email ); ?>'
                    }
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
        <?php
        $payment_form = apply_filters(
            'ms_gateway_form',
            ob_get_clean(),
            $gateway,
            $invoice,
            $this
        );

        ob_start();
        ?>
        <tr class="<?php echo esc_attr( $row_class ); ?>">
            <td class="ms-buy-now-column" colspan="2">
                <?php echo $payment_form; ?>
            </td>
        </tr>
        <?php
        $html = ob_get_clean();

        $html = apply_filters(
            'ms_gateway_button-' . $gateway->id,
            $html,
            $this
        );

        return $html;
    }

    private function prepare_fields() {
        $gateway = $this->data['gateway'];
        $subscription = $this->data['ms_relationship'];

        $fields = array(
            '_wpnonce' 			=> array(
                'id' 	=> '_wpnonce',
                'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value' => wp_create_nonce(
                    $gateway->id . '_' . $subscription->id
                ),
            ),
            'gateway' 			=> array(
                'id' 	=> 'gateway',
                'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value' => $gateway->id,
            ),
            'ms_relationship_id' => array(
                'id' 	=> 'ms_relationship_id',
                'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value' => $subscription->id,
            ),
            'step' 				=> array(
                'id' 	=> 'step',
                'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value' => $this->data['step'],
            ),
        );

        return $fields;
    }
}