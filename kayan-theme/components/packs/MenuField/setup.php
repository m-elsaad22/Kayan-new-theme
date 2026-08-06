<?php class MenuCustomFields {
    function __construct($args=array()) {
        $this->args = $args;
        $this->ThemeStatic = new ThemeStatic;
        $this->YC__CFM = new YC__CFM;
    }

    # CLASS TOOLS .

        public function ObjectFields(){
            return array(
                array(
                    "title"  => "الايقونة ",
                    "type"  => "text",
                    "id"    => "_menu_item_icon",
                ),
                array(
                    "title"  => "MegaMenu",
                    "type"  => "checkbox",
                    "id"    => "_menu_MegaMenu_Action",
                ),
                array(
                    'id'=>'MegaMenu_data',
                    'type'=>'Models-Selector',
                    'title'=>'إعدادات رابط خطط الاسعار',
                    'select_field'=>array(
                        'id'=>'button_mode',
                        'type'=>'Select',
                        'selected_shows'=>true,
                        'title'=>'تحديد نوع رابط الزرار',
                        'options'=>array(
                            ''=>'بدون',
                            'post' => 'مقالات',
                            'taxonomy'=>'اقسام',
                        ),
                    ),
                    'create_fields'=>true,
                    'choose_fields'=>array(
                        'post' => array(
                            'id'=>'post',
                            'title' => 'تحديد المقالات',
                            'fields'=> array(
                                array(
                                    'type'=>'Title',
                                    'id'=>'sefsefsef',
                                    'title'=>'الجزء الخاص عنوان القائمة',
                                ),
                                array(
                                    'type'=>'Text',
                                    'id'=>'main_menu_title_post',
                                    'title'=>'عنوان القائمة',
                                ),
                                array(
                                    'type'=>'Text',
                                    'id'=>'short_main_menu_title_post',
                                    'title'=>'عنوان مختصر للقائمة',
                                ),
                                array(
                                    'type'=>'Title',
                                    'id'=>'sefsefsef',
                                    'title'=>'الجزء الخاص لتحديد مقالات القائمة',
                                ),
                                array(
                                    'type'=>'Compo-Select-Field',
                                    'id'=>'post__mapItems_list',
                                    'title'=>'تحديد قائمة المقالات',
                                    'object__type'=>'posts',
                                    'object__name'=>'post',
                                    'show__perview__items'=>true,
                                    'per'=>5,
                                    'multiple'=>true,
                                ),
                                array(
                                    'type'=>'Title',
                                    'id'=>'sefsefsef',
                                    'title'=>'الجزء الخاص بأعدادات زر',
                                ),
                                array(
                                    'type'=>'Posts-Select',
                                    'id' => 'button_main_page_posts',
                                    'post_type_name'=>'page',
                                    'title' =>'تحديد صفحة',
                                    'disc' => 'قم بتحديد صفحة المقالات',
                                ),
                                array(
                                    'type'=>'Text',
                                    'id' => 'button_main_Text_posts',
                                    'title' =>'عنوان الزر',
                                ),
                                array(
                                    'type'=>'TextArea_Code',
                                    'id'=>'FirstButtonIcon_main_posts',
                                    'title'=>'ايقونة صفحة ',
                                ),
                            )
                        ),
                        'taxonomy'=>array(
                            'id'=>'taxonomy',
                            'title' => 'تحديد الاقسام',
                            'fields'=> array(
                                 array(
                                    'type'=>'Title',
                                    'id'=>'sefsefsef',
                                    'title'=>'الجزء الخاص عنوان القائمة',
                                ),
                                array(
                                    'type'=>'Text',
                                    'id'=>'main_menu_title',
                                    'title'=>'عنوان القائمة',
                                ),
                                array(
                                    'type'=>'Text',
                                    'id'=>'short_main_menu_title',
                                    'title'=>'عنوان مختصر للقائمة',
                                ),
                                array(
                                    'type'=>'Title',
                                    'id'=>'sefsefsef',
                                    'title'=>'الجزء الخاص لتحديد مقالات القائمة',
                                ),
                                array(
                                    'type'    => 'Taxonomy-CheckBox',
                                    'id'      => 'taxonomy_option',
                                    'title'   => 'اختار التصنيف',
                                    'taxonomy_name' => 'category',
                                    'taxonomy_type' => 'taxonomy',
                                    'pre'=>10
                                ),
                                array(
                                    'type'=>'Title',
                                    'id'=>'sefsefsef',
                                    'title'=>'الجزء الخاص بأعدادات زر',
                                ),
                                array(
                                    'type'=>'Posts-Select',
                                    'id' => 'button_main_page_tax',
                                    'post_type_name'=>'page',
                                    'title' =>'تحديد صفحة',
                                    'disc' => 'قم بتحديد صفحة المقالات',
                                ),
                                array(
                                    'type'=>'Text',
                                    'id' => 'button_main_Text_tax',
                                    'title' =>'عنوان الزر',
                                ),
                                array(
                                    'type'=>'TextArea_Code',
                                    'id'=>'FirstButtonIcon_main_tax',
                                    'title'=>'ايقونة صفحة ',
                                ),

                            )
                        )
                    )
                )
            );
        }

        public function MenuInputs($args=array(),$item_id='') {
            foreach ($args as $i => $field) {
                $MenuMeta = get_post_meta($item_id,$field['id'],true);
                $field['value'] = $MenuMeta;
                echo '<div class="-customfields-menuFields" style="clear: both;" for="'.$field['id'].'">';
                    $field['id'] .= '['.$item_id.']';
                    $this->YC__CFM->Fields__Part($field['type'],$field);
                echo '</div>';
            }
            #die;
        }   

    # APPEND FIELDS 
        public function FieldsSetup( $item_id, $item){

            $ObjectFields = $this->ObjectFields();
            $this->MenuInputs($ObjectFields,$item_id);  
        }

    # SAVE FIELDS
        public function UpdateMenuFields($menu_id, $menu_item_db_id){
            $ObjectFields = $this->ObjectFields();
            //
            foreach ($ObjectFields as $k => $field) {
                $meta_key = $field['id'];
                if ( isset( $_POST[$meta_key][$menu_item_db_id]  ) ) {
                    if( !is_array( $_POST[$meta_key][$menu_item_db_id] ) ){
                        $sanitized_data = sanitize_text_field( $_POST[$meta_key][$menu_item_db_id] );
                    }else{
                        $sanitized_data = $_POST[$meta_key][$menu_item_db_id];
                    }
                    update_post_meta( $menu_item_db_id, $meta_key, $sanitized_data );
                } else {
                    delete_post_meta( $menu_item_db_id, $meta_key );
                }
            }
        }

    # HOW TO SUBMENU.
        public function CheckSubMenu($classes){
            if( in_array('menu-item-has-children',$classes) ) return true;
            return false;
        }


    # INSERT FIELDS IN UI MENU
        public function ShowMenuUI( $title, $item ) {
            if( is_object( $item ) && isset( $item->ID ) ) {
                $Return = '';
                $CheckSubMenu = $this->CheckSubMenu($item->classes);
                # 
                $IconColor = get_post_meta($item->ID,'IconColor',true);
                if( empty( $IconColor ) ){
                    $IconColor = '';
                }
                #

                $_menu_MegaMenu_Action = get_post_meta($item->ID,'MegaMenu_data',true);
                if( !empty( $_menu_MegaMenu_Action ) && isset( $_menu_MegaMenu_Action['button_mode'] ) && isset( $_menu_MegaMenu_Action[ $_menu_MegaMenu_Action['button_mode'] ] ) ){

                    $menu_setup = array(
                        'ObjectValue'=>$_menu_MegaMenu_Action,
                        'Blade_ID'=>$_menu_MegaMenu_Action['button_mode'],
                    );
                    $CheckSubMenu = true;

                    $Return .= '<em data-mega-menu="'.base64_encode( json_encode( $menu_setup ) ).'"></em>';
                 
                }

                $Icon = get_post_meta($item->ID,'_menu_item_icon',true);
                if( !empty( $Icon ) ){
                    $Icon = '<i class="'.$Icon.'"></i>';
                    $Return .= $Icon;
                }

                $Return .= '<span style="'.$IconColor.'">'.$title.'</span>';
            }
            return $Return;
        }

    # HIDE MENU IN CUSTOM PAGE  
    public  function hide_menu_item_on_homepage( $items, $menu, $args ) {
        if ( !is_home() && !is_admin() ) {
            // تحديد العنصر الذي تريد إخفائه
            foreach ( $items as $key => $item ) {
                $_menu_item_faqs = get_post_meta($item->ID,'_menu_item_faqs',true);
                if( $_menu_item_faqs == 'on' ){
                    unset( $items[$key] );
                }
            }
        }
        return $items;
    }


    public function Setup(){
        add_action( 'wp_nav_menu_item_custom_fields',array($this, 'FieldsSetup'), 10, 2 );
        add_action( 'wp_update_nav_menu_item', array($this,'UpdateMenuFields'), 10, 2 );
        add_filter( 'nav_menu_item_title',array($this, 'ShowMenuUI'), 10, 2 );
        add_filter( 'wp_get_nav_menu_items', array($this,'hide_menu_item_on_homepage'), 10, 3 );
    }   

}
(new MenuCustomFields)->Setup();