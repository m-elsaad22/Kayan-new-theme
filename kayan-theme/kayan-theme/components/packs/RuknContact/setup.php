<?php
/**
 * ╔══════════════════════════════════════════════════════════════════╗
 *   RUKN CONTACT SYSTEM — نظام التحكم المتقدم بأزرار الاتصال والواتساب
 *   ثلاثة مستويات هرمية: المقال ← التصنيف ← الإعدادات العامة
 *
 *   - حقول في كل تصنيف: إظهار/إخفاء + أرقام مخصصة تُورث لكل مقالاته
 *   - ميتا بوكس في المقال: يتجاوز التصنيف والعام
 *   - إنفاذ شامل: الإخفاء يطبق على كل عناصر القالب (كروت/ودجات/شريط
 *     عائم/شورت كودز/أي CTA) عبر طبقة CSS + JS مركزية
 *   - عند إخفاء الاتصال مع بقاء الواتساب: يتحول لمربع محادثة مصغر
 *     ثابت أسفل الشاشة بزر إغلاق
 *   - استبدال الأرقام تلقائياً في كل روابط tel: و wa.me حسب الأولوية
 *   - رسالة واتساب تلقائية: مرحباً! (عنوان المقال) (اسم الموقع)
 * ╚══════════════════════════════════════════════════════════════════╝
 */

if( !defined('RUKN_CS_DEFAULT_WA') ) define('RUKN_CS_DEFAULT_WA', '971586634710');

class Rukn_Contact_System {

	public function __construct(){

		# ═══ المستوى الثاني: حقول التصنيفات ═══
		add_action( 'category_add_form_fields',  array( $this, 'category_fields_add' ) );
		add_action( 'category_edit_form_fields', array( $this, 'category_fields_edit' ), 10, 1 );
		add_action( 'created_category',          array( $this, 'category_fields_save' ) );
		add_action( 'edited_category',           array( $this, 'category_fields_save' ) );

		# ═══ المستوى الثالث: ميتا بوكس المقال ═══
		add_action( 'add_meta_boxes', array( $this, 'post_metabox_register' ) );
		add_action( 'save_post',      array( $this, 'post_metabox_save' ) );

		# ═══ طبقة الإنفاذ في الواجهة ═══
		add_filter( 'body_class', array( $this, 'front_body_classes' ) );
		add_action( 'wp_head',    array( $this, 'front_css' ), 99 );
		add_action( 'wp_footer',  array( $this, 'front_js' ), 99 );
	}

	# ════════════════════════════════════════════════════════════════
	# المحلّل المركزي — ترتيب الأولوية: المقال ← التصنيف ← العام
	# ════════════════════════════════════════════════════════════════
	public static function resolve( $post_id = null ){
		static $cache = array();

		if( is_null( $post_id ) && is_singular() ) $post_id = get_the_ID();
		$cache_key = ( $post_id ) ? $post_id : 0;
		if( isset( $cache[ $cache_key ] ) ) return $cache[ $cache_key ];

		# ═══ المستوى الأول: الإعدادات العامة ═══
		$call_show   = ( empty( get_option('rukn_hide_call_global') ) );
		$wa_show     = ( empty( get_option('rukn_hide_wa_global') ) );
		$call_number = get_option('phonenumber');
		$wa_number   = get_option('whatsapp_number');
		if( empty( $wa_number ) ) $wa_number = RUKN_CS_DEFAULT_WA;

		if( !empty( $post_id ) ){

			# ═══ المستوى الثاني: التصنيفات — أول تصنيف له إعداد صريح يفوز ═══
			$terms = get_the_terms( $post_id, 'category' );
			if( is_array( $terms ) ){
				foreach ( $terms as $term ) {
					$term_call_state  = get_term_meta( $term->term_id, 'rukn_call_state',  true );
					$term_wa_state    = get_term_meta( $term->term_id, 'rukn_wa_state',    true );
					$term_call_number = get_term_meta( $term->term_id, 'rukn_call_number', true );
					$term_wa_number   = get_term_meta( $term->term_id, 'rukn_wa_number',   true );

					if( $term_call_state === 'show' ) $call_show = true;
					if( $term_call_state === 'hide' ) $call_show = false;
					if( $term_wa_state   === 'show' ) $wa_show   = true;
					if( $term_wa_state   === 'hide' ) $wa_show   = false;
					if( !empty( $term_call_number ) ) $call_number = $term_call_number;
					if( !empty( $term_wa_number ) )   $wa_number   = $term_wa_number;

					# أول تصنيف فيه أي إعداد صريح بيحسمها
					if( $term_call_state !== '' || $term_wa_state !== '' || !empty( $term_call_number ) || !empty( $term_wa_number ) ) break;
				}
			}

			# ═══ المستوى الثالث: المقال — يتجاوز الكل ═══
			$post_call_state  = get_post_meta( $post_id, 'rukn_call_state',  true );
			$post_wa_state    = get_post_meta( $post_id, 'rukn_wa_state',    true );
			$post_call_number = get_post_meta( $post_id, 'rukn_call_number', true );
			$post_wa_number   = get_post_meta( $post_id, 'rukn_wa_number',   true );

			# توافق مع حقول القالب القديمة على مستوى المقال
			if( empty( $post_call_number ) ) $post_call_number = get_post_meta( $post_id, 'phone_number',    true );
			if( empty( $post_wa_number ) )   $post_wa_number   = get_post_meta( $post_id, 'whatsapp_number', true );

			if( $post_call_state === 'show' ) $call_show = true;
			if( $post_call_state === 'hide' ) $call_show = false;
			if( $post_wa_state   === 'show' ) $wa_show   = true;
			if( $post_wa_state   === 'hide' ) $wa_show   = false;
			if( !empty( $post_call_number ) ) $call_number = $post_call_number;
			if( !empty( $post_wa_number ) )   $wa_number   = $post_wa_number;
		}

		# ═══ الرسالة التلقائية: مرحباً! (عنوان المقال) (اسم الموقع) ═══
		$wa_message = 'مرحباً! ';
		if( !empty( $post_id ) ) $wa_message .= get_the_title( $post_id ).' ';
		$wa_message .= get_bloginfo('name');

		$result = array(
			'call_show'   => $call_show,
			'wa_show'     => $wa_show,
			'call_number' => trim( (string) $call_number ),
			'wa_number'   => preg_replace( '/[^0-9]/', '', (string) $wa_number ),
			'wa_message'  => $wa_message,
		);

		$cache[ $cache_key ] = $result;
		return $result;
	}

	# ════════════════════════════════════════════════════════════════
	# المستوى الثاني — حقول صفحة التصنيف
	# ════════════════════════════════════════════════════════════════
	private function state_options( $current = '' ){
		$options = array( ''=>'وراثة (من الإعدادات العامة)', 'show'=>'إظهار', 'hide'=>'إخفاء' );
		$output = '';
		foreach ( $options as $value => $label ) {
			$output .= '<option value="'.esc_attr( $value ).'"'.selected( $current, $value, false ).'>'.$label.'</option>';
		}
		return $output;
	}

	public function category_fields_add(){
		wp_nonce_field( 'rukn_cs_term', 'rukn_cs_term_nonce' );
		echo '<div class="form-field"><label>زر الاتصال لهذا التصنيف</label><select name="rukn_call_state">'.$this->state_options().'</select></div>';
		echo '<div class="form-field"><label>زر الواتساب لهذا التصنيف</label><select name="rukn_wa_state">'.$this->state_options().'</select></div>';
		echo '<div class="form-field"><label>رقم الاتصال المخصص</label><input type="text" name="rukn_call_number" dir="ltr" placeholder="+9715xxxxxxxx"><p>يُطبق تلقائياً على جميع مقالات هذا التصنيف. اتركه فارغاً لاستخدام الرقم العام.</p></div>';
		echo '<div class="form-field"><label>رقم الواتساب المخصص</label><input type="text" name="rukn_wa_number" dir="ltr" placeholder="9715xxxxxxxx"><p>يُطبق تلقائياً على جميع مقالات هذا التصنيف. اتركه فارغاً لاستخدام الرقم العام.</p></div>';
	}

	public function category_fields_edit( $term ){
		wp_nonce_field( 'rukn_cs_term', 'rukn_cs_term_nonce' );
		$call_state  = get_term_meta( $term->term_id, 'rukn_call_state',  true );
		$wa_state    = get_term_meta( $term->term_id, 'rukn_wa_state',    true );
		$call_number = get_term_meta( $term->term_id, 'rukn_call_number', true );
		$wa_number   = get_term_meta( $term->term_id, 'rukn_wa_number',   true );

		echo '<tr class="form-field"><th colspan="2"><h2 style="margin:10px 0 0">📞 نظام أزرار الاتصال والواتساب</h2><p style="font-weight:400">الأرقام والإعدادات هنا تُورث تلقائياً لجميع مقالات هذا التصنيف — ويمكن تجاوزها من داخل أي مقال.</p></th></tr>';
		echo '<tr class="form-field"><th><label>زر الاتصال لهذا التصنيف</label></th><td><select name="rukn_call_state">'.$this->state_options( $call_state ).'</select></td></tr>';
		echo '<tr class="form-field"><th><label>زر الواتساب لهذا التصنيف</label></th><td><select name="rukn_wa_state">'.$this->state_options( $wa_state ).'</select></td></tr>';
		echo '<tr class="form-field"><th><label>رقم الاتصال المخصص</label></th><td><input type="text" name="rukn_call_number" dir="ltr" value="'.esc_attr( $call_number ).'" placeholder="+9715xxxxxxxx"><p class="description">يُطبق على جميع مقالات التصنيف دفعة واحدة. فارغ = الرقم العام.</p></td></tr>';
		echo '<tr class="form-field"><th><label>رقم الواتساب المخصص</label></th><td><input type="text" name="rukn_wa_number" dir="ltr" value="'.esc_attr( $wa_number ).'" placeholder="9715xxxxxxxx"><p class="description">يُطبق على جميع مقالات التصنيف دفعة واحدة. فارغ = الرقم العام.</p></td></tr>';
	}

	public function category_fields_save( $term_id ){
		if( !isset( $_POST['rukn_cs_term_nonce'] ) || !wp_verify_nonce( $_POST['rukn_cs_term_nonce'], 'rukn_cs_term' ) ) return;
		if( !current_user_can( 'manage_categories' ) ) return;

		foreach ( array('rukn_call_state','rukn_wa_state','rukn_call_number','rukn_wa_number') as $field ) {
			if( isset( $_POST[ $field ] ) ){
				$value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
				if( $value === '' ){
					delete_term_meta( $term_id, $field );
				}else{
					update_term_meta( $term_id, $field, $value );
				}
			}
		}
	}

	# ════════════════════════════════════════════════════════════════
	# المستوى الثالث — ميتا بوكس المقال
	# ════════════════════════════════════════════════════════════════
	public function post_metabox_register(){
		$post_types = apply_filters( 'rukn_cs_post_types', array('post','works','price','faq','page') );
		foreach ( $post_types as $post_type ) {
			add_meta_box( 'rukn_contact_system', '📞 أزرار الاتصال والواتساب — تحكم خاص بهذا المحتوى', array( $this, 'post_metabox_render' ), $post_type, 'normal', 'default' );
		}
	}

	public function post_metabox_render( $post ){
		wp_nonce_field( 'rukn_cs_post', 'rukn_cs_post_nonce' );
		$call_state  = get_post_meta( $post->ID, 'rukn_call_state',  true );
		$wa_state    = get_post_meta( $post->ID, 'rukn_wa_state',    true );
		$call_number = get_post_meta( $post->ID, 'rukn_call_number', true );
		$wa_number   = get_post_meta( $post->ID, 'rukn_wa_number',   true );

		echo '<style>.rukn-cs-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:8px}.rukn-cs-grid label{display:block;font-weight:700;margin-bottom:5px}.rukn-cs-grid select,.rukn-cs-grid input{width:100%}.rukn-cs-note{background:#f0f6fc;border-inline-start:4px solid #2271b1;padding:8px 12px;margin-top:12px;border-radius:4px}</style>';
		echo '<div class="rukn-cs-grid">';
			echo '<div><label>زر الاتصال</label><select name="rukn_call_state">'.$this->state_options( $call_state ).'</select></div>';
			echo '<div><label>زر الواتساب</label><select name="rukn_wa_state">'.$this->state_options( $wa_state ).'</select></div>';
			echo '<div><label>رقم الاتصال الخاص بهذا المحتوى</label><input type="text" name="rukn_call_number" dir="ltr" value="'.esc_attr( $call_number ).'" placeholder="+9715xxxxxxxx"></div>';
			echo '<div><label>رقم الواتساب الخاص بهذا المحتوى</label><input type="text" name="rukn_wa_number" dir="ltr" value="'.esc_attr( $wa_number ).'" placeholder="9715xxxxxxxx"></div>';
		echo '</div>';
		echo '<div class="rukn-cs-note">ترتيب الأولوية: <b>رقم المقال</b> ← رقم التصنيف ← الرقم العام. اترك الحقول فارغة و"وراثة" لاستخدام إعدادات التصنيف أو الإعدادات العامة.</div>';
	}

	public function post_metabox_save( $post_id ){
		if( !isset( $_POST['rukn_cs_post_nonce'] ) || !wp_verify_nonce( $_POST['rukn_cs_post_nonce'], 'rukn_cs_post' ) ) return;
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if( !current_user_can( 'edit_post', $post_id ) ) return;

		foreach ( array('rukn_call_state','rukn_wa_state','rukn_call_number','rukn_wa_number') as $field ) {
			if( isset( $_POST[ $field ] ) ){
				$value = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
				if( $value === '' ){
					delete_post_meta( $post_id, $field );
				}else{
					update_post_meta( $post_id, $field, $value );
				}
			}
		}
	}

	# ════════════════════════════════════════════════════════════════
	# طبقة الإنفاذ الشاملة في الواجهة
	# ════════════════════════════════════════════════════════════════
	public function front_body_classes( $classes ){
		$config = self::resolve();
		if( !$config['call_show'] ) $classes[] = 'rukn-hide-call';
		if( !$config['wa_show'] )   $classes[] = 'rukn-hide-wa';
		return $classes;
	}

	public function front_css(){
		echo '<style id="rukn-cs-css">';

			# ═══ القاعدة الإجبارية: إخفاء الاتصال من كل العناصر ═══
			echo 'body.rukn-hide-call a[href^="tel:"],';
			echo 'body.rukn-hide-call .btn-call,';
			echo 'body.rukn-hide-call .-callbutton--post-card,';
			echo 'body.rukn-hide-call .fab-call,';
			echo 'body.rukn-hide-call [data-rukn-call]{display:none!important}';

			# ═══ إخفاء الواتساب من كل العناصر ═══
			echo 'body.rukn-hide-wa a[href*="wa.me"],';
			echo 'body.rukn-hide-wa a[href*="api.whatsapp"],';
			echo 'body.rukn-hide-wa a[href*="whatsapp://"],';
			echo 'body.rukn-hide-wa .btn-wa,';
			echo 'body.rukn-hide-wa .whatsapp--callbutton--post-card,';
			echo 'body.rukn-hide-wa [data-rukn-wa]{display:none!important}';


		echo '</style>';
	}

	public function front_js(){
		$config = self::resolve();
		echo '<script id="rukn-cs-js" type="text/javascript">';
			echo 'window.RuknCS='.json_encode( $config, JSON_UNESCAPED_UNICODE ).';';
			echo '(function(){';
				echo 'var cfg=window.RuknCS;';
				echo 'function waLink(){return "https://wa.me/"+cfg.wa_number+"?text="+encodeURIComponent(cfg.wa_message)}';

				# ═══ استبدال الأرقام تلقائياً في كل روابط الصفحة حسب الأولوية ═══
				echo 'function applyNumbers(root){';
					echo 'root=root||document;';
					echo 'if(cfg.call_number){root.querySelectorAll(\'a[href^="tel:"]\').forEach(function(a){a.href="tel:"+cfg.call_number})}';
					echo 'if(cfg.wa_number){root.querySelectorAll(\'a[href*="wa.me"],a[href*="api.whatsapp"]\').forEach(function(a){a.href=waLink()})}';
				echo '}';

				echo 'function init(){applyNumbers()}';
				echo 'if(document.readyState==="loading"){document.addEventListener("DOMContentLoaded",init)}else{init()}';
				# إعادة تطبيق الأرقام على المحتوى المضاف بالأجاكس (تحميل المزيد وغيره)
				echo 'new MutationObserver(function(muts){muts.forEach(function(m){m.addedNodes.forEach(function(n){if(n.nodeType===1)applyNumbers(n)})})}).observe(document.body,{childList:true,subtree:true});';
			echo '})();';
		echo '</script>';
	}

}
new Rukn_Contact_System;
