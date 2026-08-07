<?php  function PostTypeArguments($data=array()){  
  extract( $data );

  if( !isset( $excloded ) ) $excloded = array();

  foreach ( array('post_format','page','attachment') as $key => $value) {
    $excloded[] = $value;
  }

  // ## Get In ## //
  $switch__get__in = false;
  if( isset( $getIn ) && !empty( $getIn ) ) {
    $switch__get__in = true;
    $getIn = ( ( is_array( $getIn ) ) ) ? $getIn : array($getIn);
  }

  if( !isset( $args )  ){
    $args = array(
      'public'   => true,
      #'_builtin'=>false
    ); 
  }

  if( !isset( $output ) )  $output = 'objects';
  if( !isset( $operator ) ) $operator = 'and';

  $return = array();
  $post_types = get_post_types( $args, $output, $operator ); 
  if ( $post_types ) {
    foreach ( $post_types  as $name => $post_type ) {
      if( !in_array( $name, $excloded ) && $switch__get__in == false || $switch__get__in == true && in_array( $name, $getIn  ) ){
        $return[$name] = $post_type;
      }
    }
  }
  return $return;
}

function get_all_post_types() {
  global $wpdb;
  $result = $wpdb->get_results( "SELECT DISTINCT post_type FROM {$wpdb->prefix}posts" );
  $post_types = array();
  foreach ( $result as $row ) {
      $post_types[$row->post_type] = $row->post_type;
  }
  return $post_types;
}


function get_all_taxonomies() {
  global $wpdb;
  $result = $wpdb->get_results( "SELECT DISTINCT taxonomy FROM {$wpdb->prefix}term_taxonomy" );
  $taxonomies = array();
  foreach ( $result as $row ) {
    $taxonomies[$row->taxonomy] = $row->taxonomy;
  }
  return $taxonomies;
}

function ExtractValues($data){
  $TaxonomyesObject = array();
  foreach (TaxonomyesObject() as $tkey => $tmeky) {
    $TaxonomyesObject[$tkey] = $tmeky->label;
  } 
  return $TaxonomyesObject;
}
function TaxonomyesObject($data=array()){
  extract( $data );

  if( !isset( $excloded ) ) $excloded = array();

  # Get In
    $switch__get__in = false;
    if( isset( $getIn ) && !empty( $getIn ) ){
      $switch__get__in = true;
      $getIn = ( ( is_array( $getIn ) ) ) ? $getIn : array($getIn);
    } 


  if( !isset( $output ) )  $output = 'objects';
  if( !isset( $operator ) ) $operator = 'and';

  $return = array();

  $args = array(
    'public'   => true,
    #'_builtin'=>false
  ); 

  $taxonomies = get_taxonomies( $args, $output, $operator ); 
  if ( $taxonomies ) {
    foreach ( $taxonomies  as $taxonomy ) {
      if( !in_array( $taxonomy->name, $excloded ) && $switch__get__in == false || $switch__get__in == true && in_array( $taxonomy->name, $getIn  ) ){
        $return[$taxonomy->name] = $taxonomy;
      }
    }
  }

  return $return;
}