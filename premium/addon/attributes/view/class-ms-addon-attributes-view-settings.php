<?php

class MS_Addon_Attributes_View_Settings extends MS_View {

	/**
	 * Returns the HTML code of the Settings form.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function render_tab() {
		$groups = $this->prepare_fields();

		ob_start();
		?>
		<div class="ms-addon-wrap">
			<?php
			MS_Helper_Html::settings_tab_header(
				array(
					'title' => __( 'Benutzerdefinierte Mitgliedschaftsattribute', 'membership2' ),
					'desc' => __( 'Definiere benutzerdefinierte Felder, die auf der Seite zum Bearbeiten von Mitgliedschaften verfügbar sind.', 'membership2' ),
				)
			);

			foreach ( $groups as $key => $fields ) {
				echo '<div class="ms-group ms-group-' . esc_attr( $key ) . '">';
				foreach ( $fields as $field ) {
					MS_Helper_Html::html_element( $field );
				}
				echo '</div>';
			}
			MS_Helper_Html::html_separator();

			$help_link = MS_Controller_Plugin::get_admin_url(
				'help',
				array( 'tab' => 'shortcodes' )
			);

			printf(
				'<p>%s</p><ul><li>%s</li><li>%s</li><li>%s</li><li>%s</li></ul>',
				__( 'So verwendest Du benutzerdefinierte Attributwerte:', 'membership2' ),
				sprintf(
					__( 'Über den %sShortcode%s %s', 'membership2' ),
					'<a href="' . $help_link . '#ms-membership-buy">',
					'</a>',
					'<code>[<b>' . MS_Addon_Attributes::SHORTCODE . '</b> slug="slug" id="..."]</code>'
				),
				sprintf(
					__( 'Über System-Filter %s', 'membership2' ),
					'<code>$val = apply_filters( "<b>ms_membership_attr</b>", "", "slug", $membership_id );</code>'
				),
				sprintf(
					__( 'Holen über PHP-Funktion %s', 'membership2' ),
					'<code>$val = <b>ms_membership_attr</b>( "slug", $membership_id );</code>'
				),
				sprintf(
					__( 'Über PHP-Funktion %s einstellen', 'membership2' ),
					'<code><b>ms_membership_attr_set</b>( "slug", $val, $membership_id );</code>'
				)
			);
			?>
		</div>
		<?php
		$html = ob_get_clean();
		echo $html;
	}

	/**
	 * Prepare fields that are displayed in the form.
	 *
	 * @since  1.0.1.0
	 * @return array
	 */
	protected function prepare_fields() {
		$action_save = MS_Addon_Attributes::AJAX_ACTION_SAVE_SETTING;
		$action_delete = MS_Addon_Attributes::AJAX_ACTION_DELETE_SETTING;

		$attribute_types = array(
			'text' => __( 'Einfaches Textfeld', 'membership2' ),
			'number' => __( 'Numerisches Feld (Ganzzahl)', 'membership2' ),
			'textarea' => __( 'Mehrzeiliger Text', 'membership2' ),
			'bool' => __( 'Yes|No', 'membership2' ),
		);

		$field_def = MS_Addon_Attributes::list_field_def();
		$fieldlist = array();
		$fieldlist[] = array(
			__( 'Attribut Titel', 'membership2' ),
			__( 'Attribut Slug', 'membership2' ),
			__( 'Attribut Typ', 'membership2' ),
			__( 'Attribut Infos', 'membership2' ),
		);
		foreach ( $field_def as $field ) {
			$fieldlist[] = array(
				$field->title,
				'<code>' . $field->slug. '</code>',
				$field->type,
				$field->info,
			);
		}

		$fields = array();

		$fields['fields'] = array(
			'add_field' => array(
				'id' => 'add_field',
				'type' => MS_Helper_Html::INPUT_TYPE_BUTTON,
				'value' => __( 'Neues Attribut', 'membership2' ),
				'class' => 'add_field',
			),
			'fieldlist' => array(
				'id' => 'fieldlist',
				'type' => MS_Helper_Html::TYPE_HTML_TABLE,
				'value' => $fieldlist,
				'field_options' => array(
					'head_row' => true,
				),
				'class' => 'field-list',
			),
		);

		$fields['editor no-auto-init'] = array(
			'title' => array(
				'id' => 'title',
				'class' => 'title',
				'type' => MS_Helper_Html::INPUT_TYPE_TEXT,
				'title' => __( 'Attribut Name', 'membership2' ),
				'desc' => __( 'Ein vom Menschen lesbarer Titel des Attributs.', 'membership2' ),
			),
			'slug' => array(
				'id' => 'slug',
				'class' => 'slug',
				'type' => MS_Helper_Html::INPUT_TYPE_TEXT,
				'title' => __( 'Attribut Slug', 'membership2' ),
				'desc' => __( 'Du verwendest den Slug im Attribut-Shortcode und im PHP-Code, um auf einen Wert zuzugreifen.', 'membership2' ),
			),
			'type' => array(
				'id' => 'type',
				'class' => 'type',
				'type' => MS_Helper_Html::INPUT_TYPE_SELECT,
				'title' => __( 'Attribut Typ', 'membership2' ),
				'desc' => __( 'Entscheide welche Art von Daten vom Attribut gespeichert werden sollen.', 'membership2' ),
				'field_options' => $attribute_types,
			),
			'info' => array(
				'id' => 'info',
				'class' => 'info',
				'type' => MS_Helper_Html::INPUT_TYPE_TEXT_AREA,
				'title' => __( 'Attribut Infos', 'membership2' ),
				'desc' => __( 'Zusätzliche Details werden im Mitgliedereditor angezeigt. Nur Administratorbenutzer können diesen Wert sehen.', 'membership2' ),
			),
			'old_slug' => array(
				'id' => 'old_slug',
				'class' => 'old_slug',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
			),
			'action_save' => array(
				'id' => 'action_save',
				'class' => 'action_save',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $action_save,
			),
			'nonce_save' => array(
				'id' => 'nonce_save',
				'class' => 'nonce_save',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => wp_create_nonce( $action_save ),
			),
			'action_delete' => array(
				'id' => 'action_delete',
				'class' => 'action_delete',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $action_delete,
			),
			'nonce_delete' => array(
				'id' => 'nonce_delete',
				'class' => 'nonce_delete',
				'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => wp_create_nonce( $action_delete ),
			),
			'buttons' => array(
				'type' => MS_Helper_Html::TYPE_HTML_TEXT,
				'value' =>
					MS_Helper_Html::html_element(
						array(
							'id' => 'btn_delete',
							'class' => 'btn_delete button-link danger',
							'type' => MS_Helper_Html::INPUT_TYPE_BUTTON,
							'value' => __( 'Löschen', 'membership2' ),
						),
						true
					) .
					MS_Helper_Html::html_element(
						array(
							'id' => 'btn_cancel',
							'class' => 'btn_cancel close',
							'type' => MS_Helper_Html::INPUT_TYPE_BUTTON,
							'value' => __( 'Abbrechen', 'membership2' ),
						),
						true
					) .
					MS_Helper_Html::html_element(
						array(
							'id' => 'btn_save',
							'class' => 'btn_save button-primary',
							'type' => MS_Helper_Html::INPUT_TYPE_BUTTON,
							'value' => __( 'Attribut speichern', 'membership2' ),
						),
						true
					),
				'class' => 'buttons',
			)
		);

		return $fields;
	}
}