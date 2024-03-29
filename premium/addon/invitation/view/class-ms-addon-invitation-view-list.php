<?php
/**
 * Renders Invitation list.
 *
 * Extends MS_View for rendering methods and magic methods.
 *
 * @since  1.0.0
 *
 * @package Membership2
 * @subpackage View
 */
class MS_Addon_Invitation_View_List extends MS_View {

	/**
	 * Create view output.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function to_html() {
		$code_list = MS_Factory::create( 'MS_Addon_Invitation_Helper_Listtable' );
		$code_list->prepare_items();

		$title = __( 'Einladungen', 'membership2' );
		$add_new_button = array(
			'id' => 'add_new',
			'type' => MS_Helper_Html::TYPE_HTML_LINK,
			'url' => MS_Controller_Plugin::get_admin_url(
				MS_Addon_Invitation::SLUG,
				array( 'action' => 'edit', 'invitation_id' => 0 )
			),
			'value' => __( 'Neuen Code hinzufügen', 'membership2' ),
			'class' => 'button',
		);

		ob_start();
		?>
		<div class="wrap ms-wrap">
			<?php
			MS_Helper_Html::settings_header(
				array(
					'title' => $title,
					'title_icon_class' => 'wpmui-fa wpmui-fa-ticket',
				)
			);
			?>
			<div>
				<?php MS_Helper_Html::html_element( $add_new_button );?>
			</div>

			<form action="" method="post">
				<?php $code_list->display(); ?>
			</form>
			<p><em>
				<?php
				_e( 'Standardmäßig sind alle Mitgliedschaften geschützt und erfordern einen Einladungscode zur Registrierung. <br> Du kannst dies manuell für einzelne Mitgliedschaften über eine neue Einstellung in den Einstellungen "Zahlungsoptionen" jeder Mitgliedschaft ändern.', 'membership2' );
				?>
			</em></p>
		</div>

		<?php
		$html = ob_get_clean();

		return apply_filters( 'ms_addon_invitation_view_list_to_html', $html, $this );
	}
}