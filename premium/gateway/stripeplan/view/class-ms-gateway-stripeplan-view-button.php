<?php

class MS_Gateway_Stripeplan_View_Button extends MS_View {

    public function to_html() {
        $fields         = $this->prepare_fields();
        $subscription   = $this->data['ms_relationship'];
        $invoice        = $subscription->get_next_billable_invoice();
        $member         = MS_Model_Member::get_current_member();
        $gateway        = $this->data['gateway'];

        $action_url = apply_filters(
            'ms_gateway_stripeplan_view_button_form_action_url',
            '' // Wird nicht benötigt, Stripe Checkout übernimmt die Weiterleitung
        );

        $row_class = 'gateway_' . $gateway->id;
        if ( ! $gateway->is_live_mode() ) {
            $row_class .= ' sandbox-mode';
        }

        // Hier muss im Backend eine Stripe Checkout Session erzeugt werden!
        // $checkout_session_url sollte die URL zur Stripe Checkout Session sein.
        $checkout_session_url = $this->data['checkout_session_url'];

        ob_start();
        ?>
        <form id="stripe-checkout-form-<?php echo esc_attr($gateway->id); ?>" action="#" method="POST">
            <?php
            foreach ( $fields as $field ) {
                MS_Helper_Html::html_element( $field );
            }
            ?>
            <button type="button" class="ms-stripe-checkout-btn" onclick="window.location.href='<?php echo esc_url( $checkout_session_url ); ?>'">
                <?php echo esc_html( $gateway->pay_button_url ? $gateway->pay_button_url : __( 'Jetzt bezahlen', 'membership2' ) ); ?>
            </button>
        </form>
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
            '_wpnonce'  => array(
                'id'        => '_wpnonce',
                'type'      => MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value'     => wp_create_nonce(
                    $gateway->id . '_' . $subscription->id
                ),
            ),
            'gateway'   => array(
                'id'        => 'gateway',
                'type'      => MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value'     => $gateway->id,
            ),
            'ms_relationship_id' => array(
                'id'        => 'ms_relationship_id',
                'type'      => MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value'     => $subscription->id,
            ),
            'step'  => array(
                'id'        => 'step',
                'type'      => MS_Helper_Html::INPUT_TYPE_HIDDEN,
                'value'     => $this->data['step'],
            ),
        );

        return $fields;
    }
}