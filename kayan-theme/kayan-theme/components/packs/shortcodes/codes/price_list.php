<?php 
class price_list_short_code{
	function __construct($argument=array() ){
		
	}

	/**
	 * استخراج رقم السعر من النص (مثال: "250 درهم" أو "1,200 AED")
	 */
	private function extract_amount( $raw ) {
		if ( class_exists( 'Kayan_Price_Pay' ) ) {
			return Kayan_Price_Pay::extract_amount( $raw );
		}
		$raw = (string) $raw;
		if ( preg_match( '/(\d+(?:[.,]\d+)?)/u', $raw, $m ) ) {
			return str_replace( ',', '', $m[1] );
		}
		return preg_replace( '/[^\d.]/', '', $raw );
	}

	public function ShortCodeAppend(){
		global $post;
		
		$hide_price_list__section = get_post_meta( $post->ID,'hide_price_list__section',true );
		if( !empty( $hide_price_list__section ) ) return ;

		$post__price_list__data = get_post_meta( $post->ID,'post__price_list__data',true );
		$post__price_list__data = ( ( is_array( $post__price_list__data ) ) ) ? $post__price_list__data : array();
		if( empty( $post__price_list__data ) ) return ;

		$service_title = get_the_title( $post );
		$currency = get_option( 'currency' );
		if ( empty( $currency ) ) $currency = 'AED';
		$uid = 'kpp_' . $post->ID;

		# SHORTCODE OUTPUT — حجز تفاعلي + دفع خارجي
     		ob_start();
				echo '<div id="kayan-price-booking" class="yc-shortcode--box yc-shortcode--price_list kayan-price-booking" data-service="'.esc_attr( $service_title ).'">';

					echo ( ( isset( $post__price_list__data['price_list__title'] ) && !empty( $post__price_list__data['price_list__title'] ) ) ) ? '<h2 class="--short--code--title kpp-heading">'.$post__price_list__data['price_list__title'].'</h2>' : '<h2 class="--short--code--title kpp-heading">اختر باقتك واحجز الآن</h2>';
					
					echo ( ( isset( $post__price_list__data['price_list__content'] ) && !empty( $post__price_list__data['price_list__content'] ) ) ) ? '<p class="--short--code--content kpp-lead">'.$post__price_list__data['price_list__content'].'</p>' : '<p class="--short--code--content kpp-lead">اختر الباقة المناسبة ثم أدخل بياناتك لإتمام الدفع.</p>';

					if( isset( $post__price_list__data['price_list__items'] ) && !empty( $post__price_list__data['price_list__items'] ) ){
						$post__price_list__data['price_list__items'] = ( ( is_array( $post__price_list__data['price_list__items'] ) ) ) ? $post__price_list__data['price_list__items'] : array();

						echo '<div class="kpp-packages" role="listbox" aria-label="باقات الأسعار">';
						$i = 0;
						foreach ( $post__price_list__data['price_list__items'] as $tr ) {
							$title = isset( $tr['title'] ) ? $tr['title'] : '';
							$value = isset( $tr['value'] ) ? $tr['value'] : '';
							if ( '' === trim( (string) $title ) ) continue;
							$amount = $this->extract_amount( $value );
							$active = ( 0 === $i ) ? ' is-active' : '';
							echo '<button type="button" class="kpp-package'.$active.'" role="option" aria-pressed="'.( 0 === $i ? 'true' : 'false' ).'" data-package="'.esc_attr( $title ).'" data-amount="'.esc_attr( $amount ).'" data-amount-raw="'.esc_attr( $value ).'" data-currency="'.esc_attr( $currency ).'">';
								echo '<span class="kpp-package-name">'.esc_html( $title ).'</span>';
								echo '<span class="kpp-package-price">'.esc_html( $value );
									if ( $amount && false === strpos( (string) $value, (string) $currency ) ) {
										echo ' <small>'.esc_html( $currency ).'</small>';
									}
								echo '</span>';
							echo '</button>';
							$i++;
						}
						echo '</div>';
					}

					# نموذج الحجز + زر ادفع الآن
					if ( class_exists( 'Kayan_Price_Pay' ) ) {
						Kayan_Price_Pay::render_form( array(
							'service' => $service_title,
							'uid'     => $uid,
						) );
					}

				echo '</div>';

      		$content = ob_get_clean();

	    return $content;
	}

	public function ShortCode__insert_field(){
		global $YC__CFM__global_setup_fields;

		$YC__CFM__global_setup_fields['Post_Types']['yourcolor__price_list_short_code'] = array(
			'title'=>  'شريحة جدول الاسعار',
			'ObjectType'=>'post',
			'id'=>'yourcolor__price_list_short_code',
			'ObjectType'=>array('post','page'),
            'context' => 'normal',
            'priority' => 'high',
            'MetaBox__Action'=>'fields_metabox',
			'fields'=>array(

                array(
                    'id'=> 'title_price_list_short_code',
                    'type'=>'Title',
                    'title'=>'حجز تفاعلي + دفع — ضع الكود داخل المحتوى',
                    'disc'=>'انسخ هذا الكود   <code data-copy-action="[post_prices]"><input type="hidden" value="[post_prices]">[post_prices]</code> — كل صف في الجدول يتحول إلى باقة قابلة للاختيار (مثال: الأساسية / المتقدمة / البريميوم).'
                ),

                array(
                    'id'=> 'hide_price_list__section',
                    'type'=>'SwitchBox',
                    'title'=>'إخفاء شريحة الأسعار/الحجز من المحتوى',
                ),

				array(
					'title'  =>'إعدادات الباقات والحجز',
					'type'  => 'SingleGroup',
					'id'    => 'post__price_list__data',
					'is__open'=>true,
					'fields'=> array(
		                array(
		                    'id'=> 'price_list__title',
		                    'type'=>'Text',
		                    'title'=>'عنوان قسم الحجز',
		                ),
		                array(
		                    'id'=> 'price_list__content',
		                    'type'=>'TextArea',
		                    'title'=> 'وصف قسم الحجز',
		                ),

		                array(
		                    'id'=> 'price_exptes__title',
		                    'type'=>'Title',
		                    'title'=>'أسماء أعمدة الجدول (للمرجع في الأدمن)',
		                ),

		                array(
		                    'id'=> 'price_list__table_title1',
		                    'type'=>'Text',
		                    'title'=>'عنوان اسم الباقة (مثال: الباقة)',
		                ),
		                array(
		                    'id'=> 'price_list__table_title2',
		                    'type'=>'Text',
		                    'title'=> 'عنوان السعر (مثال: القيمة)',
		                ),

				        array(
				            'title'=>'باقات الأسعار (كل صف = باقة قابلة للاختيار)',
				            'id'=> 'price_list__items',
				            'type'=>'GroupsField',
				            'fields'=>array(
				                array(
				                    'id'=> 'title',
				                    'type'=>'Text',
				                    'title'=>'اسم الباقة (الأساسية / المتقدمة / البريميوم)',
				                ),
				                array(
				                    'id'=> 'value',
				                    'type'=>'Text',
				                    'title'=> 'السعر (مثال: 299 أو 299 درهم)',
				                ),		                
				            ),
				        ),
					)
				)
			)
		);
	}

	public function Setup(){
		add_action( 'YC__CFM__global_setup_fields',array($this,'ShortCode__insert_field'),1 );
		add_action('init',function(){			
			add_shortcode( 'post_prices', array($this,'ShortCodeAppend'));
		});		
	}
}
(new price_list_short_code)->Setup();
