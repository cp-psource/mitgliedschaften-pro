<?php
/**
 * Membership List Table
 *
 * @since  1.0.0
 */
class MS_Rule_CptItem_ListTable extends MS_Helper_ListTable_Rule {

	protected $id = MS_Rule_CptItem::RULE_ID;

	public function __construct( $model ) {
		parent::__construct( $model );
		$this->name['singular'] = __( 'Benutzerdefinierter Beitrag', 'membership2' );
		$this->name['plural'] 	= __( 'Benutzerdefinierte Beiträge', 'membership2' );
	}

	public function get_columns() {
		return apply_filters(
			"membership_helper_listtable_{$this->id}_columns",
			array(
				'cb' => true,
				'post_title'	=> __( 'Benutzerdefinierter Beitragstitel', 'membership2' ),
				'post_type' 	=> __( 'Beitrags-Typ', 'membership2' ),
				'access' 		=> true,
				'dripped' 		=> true,
			)
		);
	}

	public function get_sortable_columns() {
		return apply_filters(
			"membership_helper_listtable_{$this->id}_sortable_columns",
			array(
				'post_title' 	=> 'post_title',
				'post_type' 	=> 'post_type',
				'access' 		=> 'access',
			)
		);
	}

	public function column_post_title( $item ) {
		$actions = array(
			sprintf(
				'<a href="%s">%s</a>',
				get_edit_post_link( $item->id, true ),
				__( 'Bearbeiten', 'membership2' )
			),
			sprintf(
				'<a href="%s">%s</a>',
				get_permalink( $item->id ),
				__( 'Ansehen', 'membership2' )
			),
		);

		$actions = apply_filters(
			'ms_rule_' . $this->id . '_column_actions',
			$actions,
			$item
		);

		return sprintf(
			'%1$s %2$s',
			$item->post_title,
			$this->row_actions( $actions )
		);
	}

	public function column_post_type( $item, $column_name ) {
		return $item->post_type;
	}

}
