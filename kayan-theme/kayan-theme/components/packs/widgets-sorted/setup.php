<?php  function Sort__this__list($list){
    $New__feature__list = array();
    $final__feature__list = array();
    $end__loop = 1;
    foreach ( $list as $s__k => $s___v ) {
        if( isset( $s___v['number'] ) && !empty( $s___v['number'] ) ){
            $s___v['ItemID'] = $s__k;
            $New__feature__list[ $s___v['number'] ] = $s___v;

            if( $s___v['number'] > $end__loop  ) $end__loop = $s___v['number'];

        }
    }
    #
    for ($i=0; $i <= $end__loop; $i++) { 
        if( isset( $New__feature__list[ $i ] ) ){
            $final__feature__list[ $New__feature__list[ $i ]['ItemID'] ] = $New__feature__list[ $i ];
        }
    }
    # 
    foreach ( $list as $t___k => $t___v) {
        if( !isset( $final__feature__list[ $t___k ] ) ){
            $t___v['ItemID'] = $t___k;
            $final__feature__list[ $t___k ] = $t___v;
        }
    }
    return $final__feature__list;
    
}