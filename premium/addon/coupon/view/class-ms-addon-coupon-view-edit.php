<?php

/**
 * Render Coupon add/edit view.
 *
 * Extends MS_View for rendering methods and magic methods.
 *
 * @since  1.0.0
 *
 * @package Membership2
 * @subpackage View
 */
class MS_Addon_Coupon_View_Edit extends MS_View {

	/**
	 * Create view output.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function to_html() {
		$fields = $this->prepare_fields();
		$form_url = esc_url_raw(
			remove_query_arg( array( 'action', 'coupon_id' ) )
		);

		if ( $this->data['coupon']->is_valid() ) {
			$title = __( 'Gutschein bearbeiten', 'membership2' );
		} else {
			$title = __( 'Gutschein hinzufügen', 'membership2' );
		}

		ob_start();
		// Render tabbed interface.
		?>
		<div class="ms-wrap">
			<?php
			MS_Helper_Html::settings_header(
				array(
					'title' => $title,
					'title_icon_class' => 'wpmui-fa wpmui-fa-pencil-square',
				)
			);
			?>
			<form action="<?php echo esc_url( $form_url ); ?>" method="post" class="ms-form">
				<?php MS_Helper_Html::settings_box( $fields, '', '', 'static', 'ms-small-form' ); ?>
			</form>
			<div class="clear"></div>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( 'ms_addon_coupon_view_edit_to_html', $html, $this );
	}

	/**
	 * Prepare html fields.
	 *
	 * @since  1.0.0
	 *
	 * @return array
	 */
	protected function prepare_fields() {
		$coupon = $this->data['coupon'];
		$fields = array(
			'code' => array(
				'id' => 'code',
				'title' => __( 'Gutscheincode', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_TEXT,
				'value' => $coupon->code,
				'class' => 'widefat',
			),
			'discount' => array(
				'id' => 'discount',
				'title' => __( 'Rabatt', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_NUMBER,
				'value' => $coupon->discount,
				'config' => array(
					'step' => 'any',
					'min' => 0,
				),
			),
			'duration' => array(
				'id' => 'duration',
				'title' => __( 'Bei wiederkehrenden Zahlungen wird nur ein Gutschein verwendet', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_SELECT,
				'field_options' => $coupon->get_discount_duration(),
				'value' => $coupon->duration,
			),
			'discount_type' => array(
				'id' => 'discount_type',
				'title' => __( 'Rabattart', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_SELECT,
				'field_options' => $coupon->get_discount_types(),
				'value' => $coupon->discount_type,
			),
			'start_date' => array(
				'id' => 'start_date',
				'title' => __( 'Anfangsdatum', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_DATEPICKER,
				'value' => ( $coupon->start_date ) ? $coupon->start_date : MS_Helper_Period::current_date(),
				'class' => 'ms-date',
			),
			'expire_date' => array(
				'id' => 'expire_date',
				'title' => __( 'Ablaufdatum', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_DATEPICKER,
				'value' => $coupon->expire_date,
				'class' => 'ms-date',
			),
			'membership_id' => array(
				'id' => 'membership_id',
				'title' => __( 'Auf diese Mitgliedschaften kann ein Gutschein angewendet werden', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_CHECKBOX,
				'field_options' => $this->data['memberships'],
				'value' => $coupon->membership_id,
			),
			'max_uses' => array(
				'id' => 'max_uses',
				'title' => __( 'Max. einlösbar', 'membership2' ),
				'type' => MS_Helper_Html::INPUT_TYPE_NUMBER,
				'value' => $coupon->max_uses,
				'config' => array(
					'step' => '1',
					'min' => 0,
				),
			),
			'coupon_id' => array(
				'id' => 'coupon_id',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $coupon->id,
			),
			'_wpnonce' => array(
				'id' => '_wpnonce',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => wp_create_nonce( $this->data['action'] ),
			),
			'action' => array(
				'id' => 'action',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $this->data['action'],
			),
			'separator' => array(
				'type' => MS_Helper_Html::TYPE_HTML_SEPARATOR,
			),
			'cancel' => array(
				'id' => 'cancel',
				'type' => MS_Helper_Html::TYPE_HTML_LINK,
				'title' => __( 'Abbrechen', 'membership2' ),
				'value' => __( 'Abbrechen', 'membership2' ),
				'url' => esc_url_raw( remove_query_arg( array( 'action', 'coupon_id' ) ) ),
				'class' => 'wpmui-field-button button',
			),
			'submit' => array(
				'id' => 'submit',
				'type' => MS_Helper_Html::INPUT_TYPE_SUBMIT,
				'value' => __( 'Änderungen speichern', 'membership2' ),
			),
		);

		return apply_filters(
			'ms_addon_coupon_view_edit_prepare_fields',
			$fields,
			$this
		);
	}
}