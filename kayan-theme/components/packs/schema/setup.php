<?php 
/**
 * 
 */
class YourColor__Schema{
	
	function __construct($argument=array()){
		
	}

	# SINGLE EDITS .
		public function Single(){
			global $post;

            echo '<script type="application/ld+json">';

            	$Permalink = get_the_permalink( $post->ID );
			    $publish_date = get_the_date('Y-m-d');
			    $modified_date = get_the_modified_date('Y-m-d');
			    $sitename__schema = get_option('sitename__schema');
			    $logo__schema = get_option('logo__schema');
			    $post_author = get_userdata($post->post_author);
			    # حماية: مقال بدون مؤلف صالح (مستخدم محذوف) — كائن بديل يمنع تحذير PHP (v1.2.1)
			    if ( ! $post_author ) {
			    	$post_author = (object) array( 'display_name' => get_bloginfo('name'), 'ID' => 0 );
			    }
			    $Author__url = get_author_posts_url($post_author->ID);
			    $thumbnail_url = get_the_post_thumbnail_url( $post->ID );


            	# ImageObject
            		$hide_schema_ImageObject = get_option('hide_schema_ImageObject');
            		if( empty( $hide_schema_ImageObject ) && !empty( $sitename__schema ) && !empty( $logo__schema )  ) {
						$YourColor_ImageObject = get_post_meta( $post->ID,'YourColor_ImageObject',true);
						$YourColor_ImageObject = ( is_array( $YourColor_ImageObject ) ) ? $YourColor_ImageObject : array();
						#
						if( !isset( $YourColor_ImageObject['hide_schema_ImageObject'] ) || ( isset( $YourColor_ImageObject['hide_schema_ImageObject'] ) && empty( $YourColor_ImageObject['hide_schema_ImageObject'] ) ) ) {

							$defualt_ImageObject = get_option('YourColor_ImageObject');
							$defualt_ImageObject = ( is_array( $defualt_ImageObject ) ) ? $defualt_ImageObject : array();
							if( !isset( $defualt_ImageObject['description'] ) || ( isset( $defualt_ImageObject['description'] ) && empty( $defualt_ImageObject['description'] ) ) ) $defualt_ImageObject['description'] = wp_trim_words( $post->post_content,20);
							if( !isset( $defualt_ImageObject['contentLocation'] ) || ( isset( $defualt_ImageObject['contentLocation'] ) && empty( $defualt_ImageObject['contentLocation'] ) ) ) $defualt_ImageObject['contentLocation'] = '';
							#
							if( !isset( $YourColor_ImageObject['description'] ) || ( isset( $YourColor_ImageObject['description'] ) && empty( $YourColor_ImageObject['description'] ) ) ) $YourColor_ImageObject['description'] = $defualt_ImageObject['description'];
							if( !isset( $YourColor_ImageObject['contentLocation'] ) || ( isset( $YourColor_ImageObject['contentLocation'] ) && empty( $YourColor_ImageObject['contentLocation'] ) ) ) $YourColor_ImageObject['contentLocation'] = $defualt_ImageObject['contentLocation'];

							$thumbnail_url = get_the_post_thumbnail_url( $post->ID );
							if( !empty( $thumbnail_url ) ){
					            echo '{';
					                echo '"@context": "http://schema.org",';
					                echo '"@type": "ImageObject",';
					                echo '"url": "'.$Permalink.'",';
					                echo '"datePublished": "'.$publish_date.'",';
					                echo '"dateModified": "'.$modified_date.'",';
					                echo '"description": "'.$YourColor_ImageObject['description'].'",';
					                echo '"uploadDate": "'.date('Y-m-d H:i:s', strtotime($publish_date) ).'",';
					                echo '"contentUrl": "'.$thumbnail_url.'",';
					                echo '"contentLocation": "'.$YourColor_ImageObject['contentLocation'].'",';
					                echo '"publisher": {';
					                    echo '"@type": "Organization",';
					                    echo '"name": "'.$sitename__schema.'",';
					                    echo '"logo": {';
						                        echo '"@type": "ImageObject",';
						                        echo '"url": "'.$logo__schema.'"';
					                      	echo '}';
					                  	echo '},';
					                echo '"author": {';
					                    echo '"@type": "Person",';
					                    echo '"name": "'.$post_author->display_name.'"';
					                echo '}';
					            echo '}';
							}
						}
            		}

	     	 	echo '</script>';   
	     	 	echo '<script type="application/ld+json">';
            	# YourColor_Service .	
	          		$hide_schema_Service = get_option('hide_schema_Service');
	        		if( empty( $hide_schema_Service ) ) {
						$YourColor_Service = get_post_meta( $post->ID,'YourColor_Service',true);
						$YourColor_Service = ( is_array( $YourColor_Service ) ) ? $YourColor_Service : array();
						#
						if( !isset( $YourColor_Service['hide_schema_Service'] ) || ( isset( $YourColor_Service['hide_schema_Service'] ) && empty( $YourColor_Service['hide_schema_Service'] ) ) ) {

							$defualt_Service = get_option('YourColor_Service');
							$defualt_Service = ( is_array( $defualt_Service ) ) ? $defualt_Service : array();
							if( !isset( $defualt_Service['priceRange'] ) || ( isset( $defualt_Service['priceRange'] ) && empty( $defualt_Service['priceRange'] ) ) ) $defualt_Service['priceRange'] = '$';
							if( !isset( $defualt_Service['description'] ) || ( isset( $defualt_Service['description'] ) && empty( $defualt_Service['description'] ) ) ) $defualt_Service['description'] = wp_trim_words( $post->post_content,20);
							
							if( !isset( $defualt_Service['addressLocality'] ) || ( isset( $defualt_Service['addressLocality'] ) && empty( $defualt_Service['addressLocality'] ) ) ) $defualt_Service['addressLocality'] = '';
							if( !isset( $defualt_Service['postalCode'] ) || ( isset( $defualt_Service['postalCode'] ) && empty( $defualt_Service['postalCode'] ) ) ) $defualt_Service['postalCode'] = '';
							if( !isset( $defualt_Service['telephone'] ) || ( isset( $defualt_Service['telephone'] ) && empty( $defualt_Service['telephone'] ) ) ) $defualt_Service['telephone'] = '';
							if( !isset( $defualt_Service['addressCountry'] ) || ( isset( $defualt_Service['addressCountry'] ) && empty( $defualt_Service['addressCountry'] ) ) ) $defualt_Service['addressCountry'] = '';
							if( !isset( $defualt_Service['streetAddress'] ) || ( isset( $defualt_Service['streetAddress'] ) && empty( $defualt_Service['streetAddress'] ) ) ) $defualt_Service['streetAddress'] = '';
							if( !isset( $defualt_Service['addressRegion'] ) || ( isset( $defualt_Service['addressRegion'] ) && empty( $defualt_Service['addressRegion'] ) ) ) $defualt_Service['addressRegion'] = '';
							if( !isset( $defualt_Service['areaServed'] ) || ( isset( $defualt_Service['areaServed'] ) && empty( $defualt_Service['areaServed'] ) ) ) $defualt_Service['areaServed'] = '';
							if( !isset( $defualt_Service['OfferCatalog'] ) || ( isset( $defualt_Service['OfferCatalog'] ) && empty( $defualt_Service['OfferCatalog'] ) ) ) $defualt_Service['OfferCatalog'] = '';
							if( !isset( $defualt_Service['identifier'] ) || ( isset( $defualt_Service['identifier'] ) && empty( $defualt_Service['identifier'] ) ) ) $defualt_Service['identifier'] = '';
							if( !isset( $defualt_Service['additionalType'] ) || ( isset( $defualt_Service['additionalType'] ) && empty( $defualt_Service['additionalType'] ) ) ) $defualt_Service['additionalType'] = '';
							if( !isset( $defualt_Service['ratingValue'] ) || ( isset( $defualt_Service['ratingValue'] ) && empty( $defualt_Service['ratingValue'] ) ) ) $defualt_Service['ratingValue'] = '';
							if( !isset( $defualt_Service['reviewCount'] ) || ( isset( $defualt_Service['reviewCount'] ) && empty( $defualt_Service['reviewCount'] ) ) ) $defualt_Service['reviewCount'] = '';

							#
							if( !isset( $YourColor_Service['priceRange'] ) || ( isset( $YourColor_Service['priceRange'] ) && empty( $YourColor_Service['priceRange'] ) ) ) $YourColor_Service['priceRange'] = $defualt_Service['priceRange'];
							if( !isset( $YourColor_Service['description'] ) || ( isset( $YourColor_Service['description'] ) && empty( $YourColor_Service['description'] ) ) ) $YourColor_Service['description'] = $defualt_Service['description'];

							if( !isset( $YourColor_Service['addressLocality'] ) || ( isset( $YourColor_Service['addressLocality'] ) && empty( $YourColor_Service['addressLocality'] ) ) ) $YourColor_Service['addressLocality'] = $defualt_Service['addressLocality'];
							if( !isset( $YourColor_Service['postalCode'] ) || ( isset( $YourColor_Service['postalCode'] ) && empty( $YourColor_Service['postalCode'] ) ) ) $YourColor_Service['postalCode'] = $defualt_Service['postalCode'];
							if( !isset( $YourColor_Service['telephone'] ) || ( isset( $YourColor_Service['telephone'] ) && empty( $YourColor_Service['telephone'] ) ) ) $YourColor_Service['telephone'] = $defualt_Service['telephone'];
							if( !isset( $YourColor_Service['addressCountry'] ) || ( isset( $YourColor_Service['addressCountry'] ) && empty( $YourColor_Service['addressCountry'] ) ) ) $YourColor_Service['addressCountry'] = $defualt_Service['addressCountry'];
							if( !isset( $YourColor_Service['streetAddress'] ) || ( isset( $YourColor_Service['streetAddress'] ) && empty( $YourColor_Service['streetAddress'] ) ) ) $YourColor_Service['streetAddress'] = $defualt_Service['streetAddress'];
							if( !isset( $YourColor_Service['addressRegion'] ) || ( isset( $YourColor_Service['addressRegion'] ) && empty( $YourColor_Service['addressRegion'] ) ) ) $YourColor_Service['addressRegion'] = $defualt_Service['addressRegion'];
							if( !isset( $YourColor_Service['areaServed'] ) || ( isset( $YourColor_Service['areaServed'] ) && empty( $YourColor_Service['areaServed'] ) ) ) $YourColor_Service['areaServed'] = $defualt_Service['areaServed'];
							if( !isset( $YourColor_Service['OfferCatalog'] ) || ( isset( $YourColor_Service['OfferCatalog'] ) && empty( $YourColor_Service['OfferCatalog'] ) ) ) $YourColor_Service['OfferCatalog'] = $defualt_Service['OfferCatalog'];
							if( !isset( $YourColor_Service['identifier'] ) || ( isset( $YourColor_Service['identifier'] ) && empty( $YourColor_Service['identifier'] ) ) ) $YourColor_Service['identifier'] = $defualt_Service['identifier'];
							if( !isset( $YourColor_Service['additionalType'] ) || ( isset( $YourColor_Service['additionalType'] ) && empty( $YourColor_Service['additionalType'] ) ) ) $YourColor_Service['additionalType'] = $defualt_Service['additionalType'];
							if( !isset( $YourColor_Service['ratingValue'] ) || ( isset( $YourColor_Service['ratingValue'] ) && empty( $YourColor_Service['ratingValue'] ) ) ) $YourColor_Service['ratingValue'] = $defualt_Service['ratingValue'];
							if( !isset( $YourColor_Service['reviewCount'] ) || ( isset( $YourColor_Service['reviewCount'] ) && empty( $YourColor_Service['reviewCount'] ) ) ) $YourColor_Service['reviewCount'] = $defualt_Service['reviewCount'];

							if( !empty( $thumbnail_url ) ){
				                echo '{';
				                  	echo '"@context": "http://schema.org",';
				                  	echo '"@type": "Service",';
				                  	echo '"serviceType": "'.$post->post_title.'",';
				                  	echo '"provider": {';
					                    echo '"@type": "LocalBusiness",';
					                    echo '"name": "'.$post->post_title.'",';
					                    echo '"url": "'.$Permalink.'",';
					                    echo '"priceRange": "'.$YourColor_Service['priceRange'].'",';
					                    echo '"image": "'.$thumbnail_url.'",';
					                    echo '"address": {';
					                        echo '"@type": "PostalAddress",';
					                        echo '"addressLocality": "'.$YourColor_Service['addressLocality'].'",';
					                        echo '"postalCode": "'.$YourColor_Service['postalCode'].'",';
					                        echo '"telephone": "'.$YourColor_Service['telephone'].'",';
					                        echo '"addressCountry": "'.$YourColor_Service['addressCountry'].'",';
					                        echo '"streetAddress": "'.$YourColor_Service['streetAddress'].'",';
					                        echo '"addressRegion": "'.$YourColor_Service['addressRegion'].'"';
				                      	echo '}';
				                  	echo '},';
				                  	echo '"areaServed": {';
										echo '"@type": "Place",';
										echo '"name": "'.$YourColor_Service['areaServed'].'"';
				                  	echo '},';
				                  	echo '"description": "'.$YourColor_Service['description'].'",';
				                  	echo '"url": "'.$Permalink.'",';
				                  	echo '"hasOfferCatalog": {';
					                    echo '"@type":"OfferCatalog",';
					                    echo '"name" : "'.$YourColor_Service['OfferCatalog'].'"';
				                    echo '},';
				                  	echo '"identifier": "'.$YourColor_Service['identifier'].'",';
				                  	echo '"additionalType": "'.$YourColor_Service['additionalType'].'"';
				                  	/*echo '"aggregateRating": {';
					                    echo '"@type": "AggregateRating",';
					                    echo '"ratingValue": "'.$YourColor_Service['ratingValue'].'",';
					                    echo '"reviewCount": "'.$YourColor_Service['reviewCount'].'"';
				                  	echo '}';*/
				                echo '}';
							}
						}
					}
			 	echo '</script>';

				# 
				echo '<script type="application/ld+json">';
	          		$hide_schema_Article = get_option('hide_schema_Article');
	        		if( empty( $hide_schema_Article ) ) {
						$YourColor_Article = get_post_meta( $post->ID,'YourColor_Article',true);
						$YourColor_Article = ( is_array( $YourColor_Article ) ) ? $YourColor_Article : array();
						#
						if( !isset( $YourColor_Article['hide_schema_Article'] ) || ( isset( $YourColor_Article['hide_schema_Article'] ) && empty( $YourColor_Article['hide_schema_Service'] ) ) ) {

							$defualt_Service = get_option('YourColor_Article');
							$defualt_Service = ( is_array( $defualt_Service ) ) ? $defualt_Service : array();
							if( !isset( $defualt_Service['headline'] ) || ( isset( $defualt_Service['headline'] ) && empty( $defualt_Service['headline'] ) ) ) $defualt_Service['headline'] = wp_trim_words( $post->post_content,20);
							if( !isset( $defualt_Service['description'] ) || ( isset( $defualt_Service['description'] ) && empty( $defualt_Service['description'] ) ) ) $defualt_Service['description'] = '';
							if( !isset( $defualt_Service['articleBody'] ) || ( isset( $defualt_Service['articleBody'] ) && empty( $defualt_Service['articleBody'] ) ) ) $defualt_Service['articleBody'] = '';

							#
							if( !isset( $YourColor_Article['description'] ) || ( isset( $YourColor_Article['description'] ) && empty( $YourColor_Article['description'] ) ) ) $YourColor_Article['description'] = $defualt_Service['description'];
							if( !isset( $YourColor_Article['headline'] ) || ( isset( $YourColor_Article['headline'] ) && empty( $YourColor_Article['headline'] ) ) ) $YourColor_Article['headline'] = $defualt_Service['headline'];
							if( !isset( $YourColor_Article['articleBody'] ) || ( isset( $YourColor_Article['articleBody'] ) && empty( $YourColor_Article['articleBody'] ) ) ) $YourColor_Article['articleBody'] = $defualt_Service['articleBody'];

							if( !empty( $thumbnail_url ) ){
						        echo '{';
						          	echo '"@context": "http://schema.org",';
						          	echo '"@type": "Article",';
						          	echo '"author": {';
						                echo '"@type": "Person",';
						                echo '"name": "'.$post_author->display_name.'",';
						                echo '"url": "'.$Author__url.'"';
						          	echo '},';
						          	echo '"headline": "'.$YourColor_Article['headline'].'",';
						          	echo '"image": [';
						            	echo '"'.$thumbnail_url.'"';
						          	echo '],';
						          	echo '"datePublished": "'.date('c', strtotime( $publish_date) ).'",';
						          	echo '"dateModified": "'.date('c', strtotime( $modified_date) ).'",';
						          	echo '"publisher": {';
					                    echo '"@type": "Organization",';
					                    echo '"name": "'.$sitename__schema.'",';
				                    	echo '"logo": {';
					                        echo '"@type": "ImageObject",';
					                        echo '"url": "'.$logo__schema.'"';
				                      	echo '}';
				                  	echo '},';
						          	echo '"description": "'.$defualt_Service['description'].'",';
						          	echo '"articleBody": "'.$YourColor_Article['articleBody'].'"';
						        echo '}';
							}
						}
					}
			 	echo '</script>';
			 	## faqs
			 	echo '<script type="application/ld+json">';
	          		$hide_schema_faqs = get_option('hide_schema_faqs');
	        		if( empty( $hide_schema_faqs ) ) {

					    $questionsMeta = get_post_meta($post->ID, 'yourcolor__faqs', true);
					    $questionsMeta = ( ( is_array( $questionsMeta ) ) ) ? $questionsMeta : array();
					    if( !empty( $questionsMeta ) ){

						    $questions = array();
						    if(!empty($questionsMeta)){
						        foreach( $questionsMeta as $q ) {
						            if( !empty($q['question']) ) {
						                $questions[] = $q;
						            }
						        }
						    }
				            echo '{';
				                echo '"@context": "https://schema.org",';
				                echo '"@type": "FAQPage",';
				                echo '"mainEntity": [';
				                    foreach( $questions as $i => $faq ){
				                        $i++;
				                        echo '{';
				                        echo '"@type": "Question",';
				                        echo '"name": "'.$faq['question'].'",';
				                        echo '"acceptedAnswer": {';
				                            echo '"@type": "Answer",';
				                            echo '"text": "'.$faq['answer'].'"';
				                            echo '}';
				                        echo '}';
				                        if( $i < count($questions) ){
				                            echo ',';
				                        }
				                    }
				                echo ']';
				            echo '}';
					    }
						
					}

            	echo '</script>';
            	## Rating Schema
            	echo '<script type="application/ld+json">';
	          		$hide_schema_Rating = get_option('hide_schema_Rating');
	        		if( empty( $hide_schema_Rating ) ) {
						$YourColor__Rating = get_post_meta( $post->ID,'YourColor__Rating',true);
						$YourColor__Rating = ( is_array( $YourColor__Rating ) ) ? $YourColor__Rating : array();
						#
						if( !isset( $YourColor__Rating['hide_schema_rating'] ) || ( isset( $YourColor__Rating['hide_schema_rating'] ) && empty( $YourColor__Rating['hide_schema_rating'] ) ) ) {

							$defualt_Rating = get_option('YourColor_Rating');
							$defualt_Rating = ( is_array( $defualt_Rating ) ) ? $defualt_Rating : array();

							# # Get Option
							if( !isset( $defualt_Rating['RatingValue_def'] ) || ( isset( $defualt_Rating['RatingValue_def'] ) && empty( $defualt_Rating['RatingValue_def'] ) ) ) $defualt_Rating['RatingValue_def'] = '';
							#
							if( !isset( $defualt_Rating['Best_Rating_def'] ) || ( isset( $defualt_Rating['Best_Rating_def'] ) && empty( $defualt_Rating['Best_Rating_def'] ) ) ) $defualt_Rating['Best_Rating_def'] = '';
							#
							if( !isset( $defualt_Rating['RatingCount_def'] ) || ( isset( $defualt_Rating['RatingCount_def'] ) && empty( $defualt_Rating['RatingCount_def'] ) ) ) $defualt_Rating['RatingCount_def'] = '';

							# post meta
							
							if( !isset( $YourColor__Rating['Rating_Value'] ) || ( isset( $YourColor__Rating['Rating_Value'] ) && empty( $YourColor__Rating['Rating_Value'] ) ) ) $YourColor__Rating['Rating_Value'] = $defualt_Rating['RatingValue_def'];

							if( !isset( $YourColor__Rating['Best_Rating'] ) || ( isset( $YourColor__Rating['Best_Rating'] ) && empty( $YourColor__Rating['Best_Rating'] ) ) ) $YourColor__Rating['Best_Rating'] = $defualt_Rating['Best_Rating_def'];

							if( !isset( $YourColor__Rating['Rating_Count'] ) || ( isset( $YourColor__Rating['Rating_Count'] ) && empty( $YourColor__Rating['Rating_Count'] ) ) ) $YourColor__Rating['Rating_Count'] = $defualt_Rating['RatingCount_def'];


							if( !empty( $YourColor__Rating['Rating_Value'] ) ){
						        echo '{';
						          	echo '"@context": "http://schema.org",';
						          	echo '"@type": "CreativeWorkSeries",';
								    echo '"name": "'.$post->post_title.'",';
								    echo '"aggregateRating": {';
								        echo '"@type": "AggregateRating",';
								        echo '"ratingValue": "'.$YourColor__Rating['Rating_Value'].'",';
								        echo '"bestRating": "'.$YourColor__Rating['Best_Rating'].'",';
								        echo '"ratingCount": "'.$YourColor__Rating['Rating_Count'].'"';
								    echo '}';

						        echo '}';
							}
						}
					}
			 	echo '</script>';
		}

	# ARCHIVE EDITS .
		public function Archive(){
			
		}

	# PAGES EDITS .
		public function Page(){
			
		}

	public function Author(){
	
	}
	# HOME EDITS .	
		public function Home(){
			

			$YourColor_Schema_business = get_option('YourColor_Schema_business');
			$YourColor_Schema_business = ( is_array( $YourColor_Schema_business ) ) ? $YourColor_Schema_business : array();

			$logo__schema = get_option('logo__schema');

			if( !isset( $YourColor_Schema_business['hide_schema_business'] ) || ( isset( $YourColor_Schema_business['hide_schema_business'] ) && empty( $YourColor_Schema_business['hide_schema_business'] ) ) ) {

		        echo '<script type="application/ld+json">';
			        echo '{';
			          	echo '"@context": "http://schema.org",';
			          	echo '"@type": "LocalBusiness",';
			          	echo '"name": "'.( ( isset( $YourColor_Schema_business['Business_Name'] ) && !empty( $YourColor_Schema_business['Business_Name'] ) ) ? $YourColor_Schema_business['Business_Name'] : '' ).'",';
			          	echo '"description": "'.( ( isset( $YourColor_Schema_business['description'] ) && !empty( $YourColor_Schema_business['description'] ) ) ? $YourColor_Schema_business['description'] : '' ).'",';
			          	echo '"address": {';
				            echo '"@type": "PostalAddress",';
				            echo '"streetAddress": "'.( ( isset( $YourColor_Schema_business['Street_Address'] ) && !empty( $YourColor_Schema_business['Street_Address'] ) ) ? $YourColor_Schema_business['Street_Address'] : '' ).'",';
				            echo '"addressLocality": "'.( ( isset( $YourColor_Schema_business['City'] ) && !empty( $YourColor_Schema_business['City'] ) ) ? $YourColor_Schema_business['City'] : '' ).'",';
				            echo '"addressRegion": "'.( ( isset( $YourColor_Schema_business['State'] ) && !empty( $YourColor_Schema_business['State'] ) ) ? $YourColor_Schema_business['State'] : '' ).'",';
				            echo '"postalCode": "'.( ( isset( $YourColor_Schema_business['Postal_Code'] ) && !empty( $YourColor_Schema_business['Postal_Code'] ) ) ? $YourColor_Schema_business['Postal_Code'] : '' ).'",';
				            echo '"addressCountry": "'.( ( isset( $YourColor_Schema_business['Country'] ) && !empty( $YourColor_Schema_business['Country'] ) ) ? $YourColor_Schema_business['Country'] : '' ).'"';
			          	echo '},';
			          	echo '"telephone": "'.( ( isset( $YourColor_Schema_business['telephone'] ) && !empty( $YourColor_Schema_business['telephone'] ) ) ? $YourColor_Schema_business['telephone'] : '' ).'",';
			            echo '"url": "'.home_url().'",';
			            echo '"image": "'.( ( !empty( $logo__schema ) ) ? $logo__schema : '' ).'",';
			            echo '"openingHours": "'.( ( isset( $YourColor_Schema_business['openingHours'] ) && !empty( $YourColor_Schema_business['openingHours'] ) ) ? $YourColor_Schema_business['openingHours'] : '' ).'",';
			            echo '"priceRange": "'.( ( isset( $YourColor_Schema_business['Price_Range'] ) && !empty( $YourColor_Schema_business['Price_Range'] ) ) ? $YourColor_Schema_business['Price_Range'] : '' ).'",';
			          	/*echo '"socialMedia": {';
				            echo '"Facebook": "'.( ( isset( $YourColor_Schema_business['Facebook'] ) && !empty( $YourColor_Schema_business['Facebook'] ) ) ? $YourColor_Schema_business['Facebook'] : '' ).'",';
				            echo '"Twitter": "'.( ( isset( $YourColor_Schema_business['Twitter'] ) && !empty( $YourColor_Schema_business['Twitter'] ) ) ? $YourColor_Schema_business['Twitter'] : '' ).'",';
				            echo '"Instagram": "'.( ( isset( $YourColor_Schema_business['Instagram'] ) && !empty( $YourColor_Schema_business['Instagram'] ) ) ? $YourColor_Schema_business['Instagram'] : '' ).'",';
				            echo '"Pinterest": "'.( ( isset( $YourColor_Schema_business['Pinterest'] ) && !empty( $YourColor_Schema_business['Pinterest'] ) ) ? $YourColor_Schema_business['Pinterest'] : '' ).'",';
				            echo '"Linkedin": "'.( ( isset( $YourColor_Schema_business['Linkedin'] ) && !empty( $YourColor_Schema_business['Linkedin'] ) ) ? $YourColor_Schema_business['Linkedin'] : '' ).'",';
				            echo '"Soundcloud": "'.( ( isset( $YourColor_Schema_business['Soundcloud'] ) && !empty( $YourColor_Schema_business['Soundcloud'] ) ) ? $YourColor_Schema_business['Soundcloud'] : '' ).'",';
				            echo '"Tumblr": "'.( ( isset( $YourColor_Schema_business['Tumblr'] ) && !empty( $YourColor_Schema_business['Tumblr'] ) ) ? $YourColor_Schema_business['Tumblr'] : '' ).'",';
				            echo '"Youtube": "'.( ( isset( $YourColor_Schema_business['Youtube'] ) && !empty( $YourColor_Schema_business['Youtube'] ) ) ? $YourColor_Schema_business['Youtube'] : '' ).'"';
			          	echo '},';*/
			          	echo '"aggregateRating": {';
				            echo '"@type": "AggregateRating",';
				            echo '"ratingValue": "'.( ( isset( $YourColor_Schema_business['ratingValue'] ) && !empty( $YourColor_Schema_business['ratingValue'] ) ) ? $YourColor_Schema_business['ratingValue'] : '' ).'",';
				            echo '"reviewCount": "'.( ( isset( $YourColor_Schema_business['Rating_Count'] ) && !empty( $YourColor_Schema_business['Rating_Count'] ) ) ? $YourColor_Schema_business['Rating_Count'] : '' ).'"';
			          	echo '}';
			          	#echo '"website": "'.home_url().'",';
			          	/*echo '"serviceOffered": {';
				            echo '"@type": "Service",';
				            echo '"url": "'.home_url().'",';
				            echo '"name": "'.( ( isset( $YourColor_Schema_business['Service_Offered_Name'] ) && !empty( $YourColor_Schema_business['Service_Offered_Name'] ) ) ? $YourColor_Schema_business['Service_Offered_Name'] : '' ).'"';
			          	echo '},'*/;
			          	#echo '"operationDays": "'.( ( isset( $YourColor_Schema_business['Operation_Days'] ) && !empty( $YourColor_Schema_business['Operation_Days'] ) ) ? $YourColor_Schema_business['Operation_Days'] : '' ).'"';
			        echo '}';
		        echo '</script>';

			}
			## Schema SearchAction
		 	$YourColor_Schema_websites = get_option('YourColor_Schema_websites');
		 	$YourColor_Schema_websites = ( is_array( $YourColor_Schema_websites ) ) ? $YourColor_Schema_websites : array();
		 	if( !isset( $YourColor_Schema_websites['hide_schema_websites'] ) || ( isset( $YourColor_Schema_websites['hide_schema_websites'] ) && empty( $YourColor_Schema_websites['hide_schema_websites'] ) ) ) {
            	echo '<script type="application/ld+json">';
					echo '{';
						echo '"@context": "http://schema.org",';
						echo '"@type": "WebSite",';
						echo '"url": "'.home_url().'",';
						echo '"potentialAction": {';
							echo '"@type": "SearchAction",';
							echo '"target": "'.home_url().'/?s={s}",';
							echo '"query-input": "required name=s"';
						echo '}';
					echo '}';
				echo '</script>';
			}

		}

	public function insert__schema(){

		$validate__schema = get_option('validate__schema');

		if( !empty( $validate__schema ) ) return ;

		if( is_home() ) $this->Home();

		if( is_page() ) $this->Page();

		if( is_single() ) $this->Single();

		if( is_archive() || is_category() || is_tax() ) $this->Archive();

		if( is_author() ) $this->Author();
	}

	public function Setup(){
		add_action('wp_head', array( $this,'insert__schema') );
	}
}
(new YourColor__Schema)->Setup();
