<?php 
$Styles = array();
$Styles['contact__form'] = 'YourColor__Widgets/contact__form.css';
$UniqId = uniqid();
#
$post_content = $post->post_content;
$post_content = str_replace('<br/>', PHP_EOL, $post_content);
$post_content = str_replace('&nbsp;', ' ', $post_content);
$post_content = strip_tags($post_content);
if( strlen($post_content) > 350 ) {
	$post_content = mb_substr($post_content, 0, 350, 'utf-8').'... <a href="javascript:void(0);" data-button="readmore-objects" data-object-type="post_type" data-object-name="'.$post->post_type.'" data-object-id="'.$post->ID.'" class="readmore--category-item">'.__('قراءة المزيد','yourcolor').'</a>';
}

# CONTACT SOURCES (ThemeOptions)
$phonenumber      = get_option('phonenumber');
$whatsapp_number  = get_option('whatsapp_number');
$company__adress  = get_option('company__adress');
$booking__note    = get_option('booking__note');
$site_name        = get_bloginfo('name');

# FORM FIELDS (validated by setup.js via data-fields-arguments, handled by AjaxCenter/contact__form)
$FieldSetup = array(
	array('title'=>__('الاسم بالكامل','yourcolor'),'id'=>'user__name','type'=>'Text','Require'=>'on'),
	array('title'=>__('البريد الالكتروني','yourcolor'),'id'=>'user_mail','type'=>'Email','Require'=>'on'),
	array('title'=>__('رقم الهاتف','yourcolor'),'id'=>'phone__number','type'=>'Number','Require'=>'on'),
	array('title'=>__('تفاصيل الحجز','yourcolor'),'id'=>'description','type'=>'TextArea','Require'=>'on'),
);
$FieldsArguments = base64_encode( json_encode( $FieldSetup ) );

$this->Part('header',array('Styles'=>$Styles));

echo '<div class="-primary-body">';

	echo '<div class="--primary--intro--pages">';
		echo '<div class="container">';
			echo '<div class="container-pages-head">';
				echo '<div class="--container--category--info">';
					echo '<h1>'.$post->post_title.'</h1>';
					echo '<div class="--archive--be-content">'.$post_content.'</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="-Yc-breadcrumb-">';
		echo '<div class="container">';
			echo '<div class="YC-BreadCrumb -BreadCrumb-PT-'.$post->post_type.'">';
				Breadcrumb();
			echo '</div>';
		echo '</div>';
	echo '</div>';

	echo '<div class="-page--container-sidebars">';
		echo '<div class="-YC-Widgets-Inner-Row">';
			echo '<div class="container">';

				echo '<div class="kayan-booking-wrap">';

					# FORM
					echo '<div class="kayan-booking-form">';
						echo '<form method="POST" action="contact__form" data-form-ajax="true" data-for-action="1" data-fields-arguments="'.$FieldsArguments.'">';

							foreach ( $FieldSetup as $field ) {
								echo '<div class="-fix-inputs-area" data-field-id="'.$field['id'].'" style="margin-bottom:18px;">';
									echo '<label style="display:block;font-weight:700;margin-bottom:8px;">'.$field['title'].'</label>';
									if( $field['type'] == 'TextArea' ) {
										echo '<textarea name="'.$field['id'].'"></textarea>';
									}else if( $field['type'] == 'Email' ) {
										echo '<input type="email" name="'.$field['id'].'" />';
									}else if( $field['type'] == 'Number' ) {
										echo '<input type="tel" name="'.$field['id'].'" />';
									}else{
										echo '<input type="text" name="'.$field['id'].'" />';
									}
								echo '</div>';
							}

							echo '<button type="submit" class="kayan-btn"><i class="fa-solid fa-calendar-check"></i> '.__('تأكيد الحجز','yourcolor').'</button>';

						echo '</form>';
					echo '</div>';

					# SIDE INFO (from ThemeOptions)
					echo '<aside class="kayan-booking-side">';
						echo '<h3>'.$site_name.'</h3>';
						if( !empty( $booking__note ) ) {
							echo '<p>'.$booking__note.'</p>';
						}
						if( !empty( $phonenumber ) ) {
							echo '<a class="kayan-side-item" href="tel:'.esc_attr($phonenumber).'" rel="nofollow"><i class="fa-solid fa-phone"></i><span>'.$phonenumber.'</span></a>';
						}
						if( !empty( $whatsapp_number ) ) {
							echo '<a class="kayan-side-item" href="https://wa.me/'.esc_attr($whatsapp_number).'" target="_blank" rel="nofollow noopener"><i class="fa-brands fa-whatsapp"></i><span>'.$whatsapp_number.'</span></a>';
						}
						if( !empty( $company__adress ) ) {
							echo '<div class="kayan-side-item"><i class="fa-solid fa-location-dot"></i><span>'.$company__adress.'</span></div>';
						}
					echo '</aside>';

				echo '</div>';

			echo '</div>';
		echo '</div>';
	echo '</div>';

echo '</div>';
$this->Part('footer',array('Styles'=>$Styles));
