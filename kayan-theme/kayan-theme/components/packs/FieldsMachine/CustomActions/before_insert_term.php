<?php  class before_insert_Term_page {
	function __construct() {
		$this->YC__CFM = new YC__CFM;
		$this->BeforeInsert__path = $this->YC__CFM->YC__CFM_Path.'BeforeInsert/*';
		$this->BeforeInsert__Packages = glob( $this->BeforeInsert__path );	
		$this->TaxonomyesObject = get_all_taxonomies();	
		$this->exclode__terms = array('nav_menu');
	}
	public function term_insert_fields(){
		global $before_insert_terms__list;

		do_action('before_insert_terms__list');

		# TAXONOMY TYPE NOW .
			$taxonomy = (isset( $_GET['taxonomy'] )) ? $_GET['taxonomy'] : 'category';

		# NOT FOUND THIS POST TYPE IN INSERT LIST '$before_insert_terms__list'
			if( !isset( $before_insert_terms__list[ $taxonomy ] ) ) return ;

		# ADD BODY CUSTOM CLASS .
			add_filter( 'admin_body_class', function( $classes ) {
				$taxonomy = (isset( $_GET['taxonomy'] )) ? $_GET['taxonomy'] : 'category';
				$classes .= ' YC-before_insert_tax_page';
				$classes .= ' YC-before_insert_tax_objectType_'.$taxonomy;
				return $classes; 
			});
		
		# FIELDS INSERT .
			foreach ( $before_insert_terms__list[ $taxonomy ]['fields'] as $tax => $field__data ) {
				echo '<div class="-widgets-before-inser-tax">';
					$this->YC__CFM->Fields__Part( $field__data['type'], $field__data );
				echo '</div>';
			}
	}

	public function save__term_fields($term_id) {
		global $before_insert_terms__list;

		do_action('before_insert_terms__list');

		# TAXONOMY TYPE NOW .
			$taxonomy = (isset( $_POST['taxonomy'] )) ? $_POST['taxonomy'] : 'category';

		# FIELDS UPDATED .	
			if( isset( $before_insert_terms__list[$taxonomy] ) ){
				foreach ( $before_insert_terms__list[$taxonomy]['fields'] as $tax => $field__data ) {

					if( isset( $_POST[ $field__data['id'] ] ) ) {
						$Id__Value = sanitize_text_field( $_POST[ $field__data['id'] ] );
						update_term_meta($term_id,  $field__data['id'] , $Id__Value );

						# FIELD TYPE FILE __ ID .
							if( isset( $_POST[ $field__data['id'].'_id' ] ) ){
								$In_id__Value = sanitize_text_field( $_POST[ $field__data['id'].'_id' ] );
								update_term_meta($term_id, $field__data['id'].'_id' , $In_id__Value );
							}else{
								delete_term_meta($term_id, $field__data['id'].'_id' );
							}

					}else{
						delete_term_meta($term_id,  $field__data['id'] );
					}
					
				}
			}
	}

	public function validate_default_field($term, $taxonomy) {

		global $before_insert_terms__list;

		do_action('before_insert_terms__list');

		if( !isset( $before_insert_terms__list[ $taxonomy ] ) ) return $term;
		if( in_array( $taxonomy,$this->exclode__terms ) ) return $term;
		# FIELDS UPDATED .	
			foreach ( $before_insert_terms__list[ $taxonomy ]['fields'] as $tax => $field__data ) {

				if( !isset( $_POST[ $field__data['id'] ] ) || ( isset( $_POST[ $field__data['id'] ] ) && empty( $_POST[ $field__data['id'] ] ) ) ){
					return new WP_Error('field_required', 'حقل تحديد اللغة الاساسية * مطلوب .');
				}
			}

	    return $term;
	}

	public function Setup() {

		foreach ( $this->TaxonomyesObject as $taxonomy => $argums ) {
			if( !in_array( $taxonomy,$this->exclode__terms) ){
				add_action("{$taxonomy}_add_form_fields", array($this,'term_insert_fields'), 10, 3);
				add_action("created_{$taxonomy}",array($this,'save__term_fields' ) );
			}
		}

		add_filter('pre_insert_term', array( $this,'validate_default_field'), 10, 2);
	}

}
(new before_insert_Term_page)->Setup();