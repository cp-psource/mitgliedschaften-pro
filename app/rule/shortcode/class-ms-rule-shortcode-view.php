<?php

class MS_Rule_Shortcode_View extends MS_View {

	public function to_html() {
		$membership = MS_Model_Membership::get_base();
		$rule = $membership->get_rule( MS_Rule_Shortcode::RULE_ID );

		$rule_listtable = new MS_Rule_Shortcode_ListTable( $rule );
		$rule_listtable->prepare_items();

		$header_data = apply_filters(
			'ms_view_membership_protectedcontent_header',
			array(
				'title' => __( 'Wähle zum Schutz Shortcodes', 'membership2' ),
				'desc' 	=> '',
			),
			MS_Rule_Shortcode::RULE_ID,
			$this
		);

		ob_start();
		?>
		<div class="ms-settings">
			<?php
			MS_Helper_Html::settings_tab_header( $header_data );

			$rule_listtable->views();
			$rule_listtable->search_box( __( 'Shortcodes', 'membership2' ) );
			?>
			<form action="" method="post">
				<?php
				$rule_listtable->display();

				do_action(
					'ms_view_membership_protectedcontent_footer',
					MS_Rule_Shortcode::RULE_ID,
					$this
				);
				?>
			</form>
		</div>
		<?php

		MS_Helper_Html::settings_footer();
		return ob_get_clean();
	}

}