<?php
/**
 * Membership List Table
 *
 * @since  1.0.0
 */
class MS_Rule_CptGroup_ListTable extends MS_Helper_ListTable_Rule {

	public $_column_headers;

	protected $id = MS_Rule_CptGroup::RULE_ID;

	public function __construct( $model ) {
		parent::__construct( $model );
		$this->name['singular'] = __( 'Benutzerdefinierter Beitragstyp', 'membership2' );
		$this->name['plural'] 	= __( 'Benutzerdefinierte Beitragstypen', 'membership2' );
	}

	public function get_columns() {
		return apply_filters(
			"membership_helper_listtable_{$this->id}_columns",
			array(
				'cb' 		=> true,
				'name' 		=> __( 'Benutzerdefinierter Beitragstyp', 'membership2' ),
				'access' 	=> true,
				'dripped' 	=> true,
			)
		);
	}

	public function get_sortable_columns() {
		return apply_filters(
			"membership_helper_listtable_{$this->id}_sortable_columns",
			array(
				'name' 		=> 'name',
				'access' 	=> 'access',
			)
		);
	}

	public function column_name( $item, $column_name ) {
		return $item->name;
	}

}
