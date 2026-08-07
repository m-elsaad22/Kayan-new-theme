<?php  if( !isset( $UniqId ) ) $UniqId = uniqid();
# OBJECT TYPES ( 'posts' || 'taxonomies' || 'users' || 'database'  )
    if( !isset( $object__type ) ) $object__type = 'posts';

if( !isset( $AjaxPart ) ) $AjaxPart = false;

# OBJECT NAME -> 'post_type' OR 'taxonomies' => name ... IF 'users' DEFULT -> false .. ;
    if( !isset( $object__name ) && $object__type != 'users' ) $object__name = 'post';

    if( !isset( $object__name ) && $object__type == 'database' ) $object__name = 'DBArguments';

    if( !isset( $object__name ) && $object__type == 'users' ) $object__name = false;

# PER -> POSTS PER PAGE OR NUMBER (INTEGER) .
    if( !isset( $per ) ) $per = 10;

# PAGED .   
    $paged = $this->Paged();

# SELECT PART .
    if( !isset( $part__name ) ) $part__name = 'post';

# SEND OBJECT TO PART $YOUR_NAME .
    if( !isset( $part_object__name ) ) $part_object__name = 'post';

# SEND PART SCROLL CENTER CLASS $YOUR_NAME .
    if( !isset( $scroll___center___class ) ) $scroll___center___class = '-defualt-object--center';

# SEND YOUR BOX PART CUSTOM OPTIONS .
    if( !isset( $part___options ) ) $part___options = array();

# OPEN LOAD MORE 'TRUE' OR 'FALSE' .
    if( !isset( $ScrollLoader ) ) $ScrollLoader = false;

# AUTO LOAD MORE 'TRUE' OR 'FALSE' && IMPORTANT SET $ScrollLoader 'TRUE' .
    if( !isset( $AutoLoadmore ) ) $AutoLoadmore = false;

# ADD CUSTOM MORE BUTTON .
    if( !isset( $custom___more__btn ) ) $custom___more__btn = array();
# BULIDING QUERY ARGUMENTS .
    
    # IF NOT SEND '$PostsArguments';

        if( !isset( $PostsArguments ) ){

            if( $object__type == 'posts' ){

                $PostsArguments = array(
                    'post_type'=>$object__name,
                    'posts_per_page'=>$per,
                );

                # GET POSTS IN SELECTED TERM .
                    if( isset( $ObjectTerms ) ){
                        $PostsArguments['tax_query']['relation'] = 'AND';
                        
                        foreach ($ObjectTerms as $s => $mm) {
                            $PostsArguments['tax_query'][] = array(
                                'taxonomy'  => $mm->taxonomy,
                                'field'   => ($mm->taxonomy == 'category') ? 'term_id' : 'slug',
                                'terms'   => ($mm->taxonomy == 'category') ? $mm->term_id : $mm->slug,
                                'operator'  => 'IN'
                            );
                        }
                    }

                if( isset( $post__not_in ) ){
                    $PostsArguments['post__not_in'] = ( ( is_array( $post__not_in ) ) ) ? $post__not_in : array($post__not_in);
                }
            }

            if( $object__type == 'taxonomies' ){

                $PostsArguments = array(
                    'hide_empty'=>0,
                    'taxonomy'=>$object__name,
                    'number'=>$per,
                );

                if( isset( $parent ) )  $PostsArguments['parent'] = $parent;

                # GET POSTS IN SELECTED TERM .
                    if( isset( $ObjectTerms ) ){
                        $PostsArguments['tax_query']['relation'] = 'AND';
                        
                        foreach ($ObjectTerms as $s => $mm) {
                            $PostsArguments['tax_query'][] = array(
                                'taxonomy'  => $mm->taxonomy,
                                'field'   => ($mm->taxonomy == 'category') ? 'term_id' : 'slug',
                                'terms'   => ($mm->taxonomy == 'category') ? $mm->term_id : $mm->slug,
                                'operator'  => 'IN'
                            );
                        }
                    }


                if( isset( $post__not_in ) ){
                    $PostsArguments['exclude'] = ( ( is_array( $post__not_in ) ) ) ? array_values($post__not_in) : array($post__not_in);
                }
            }

            if( $object__type == 'database' ){
                $PostsArguments = array();
            }

            if( $object__type == 'users' ){

                $PostsArguments = array(
                    'order'        => 'ASC',
                    'number'       => $per,
                    'YC_Current_Users'=>true,
                );

                # GET USERS IN JUST THIS ROLE .
                    if( isset( $role__in ) ){
                        $PostsArguments['role__in'] = $role__in;
                    }
            }

        }

    # FOUND TOTAL COUNT .
        if( $object__type == 'posts' ){
            $Founder = new WP_Query($PostsArguments);
            $CountQuery = $Founder->found_posts;
        }

        if( $object__type == 'taxonomies' ){
            $CountQuery = wp_count_terms($PostsArguments);
        }

        if( $object__type == 'users' ){
            $user_query = new WP_User_Query($PostsArguments);
            $users = $user_query->get_results();
            $CountQuery = $user_query->get_total();
        }

        if( $object__type == 'database' ){
            $database = new $object__name;
            $CountQuery = $database->count($PostsArguments);
            if( !is_integer( $CountQuery ) && !is_array( $CountQuery ) ) $CountQuery = (INT) $CountQuery;
        }

    # INSERT OBJECT COUNT . 

        if( $paged > 1 ){
            if( $object__type == 'posts' || ( $object__type == 'database' && !isset( $offset ) ) ){
                $PostsArguments['paged'] = $paged;
            }else{
                $offset = ( $paged-1 ) * $per;
                $PostsArguments['offset'] = $offset;
            }
        }

    # CREATE OBJECT LIST .
        $LoadMoreAction = true;
        $object____lists = array();
        $current__result__count = 0;

        # IF $object__type == 'posts'
            if( $object__type == 'posts' ){

                # SEND OBJECT TO PART $YOUR_NAME .
                    if( !isset( $part_object__name ) ) $part_object__name = 'post';

                    #print_r($PostsArguments);
                # EACH OBJECTS .    
                    foreach ( get_posts( $PostsArguments ) as $post ) {$current__result__count++;
                        $object____lists[ $post->ID ] = $post;
                    }
            }

        # IF $object__type == 'taxonomy'
            if( $object__type == 'taxonomies' ){

                # SEND OBJECT TO PART $YOUR_NAME .
                    if( !isset( $part_object__name ) ) $part_object__name = 'term';

                # EACH OBJECTS .    
                    foreach ( get_terms( $PostsArguments ) as $term ) {$current__result__count++;
                        $object____lists[ $term->term_id ] = $term;
                    }
            }

        # IF $object__type == 'users'
            if( $object__type == 'users' ){
                if( !isset( $user_query ) ) $user_query = new WP_User_Query($PostsArguments);

                # SEND OBJECT TO PART $YOUR_NAME .
                    if( !isset( $part_object__name ) ) $part_object__name = 'user';

                # EACH OBJECTS .    
                    foreach ( $user_query->get_results() as $user ) {$current__result__count++;
                        $object____lists[ $user->ID ] = $user;
                    }
            }

        # IF $object__type == 'database'
            if( $object__type == 'database' ){

                $database = new $object__name;
                #

                $item__request = $database->get($PostsArguments,0,$per);
                if( $item__request != false ){
                    # SEND OBJECT TO PART $YOUR_NAME .
                        if( !isset( $part_object__name ) ) $part_object__name = 'object';

                    # EACH OBJECTS .
                        foreach ( $item__request as $single___obj ) {$current__result__count++;
                            $single___obj->Object__data = Un__Serialize( $single___obj->Object__data );
                            $object____lists[ $single___obj->Object__id ] = $single___obj;
                        }
                        
                }
            }

    # LOAD MORE OPTIONS .
        # CHECK QUERY COUNT .
            #echo $CountQuery;die;
            if( $current__result__count == 0 
                || ( isset( $PostsArguments['offset'] ) && $CountQuery <= ( $PostsArguments['offset'] + $current__result__count ) ) 
                || ( isset( $PostsArguments['paged'] ) && $CountQuery <= ( ( ( $PostsArguments['paged']-1 ) * $per ) + $current__result__count ) ) 
                || ( $current__result__count < $per || ( $current__result__count + 1 <= $per ) ) 
            ) $LoadMoreAction = false;

        #echo ( ( $LoadMoreAction == false ) ) ? 'FALSE'  : 'TRUE';
    # LOAD MORE ATTRIBUTES .

        $LoaMoreAttr = ( ( $LoadMoreAction != false && $ScrollLoader != false ) ) ? 'data-loadmore="'.base64_encode( json_encode( $PostsArguments ) ).'" data-finish="false"' : 'data-finish="true"';

    # SEND YOUR CUSTOM ATTRIBUTES TO SCROLLER CENTER .  
        if( isset( $Attributes ) && !empty( $Attributes ) ){
            $LoaMoreAttr .= ' '.$Attributes;
        }

    # SEND PART ACTION ATTRIBUTES .
        $PartBox = $vars;
        unset( $PartBox['PostsArguments'] );


echo ( ( $AjaxPart != true ) ) ? '<div class="'.$scroll___center___class.' -ScrollerCenter -Objects-center-obType-'.$part_object__name.'" '.$LoaMoreAttr.' data-autoloaded="'.$AutoLoadmore.'" data-uniqid="'.$UniqId.'" data-part="'.base64_encode( json_encode( $PartBox ) ).'">': '';

    #print_r( $object____lists );
    if( !empty( $object____lists ) ){

        foreach ( $object____lists as $object__single___item ) {
            $this->Blade('Box', array( $part_object__name=>$object__single___item ,'part___options'=>$part___options), $part__name );
        }

    }else if( isset( $show___empty__part ) ){
        if( !isset( $data___empty__part ) ) $data___empty__part = array();
        echo '<div class="--remove-insert--post">';
            $this->Blade( 'empty__objects',array_merge( array('AjaxPart'=>$AjaxPart),$data___empty__part ), $show___empty__part);
        echo '</div>';
        
    }

echo ( ( $AjaxPart != true ) ) ? '</div>': '';

if( !empty( $custom___more__btn ) ){
    if( !isset( $custom___more__btn['title'] ) ) $custom___more__btn['title'] = 'مشاهدة المزيد';
    echo ( ( $AjaxPart != true ) ) ? '<LoadMore--InpuArea><PostsScrollLoader class="PostsScrollLoader">'.( ( isset( $custom___more__btn['URL'] ) ) ? '<a href="'.$custom___more__btn['URL'].'">' : '' ).'<i class="fa-solid fa-square-plus"></i><span>'.$custom___more__btn['title'].'</span>'.( ( isset( $custom___more__btn['URL'] ) ) ? '</a>' : '' ).'</PostsScrollLoader></LoadMore--InpuArea>' : '';

}else if( $ScrollLoader == true && $LoadMoreAction == true ){
    echo ( ( $AjaxPart != true ) ) ? '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'.$UniqId.'" data-aciton-type="posts" class="PostsScrollLoader LoadMorePostsBTN" '.( ( $AutoLoadmore != false) ? 'style="display:none"' : '').'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>' : '';
    
}

if( $AjaxPart == true ){
    $json = array(
        'arguments'=>$PostsArguments,
        'ScrollLoader'=>$LoadMoreAction,
    );
    echo '<CutAjax>'.json_encode($json, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE).'</CutAjax>';
}