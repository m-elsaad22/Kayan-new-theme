<?php 
if ( ! function_exists( 'kayan_i18n_get_countries' ) ) {
	/**
	 * الدول المدعومة: مسار URL، علم، أسماء، ونصوص المناطق.
	 */
	function kayan_i18n_get_countries() {
		$countries = array(
			'ae' => array(
				'path'        => '',
				'flag'        => '🇦🇪',
				'label_ar'    => 'الإمارات',
				'label_en'    => 'UAE',
				'in_ar'       => 'الإمارات',
				'in_en'       => 'the UAE',
				'regions_ar'  => 'جميع الإمارات',
				'regions_en'  => 'All Emirates',
				'address_ar'  => 'دبي، الإمارات العربية المتحدة',
				'address_en'  => 'Dubai, United Arab Emirates',
			),
			'sa' => array(
				'path'        => '/sa',
				'flag'        => '🇸🇦',
				'label_ar'    => 'السعودية',
				'label_en'    => 'Saudi Arabia',
				'in_ar'       => 'السعودية',
				'in_en'       => 'Saudi Arabia',
				'regions_ar'  => 'جميع مناطق المملكة',
				'regions_en'  => 'All regions',
				'address_ar'  => 'الرياض، المملكة العربية السعودية',
				'address_en'  => 'Riyadh, Saudi Arabia',
			),
			'qa' => array(
				'path'        => '/qa',
				'flag'        => '🇶🇦',
				'label_ar'    => 'قطر',
				'label_en'    => 'Qatar',
				'in_ar'       => 'قطر',
				'in_en'       => 'Qatar',
				'regions_ar'  => 'جميع مناطق قطر',
				'regions_en'  => 'All of Qatar',
				'address_ar'  => 'الدوحة، قطر',
				'address_en'  => 'Doha, Qatar',
			),
			'kw' => array(
				'path'        => '/kw',
				'flag'        => '🇰🇼',
				'label_ar'    => 'الكويت',
				'label_en'    => 'Kuwait',
				'in_ar'       => 'الكويت',
				'in_en'       => 'Kuwait',
				'regions_ar'  => 'جميع محافظات الكويت',
				'regions_en'  => 'All governorates',
				'address_ar'  => 'الكويت، دولة الكويت',
				'address_en'  => 'Kuwait City, Kuwait',
			),
			'om' => array(
				'path'        => '/om',
				'flag'        => '🇴🇲',
				'label_ar'    => 'عمان',
				'label_en'    => 'Oman',
				'in_ar'       => 'عمان',
				'in_en'       => 'Oman',
				'regions_ar'  => 'جميع محافظات عُمان',
				'regions_en'  => 'All governorates',
				'address_ar'  => 'مسقط، سلطنة عُمان',
				'address_en'  => 'Muscat, Oman',
			),
			'bh' => array(
				'path'        => '/bh',
				'flag'        => '🇧🇭',
				'label_ar'    => 'البحرين',
				'label_en'    => 'Bahrain',
				'in_ar'       => 'البحرين',
				'in_en'       => 'Bahrain',
				'regions_ar'  => 'جميع محافظات البحرين',
				'regions_en'  => 'All governorates',
				'address_ar'  => 'المنامة، مملكة البحرين',
				'address_en'  => 'Manama, Bahrain',
			),
			'eg' => array(
				'path'        => '/eg',
				'flag'        => '🇪🇬',
				'label_ar'    => 'مصر',
				'label_en'    => 'Egypt',
				'in_ar'       => 'مصر',
				'in_en'       => 'Egypt',
				'regions_ar'  => 'جميع المحافظات',
				'regions_en'  => 'All governorates',
				'address_ar'  => 'القاهرة، جمهورية مصر العربية',
				'address_en'  => 'Cairo, Egypt',
			),
		);

		return apply_filters( 'kayan_i18n_countries', $countries );
	}
}
