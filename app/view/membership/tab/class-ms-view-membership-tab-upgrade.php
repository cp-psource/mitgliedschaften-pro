<?php
/**
 * Tab: Edit Upgrade Paths
 *
 * Extends MS_View for rendering methods and magic methods.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage View
 */
class MS_View_Membership_Tab_Upgrade extends MS_View {

	/**
	 * Returns the contens of the dialog
	 *
	 * @since  1.0.0
	 *
	 * @return object
	 */
	public function to_html() {
		$fields = $this->get_fields();
		$membership = $this->data['membership'];

		ob_start();
		?>
		<div>
			<p>
			<?php
			printf(
				__( 'Hier kannst Du festlegen, welche Mitglieder %s abonnieren dürfen. Standardmäßig kann jeder abonnieren.', 'membership2' ),
				$membership->get_name_tag()
			);
			?>
			</p>
			<?php
			foreach ( $fields as $field ) {
				MS_Helper_Html::html_element( $field );
			}
			?>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters( 'ms_view_membership_upgrades_to_html', $html );
	}

	/**
	 * Prepares fields for the edit form.
	 *
	 * @since  1.0.1.0
	 * @return array
	 */
	protected function get_fields() {
		$args = array( 'include_guest' => false );
		$memberships = MS_Model_Membership::get_memberships( $args );
		$membership = $this->data['membership'];
		$action = MS_Controller_Membership::AJAX_ACTION_UPDATE_MEMBERSHIP;
		$nonce = wp_create_nonce( $action );

		$fields = array();

		/*
		 * The value of "allow_val" is negated, because the radio-slider is
		 * reversed. So allow_val == false means that upgrading is allowed.
		 *
		 * This is just a UI tweak, the function ->update_allowed() returns true
		 * when upgrading is allowed.
		 */
		$list = array();
		$list['guest'] = array(
			'allow' => __( 'Benutzer ohne Mitgliedschaft können sich anmelden', 'membership2' ),
			'allow_val' => ! $membership->update_allowed( 'guest' ),
		);
		foreach ( $memberships as $item ) {
			if ( $item->id == $membership->id ) { continue; }

			$list[$item->id] = array(
				'allow' => sprintf(
					__( 'Mitglieder von %s können sich anmelden', 'membership2' ),
					$item->get_name_tag()
				),
				'allow_val' => ! $membership->update_allowed( $item->id ),
			);

			if ( MS_Model_Addon::is_enabled( MS_Model_Addon::ADDON_MULTI_MEMBERSHIPS ) ) {
				$list[$item->id]['replace'] = sprintf(
					__( '%s im Abonnement kündigen', 'membership2' ),
					$item->get_name_tag()
				);
				$list[$item->id]['replace_val'] = $membership->update_replaces( $item->id );
			}
		}

		foreach ( $list as $id => $data ) {
			$fields[] = array(
				'id' => 'deny_update[' . $id . ']',
				'type' => MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'title' => $data['allow'],
				'value' => $data['allow_val'],
				'before' => __( 'Erlauben', 'membership2' ),
				'after' => __( 'Verweigern', 'membership2' ),
				'class' => 'reverse',
				'wrapper_class' => 'ms-block inline-label ms-allow',
				'ajax_data' => array( 1 ),
			);

			if ( ! empty( $data['replace'] ) ) {
				if ( MS_Addon_Prorate::is_active() ) {
					$after_label = __( 'Abbrechen und Pro-Rate', 'membership2' );
				} else {
					$after_label = __( 'Stornieren', 'membership2' );
				}

				$fields[] = array(
					'id' => 'replace_update[' . $id . ']',
					'type' => MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
					'title' => $data['replace'],
					'value' => $data['replace_val'],
					'before' => __( 'Behalten', 'membership2' ),
					'after' => $after_label,
					'class' => 'reverse',
					'wrapper_class' => 'ms-block inline-label ms-update-replace',
					'ajax_data' => array( 1 ),
				);
			}
			$fields[] = array(
				'type' => MS_Helper_Html::TYPE_HTML_SEPARATOR,
			);
		}

		foreach ( $fields as $key => $field ) {
			if ( ! empty( $field['ajax_data'] ) ) {
				if ( ! empty( $field['ajax_data']['action'] ) ) {
					continue;
				}

				if ( ! isset( $fields[ $key ]['ajax_data']['field'] ) ) {
					$fields[ $key ]['ajax_data']['field'] = $fields[ $key ]['id'];
				}
				$fields[ $key ]['ajax_data']['_wpnonce'] = $nonce;
				$fields[ $key ]['ajax_data']['action'] = $action;
				$fields[ $key ]['ajax_data']['membership_id'] = $membership->id;
			}
		}

		return $fields;
	}

};