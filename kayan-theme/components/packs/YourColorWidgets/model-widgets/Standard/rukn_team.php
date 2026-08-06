<?php
/**
 * RUKN v3 TEAM — rukn_team (ودجت جديدة)
 * قسم "خبراؤنا في خدمتكم" — كروت الفريق بالأفاتار المتدرج والشارات
 * كل عضو: صورة (أو حروف اسمه تلقائياً في دائرة متدرجة)، الاسم، الدور،
 * التخصص، وشارتين (خبرة + اعتماد)
 */
class rukn_team extends YC__WidgetsMachine{

	function __construct(){

		# WIDGET INFO
			$this->widget__name = 'rukn_team';
			$this->folder__name = basename(__DIR__);

		# CUSTOM $VARIABLES .
			$this->ThemeStatic = (new ThemeStatic);
	}

	public function widget__ui($vars){
		extract($vars);
		# ═══════════ المحتوى الافتراضي الجاهز (نفس محتوى التصميم) ═══════════
		if( isset( $use_default_content ) && !empty( $use_default_content ) ){
			foreach ( array('avatar_text','badge_1','badge_2','before_title','content','image','name','role','spec','team_members','title','tm_display_settings','tm_head_settings','tm_members_settings') as $rukn_dv ) { if( isset( ${$rukn_dv} ) ) unset( ${$rukn_dv} ); }
		}


		# ═══════════ رأس القسم ═══════════
		if( !isset( $before_title ) || empty( $before_title ) ) $before_title = 'فريقنا';
		if( !isset( $title ) || empty( $title ) ) $title = 'خبراؤنا في {%خدمتكم%}';
		$title = str_replace('{%','<span>',$title);
		$title = str_replace('%}','</span>',$title);
		if( !isset( $content ) || empty( $content ) ) $content = 'فريق معتمد من المتخصصين بخبرة عملية طويلة في السوق الإماراتي.';

		# ═══════════ أعضاء الفريق ═══════════
		if( !isset( $team_members ) || empty( $team_members ) || !is_array( $team_members ) ){
			$team_members = array(
				array( 'name'=>'أحمد المنصوري', 'role'=>'مدير العمليات',      'spec'=>'قيادة الفرق وضمان جودة التنفيذ في كل المشاريع.',   'badge_1'=>'15+ سنة', 'badge_2'=>'خبير معتمد',   'avatar_text'=>'', 'image'=>'' ),
				array( 'name'=>'سعيد العامري',   'role'=>'خبير كشف التسربات', 'spec'=>'كشف دقيق بالكاميرا الحرارية بدون أي تكسير.',        'badge_1'=>'12+ سنة', 'badge_2'=>'شهادة معتمدة', 'avatar_text'=>'', 'image'=>'' ),
				array( 'name'=>'خالد البلوشي',   'role'=>'مشرف العزل',         'spec'=>'عزل حراري ومائي بأحدث المواد العالمية.',            'badge_1'=>'10+ سنوات','badge_2'=>'خبير معتمد',   'avatar_text'=>'', 'image'=>'' ),
				array( 'name'=>'محمد الشحي',     'role'=>'مشرف الصيانة',       'spec'=>'صيانة شاملة للتكييف والسباكة والكهرباء.',           'badge_1'=>'11+ سنة', 'badge_2'=>'شهادة معتمدة', 'avatar_text'=>'', 'image'=>'' ),
				array( 'name'=>'عبدالله الكعبي', 'role'=>'خبير الخزانات',      'spec'=>'عزل وتنظيف الخزانات بمعايير صحية آمنة.',            'badge_1'=>'9+ سنوات', 'badge_2'=>'خبير معتمد',   'avatar_text'=>'', 'image'=>'' ),
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

			# شبكة الفريق
			echo '<div class="team-grid">';
				foreach ( $team_members as $member ) {
					if( !isset( $member['name'] ) || empty( $member['name'] ) ) continue;

					# الأفاتار: صورة أو حروف الاسم تلقائياً (أول حرف من أول كلمتين)
					$avatar_text = ( isset( $member['avatar_text'] ) && !empty( $member['avatar_text'] ) ) ? $member['avatar_text'] : '';
					if( empty( $avatar_text ) ){
						$name_words = preg_split('/\s+/', trim( $member['name'] ) );
						$avatar_text = mb_substr( $name_words[0], 0, 1, 'UTF-8' );
						if( isset( $name_words[1] ) ) $avatar_text .= '.'.mb_substr( $name_words[1], 0, 1, 'UTF-8' );
					}

					$image_url = '';
					if( isset( $member['image'] ) && !empty( $member['image'] ) ){
						$image_src = wp_get_attachment_image_src( $member['image'], 'medium' );
						if( isset( $image_src[0] ) ) $image_url = $image_src[0];
					}

					echo '<div class="tcard rv">';

						if( !empty( $image_url ) ){
							echo '<div class="tav tav-img" style="background-image:url(\''.$image_url.'\')"></div>';
						}else{
							echo '<div class="tav">'.$avatar_text.'</div>';
						}

						echo '<h3>'.$member['name'].'</h3>';
						if( isset( $member['role'] ) && !empty( $member['role'] ) ) echo '<div class="role">'.$member['role'].'</div>';
						if( isset( $member['spec'] ) && !empty( $member['spec'] ) ) echo '<p class="spec">'.$member['spec'].'</p>';

						if( ( isset( $member['badge_1'] ) && !empty( $member['badge_1'] ) ) || ( isset( $member['badge_2'] ) && !empty( $member['badge_2'] ) ) ){
							echo '<div class="tbadges">';
								if( isset( $member['badge_1'] ) && !empty( $member['badge_1'] ) )
									echo '<span class="tbadge"><i class="fas fa-award"></i> '.$member['badge_1'].'</span>';
								if( isset( $member['badge_2'] ) && !empty( $member['badge_2'] ) )
									echo '<span class="tbadge"><i class="fas fa-circle-check"></i> '.$member['badge_2'].'</span>';
							echo '</div>';
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
			'title'=>'RUKN v3 — فريق الخبراء (جديدة)',
			'description'=>'كروت الفريق بالأفاتار المتدرج والشارات',
			'screen-shoot'=>'test_URL',
			'fields'=> array(
				array(
					'type'=>'SwitchBox',
					'id'=>'use_default_content',
					'title'=>'تفعيل المحتوى الافتراضي الجاهز — يعرض نفس محتوى التصميم بالكامل ويتجاهل الحقول اليدوية',
				),


				array(
					'type'=>'Title',
					'id'=>'tm_head_settings',
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
					'id'=>'tm_members_settings',
					'title'=>'أعضاء الفريق (5 في التصميم)',
				),
				array(
					'type'=>'GroupsField',
					'id'=>'team_members',
					'title'=>'الأعضاء',
					'fields'=> array(
						array(
							'type'=>'File',
							'id'=>'image',
							'title'=>'صورة العضو — اتركها فارغة لعرض حروف الاسم',
						),
						array(
							'type'=>'Text',
							'id'=>'name',
							'title'=>'الاسم',
						),
						array(
							'type'=>'Text',
							'id'=>'avatar_text',
							'title'=>'حروف الأفاتار (مثال: أ.م) — تلقائي من الاسم لو فارغ',
						),
						array(
							'type'=>'Text',
							'id'=>'role',
							'title'=>'الدور الوظيفي (مثال: خبير كشف التسربات)',
						),
						array(
							'type'=>'Text',
							'id'=>'spec',
							'title'=>'وصف التخصص',
						),
						array(
							'type'=>'Text',
							'id'=>'badge_1',
							'title'=>'شارة الخبرة (مثال: 12+ سنة)',
						),
						array(
							'type'=>'Text',
							'id'=>'badge_2',
							'title'=>'شارة الاعتماد (مثال: خبير معتمد)',
						),
					)
				),

				# DIVER OPTIONS.
				array(
					'type'=>'Title',
					'id' => 'tm_display_settings',
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
					'disc'=>'التبديل بين الخلفية الفاتحة والبيضاء لعمل تناوب بين الأقسام',
				)

			),
		);

	}

	public function Setup(){
		add_action('yc__widgets__center',array($this,'widget__setup'));
	}

}
(new rukn_team)->Setup();
