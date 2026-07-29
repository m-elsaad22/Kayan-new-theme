<?php 
$currency__ids = get__currency__items();

$metaboxes = array(
    'title'    => 'إعدادات العملات ',
    'en_title'  => 'currency options',
    'icon'    => '<i class="fa-solid fa-coins"></i>',
    'number'=>13,
    'fields'  => array(

        array(
            'title'  => 'العملة ',
            'en_title'=> 'select currency',
            'type'  => 'Select',
            'id'    => 'currency',
            'options'=>$currency__ids
        ),

        array(
            'title'=>'تحديد خيارات العملة المراد عرضها ',
            'id'=> 'currency__shows',
            'type'=>'GroupsField',
            'fields'=>array(
                array(
                    'id'=> 'title',
                    'type'=>'Text',
                    'title'=>'الاسم ',
                ),
                array(
                    'id'=> 'short',
                    'type'=>'Text',
                    'title'=>'اختصار ',
                ),

                array(
                    'id'=> 'item__id',
                    'type'=>'Select',
                    'title'=>'تحديد العملة ',
                    'options'=> $currency__ids,
                ),

            ),
        ),
    )
);