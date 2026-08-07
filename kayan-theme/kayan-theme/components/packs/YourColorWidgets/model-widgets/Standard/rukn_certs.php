<?php
/**
 * RUKN v3 CERTIFICATIONS — rukn_certs (ودجت جديدة)
 * قسم "التراخيص والشهادات والاعتمادات" — كروت الموثوقية
 * كل كارت: أيقونة، عنوان، وصف، شارة توثيق خضراء، ومنطقة المستند
 * (لو رفعت ملف/رابط للمستند بتتحول لرابط قابل للضغط)
 */
class rukn_certs extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_certs';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('badge','before_title','cert_title','certs_items','content','crt_display_settings','crt_head_settings','crt_items_settings','desc','doc_file','doc_label','doc_url','icon','title') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'الموثوقية';
		if( !isset( $title ) || empty( $title ) ) $title = 'التراخيص والشهادات {%والاعتمادات%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'نعمل بشفافية كاملة وفق التراخيص والمعايير المعتمدة في دولة الإمارات.';

		# ═══════════ الشهادات ═══════════
		if( !isset( $certs_items ) || empty( $certs_items ) || !is_array( $certs_items ) ){
			$certs_items = array(
				array( 'icon'=>'<i class="fas fa-file-signature"></i>',   'cert_title'=>'رخصة تجارية',   'desc'=>'رخصة سارية لمزاولة نشاط الخدمات المنزلية.', 'badge'=>'موثّق',  'doc_label'=>'مستند الرخصة',  'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-building-columns"></i>', 'cert_title'=>'سجل تجاري',     'desc'=>'سجل تجاري معتمد لدى الجهات الرسمية.',        'badge'=>'موثّق',  'doc_label'=>'مستند السجل',   'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-receipt"></i>',          'cert_title'=>'تسجيل ضريبي',   'desc'=>'رقم تسجيل ضريبي (VAT) رسمي وفواتير نظامية.', 'badge'=>'موثّق',  'doc_label'=>'شهادة الضريبة', 'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-medal"></i>',            'cert_title'=>'شهادة جودة',    'desc'=>'التزام بمعايير الجودة في جميع مراحل العمل.',  'badge'=>'معتمد',  'doc_label'=>'شهادة الجودة',  'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-helmet-safety"></i>',    'cert_title'=>'شهادة السلامة', 'desc'=>'اعتماد إجراءات السلامة المهنية للفرق.',        'badge'=>'معتمد',  'doc_label'=>'شهادة السلامة', 'doc_file'=>'', 'doc_url'=>'' ),
				array( 'icon'=>'<i class="fas fa-shield-halved"></i>',    'cert_title'=>'برنامج الضمان', 'desc'=>'ضمان مكتوب وموثق يصل إلى 10 سنوات.',          'badge'=>'مضمون',  'doc_label'=>'وثيقة الضمان',  'doc_file'=>'', 'doc_url'=>'' ),
			);
		}

		# ════════════════════════════════════════════════════════
		# OUTPUT — نفس بنية التصميم الجديد
		# ════════════════════════════════════════════════════════
		echo '<div class="wrap">';

			# رأس القسم
			echo '<div class="shead rv">';
				if( !empty( $before_title ) ) echo '<span class="tag">'.$before_title.'</span>';
				echo '<h2>'.$title.'</h2>';
				if( !empty( $content ) ) echo '<p>'.$content.'</p>';
			echo '</div>';

			echo '<div class="cert-grid">';

				foreach ( $certs_items as $cert ) {
					if( !isset( $cert['cert_title'] ) || empty( $cert['cert_title'] ) ) continue;

					$cert_icon = ( isset( $cert['icon'] ) && !empty( $cert['icon'] ) ) ? $cert['icon'] : '<i class="fas fa-certificate"></i>';

					# رابط المستند: ملف مرفوع أو رابط خارجي
					$doc_url = '';
					if( isset( $cert['doc_file'] ) && !empty( $cert['doc_file'] ) ){
						$doc_url = wp_get_attachment_url( $cert['doc_file'] );
					}
					if( empty( $doc_url ) && isset( $cert['doc_url'] ) && !empty( $cert['doc_url'] ) ){
						$doc_url = $cert['doc_url'];
					}

					echo '<div class="cert rv">';
						echo '<div class="cert-ic">'.$cert_icon.'</div>';
						echo '<h3>'.$cert['cert_title'].'</h3>';
						if( isset( $cert['desc'] ) && !empty( $cert['desc'] ) ) echo '<p>'.$cert['desc'].'</p>';
						if( isset( $cert['badge'] ) && !empty( $cert['badge'] ) ){
							echo '<span class="vbadge"><i class="fas fa-circle-check"></i> '.$cert['badge'].'</span>';
						}
						if( isset( $cert['doc_label'] ) && !empty( $cert['doc_label'] ) ){
							if( !empty( $doc_url ) ){
								echo '<a href="'.$doc_url.'" target="_blank" rel="noopener" class="doc"><i class="fas fa-file-pdf"></i> '.$cert['doc_label'].'</a>';
							}else{
								echo '<div class="doc"><i class="fas fa-file-pdf"></i> '.$cert['doc_label'].'</div>';
							}
						}
					echo '</div>';
				}

			echo '</div>';

		echo '</div>';

	}


	public function widget__setup(){
		global $yc__widgets__center;

		$yc__widgets__center[$this->folder__name]['Packs'][ $this->widget__name ] = array(
			'id'=>$this->widget__name,
			'title'=>'RUKN v3 — الشهادات والتراخيص (جديدة)',
			'description'=>'كروت الموثوقية والاعتمادات',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'crt_head_settings',
					'title'=>'رأس القسم',
				),
				array(
					'type'=>'Text',
					'id'=>'before_title',
					'title'=>'الشارة فوق العنوان (Tag)',
				),
				array(
					'type'=>'Text',
					'id'=>'title',
					'title'=>'عنوان الشريحة',
					'disc'=> "قَم بتمييز كلمات محددة في العنوان بتدرج لوني عن طريق إضافة ' {% ' قبل الكلمة و ' %} ' بعدها",
				),
				array(
					'type'=>'Editor',
					'id' => 'content',
					'title' =>'وصف الشريحة',
				),

				array(
					'type'=>'Title',
					'id'=>'crt_items_settings',
					'title'=>'الشهادات (6 في التصميم) — ارفع ملف PDF أو ضع رابطاً ليتحول المستند لرابط قابل للضغط',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'certs_items',
					'title'=>'الشهادات',
					'fields'=> array(
						array(
							'type'=>'TextArea_Code',
							'id'=>'icon',
							'title'=>'الأيقونة (HTML)',
						),
						array(
							'type'=>'Text',
							'id'=>'cert_title',
							'title'=>'عنوان الشهادة (مثال: رخصة تجارية)',
						),
						array(
							'type'=>'Text',
							'id'=>'desc',
							'title'=>'الوصف',
						),
						array(
							'type'=>'Text',
							'id'=>'badge',
							'title'=>'نص الشارة الخضراء (موثّق / معتمد / مضمون)',
						),
						array(
							'type'=>'Text',
							'id'=>'doc_label',
							'title'=>'اسم المستند (مثال: مستند الرخصة)',
						),
						array(
							'type'=>'File',
							'id'=>'doc_file',
							'title'=>'ملف المستند (PDF)',
						),
						array(
							'type'=>'Text',
							'id'=>'doc_url',
							'title'=>'أو رابط المستند مباشرة',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'crt_display_settings',
					'title' =>'إعدادات الظهور',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'mobile_hide_section__switch',
					'title' =>'هل تريد إخفاء هذه الشريحة مؤقتاً في الموبيل',
				),
				array(
					'type'=>'SwitchBox',
					'id' => 'show_top_separator',
					'title' =>'خلفية بيضاء للشريحة',
					'disc'=>'القسم في التصميم الأصلي بخلفية بيضاء — فعّل السويتش ده للمطابقة',
				)

			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new rukn_certs)->Setup();
