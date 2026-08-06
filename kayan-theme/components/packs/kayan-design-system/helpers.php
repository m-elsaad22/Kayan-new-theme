<?php 
if ( ! function_exists( 'kayan_gradient_builder_defaults' ) ) {
	function kayan_gradient_builder_defaults() {
		return array(
			'enabled' => '',
			'type' => 'linear',
			'angle' => '135',
			'radial_shape' => 'circle',
			'radial_position' => 'center',
			'apply_target' => 'body',
			'stops' => array(
				0 => array(
					'color' => '#a03576',
					'position' => '0',
				),
				1 => array(
					'color' => '#2563eb',
					'position' => '100',
				),
			),
		);
	}
}

if ( ! function_exists( 'kayan_normalize_gradient_builder' ) ) {
	function kayan_normalize_gradient_builder( $value ) {
		$defaults = kayan_gradient_builder_defaults();
		if ( ! is_array( $value ) ) {
			return $defaults;
		}

		$value = wp_parse_args( $value, $defaults );

		if ( ! is_array( $value['stops'] ) || empty( $value['stops'] ) ) {
			$value['stops'] = $defaults['stops'];
		}

		$normalized_stops = array();
		foreach ( $value['stops'] as $stop ) {
			if ( ! is_array( $stop ) ) {
				continue;
			}
			$color = isset( $stop['color'] ) ? sanitize_hex_color( $stop['color'] ) : '';
			if ( empty( $color ) ) {
				continue;
			}
			$position = isset( $stop['position'] ) ? floatval( $stop['position'] ) : 0;
			if ( $position < 0 ) {
				$position = 0;
			}
			if ( $position > 100 ) {
				$position = 100;
			}
			$normalized_stops[] = array(
				'color' => $color,
				'position' => (string) $position,
			);
		}

		if ( count( $normalized_stops ) < 2 ) {
			$value['stops'] = $defaults['stops'];
		} else {
			usort(
				$normalized_stops,
				function( $a, $b ) {
					return floatval( $a['position'] ) <=> floatval( $b['position'] );
				}
			);
			$value['stops'] = array_values( $normalized_stops );
		}

		if ( ! in_array( $value['type'], array( 'linear', 'radial' ), true ) ) {
			$value['type'] = 'linear';
		}

		$value['angle'] = (string) max( 0, min( 360, intval( $value['angle'] ) ) );

		if ( ! in_array( $value['radial_shape'], array( 'circle', 'ellipse' ), true ) ) {
			$value['radial_shape'] = 'circle';
		}

		$allowed_positions = array( 'center', 'top', 'bottom', 'left', 'right' );
		if ( ! in_array( $value['radial_position'], $allowed_positions, true ) ) {
			$value['radial_position'] = 'center';
		}

		$allowed_targets = array( 'body', 'header', 'buttons' );
		if ( ! in_array( $value['apply_target'], $allowed_targets, true ) ) {
			$value['apply_target'] = 'body';
		}

		return $value;
	}
}

if ( ! function_exists( 'kayan_gradient_builder_to_css' ) ) {
	function kayan_gradient_builder_to_css( $value ) {
		$value = kayan_normalize_gradient_builder( $value );

		if ( empty( $value['enabled'] ) ) {
			return '';
		}

		$parts = array();
		foreach ( $value['stops'] as $stop ) {
			$parts[] = $stop['color'] . ' ' . floatval( $stop['position'] ) . '%';
		}

		if ( empty( $parts ) ) {
			return '';
		}

		$stops_css = implode( ', ', $parts );

		if ( $value['type'] === 'radial' ) {
			return 'radial-gradient(' . $value['radial_shape'] . ' at ' . $value['radial_position'] . ', ' . $stops_css . ')';
		}

		return 'linear-gradient(' . intval( $value['angle'] ) . 'deg, ' . $stops_css . ')';
	}
}

if ( ! function_exists( 'kayan_get_gradient_builder_option' ) ) {
	function kayan_get_gradient_builder_option() {
		$value = yc_get_option( 'kayan_gradient_builder', array() );
		return kayan_normalize_gradient_builder( $value );
	}
}

if ( ! function_exists( 'kayan_get_global_gradient_css' ) ) {
	function kayan_get_global_gradient_css() {
		return kayan_gradient_builder_to_css( kayan_get_gradient_builder_option() );
	}
}

if ( ! function_exists( 'kayan_render_global_gradient_styles' ) ) {
	function kayan_render_global_gradient_styles() {
		$settings = kayan_get_gradient_builder_option();
		$css = kayan_gradient_builder_to_css( $settings );

		if ( empty( $css ) ) {
			return;
		}

		$target = $settings['apply_target'];
		$selector = 'body.before-start';

		if ( $target === 'header' ) {
			$selector = 'header.-YC-Header-Main';
		} elseif ( $target === 'buttons' ) {
			$selector = '.-YC-Button, .-YC-Button-Primary, button[type="submit"]';
		}

		echo '<style id="kayan-global-gradient">';
			echo esc_html( $selector ) . '{background:' . esc_html( $css ) . ' !important;background-image:' . esc_html( $css ) . ' !important;}';
			echo ':root{--kayan-global-gradient:' . esc_html( $css ) . ';}';
		echo '</style>';
	}
}

if ( ! function_exists( 'kayan_global_shadows_defaults' ) ) {
	function kayan_global_shadows_defaults() {
		return array(
			'enabled' => '',
			'depth_preset' => 'medium',
			'color' => '#0f172a',
			'opacity' => '16',
			'intensity' => '100',
			'apply_target' => 'cards',
			'layers' => array(
				0 => array(
					'x' => '0',
					'y' => '4',
					'blur' => '12',
					'spread' => '0',
				),
				1 => array(
					'x' => '0',
					'y' => '2',
					'blur' => '4',
					'spread' => '-1',
				),
			),
		);
	}
}

if ( ! function_exists( 'kayan_global_shadows_presets' ) ) {
	function kayan_global_shadows_presets() {
		return array(
			'subtle' => array(
				array( 'x' => '0', 'y' => '1', 'blur' => '3', 'spread' => '0' ),
			),
			'medium' => array(
				array( 'x' => '0', 'y' => '4', 'blur' => '12', 'spread' => '0' ),
				array( 'x' => '0', 'y' => '2', 'blur' => '4', 'spread' => '-1' ),
			),
			'deep' => array(
				array( 'x' => '0', 'y' => '10', 'blur' => '30', 'spread' => '-5' ),
				array( 'x' => '0', 'y' => '6', 'blur' => '16', 'spread' => '-4' ),
			),
			'floating' => array(
				array( 'x' => '0', 'y' => '20', 'blur' => '40', 'spread' => '-8' ),
				array( 'x' => '0', 'y' => '8', 'blur' => '20', 'spread' => '-6' ),
			),
		);
	}
}

if ( ! function_exists( 'kayan_hex_to_rgba' ) ) {
	function kayan_hex_to_rgba( $hex, $opacity ) {
		$hex = sanitize_hex_color( $hex );
		if ( empty( $hex ) ) {
			$hex = '#0f172a';
		}

		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		$alpha = max( 0, min( 100, floatval( $opacity ) ) ) / 100;

		return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $alpha . ')';
	}
}

if ( ! function_exists( 'kayan_normalize_global_shadows' ) ) {
	function kayan_normalize_global_shadows( $value ) {
		$defaults = kayan_global_shadows_defaults();
		if ( ! is_array( $value ) ) {
			return $defaults;
		}

		$value = wp_parse_args( $value, $defaults );
		$presets = kayan_global_shadows_presets();

		if ( ! in_array( $value['depth_preset'], array( 'subtle', 'medium', 'deep', 'floating', 'custom' ), true ) ) {
			$value['depth_preset'] = 'medium';
		}

		$value['color'] = sanitize_hex_color( $value['color'] );
		if ( empty( $value['color'] ) ) {
			$value['color'] = $defaults['color'];
		}

		$value['opacity'] = (string) max( 0, min( 100, intval( $value['opacity'] ) ) );
		$value['intensity'] = (string) max( 50, min( 150, intval( $value['intensity'] ) ) );

		$allowed_targets = array( 'cards', 'buttons', 'header', 'widgets', 'all' );
		if ( ! in_array( $value['apply_target'], $allowed_targets, true ) ) {
			$value['apply_target'] = 'cards';
		}

		if ( $value['depth_preset'] !== 'custom' ) {
			$value['layers'] = $presets[ $value['depth_preset'] ];
		} elseif ( ! is_array( $value['layers'] ) || empty( $value['layers'] ) ) {
			$value['layers'] = $defaults['layers'];
		}

		$normalized_layers = array();
		foreach ( $value['layers'] as $layer ) {
			if ( ! is_array( $layer ) ) {
				continue;
			}
			$normalized_layers[] = array(
				'x' => (string) intval( $layer['x'] ?? 0 ),
				'y' => (string) max( 0, intval( $layer['y'] ?? 0 ) ),
				'blur' => (string) max( 0, intval( $layer['blur'] ?? 0 ) ),
				'spread' => (string) intval( $layer['spread'] ?? 0 ),
			);
		}

		if ( empty( $normalized_layers ) ) {
			$value['layers'] = $defaults['layers'];
		} else {
			$value['layers'] = array_values( $normalized_layers );
		}

		return $value;
	}
}

if ( ! function_exists( 'kayan_scale_shadow_layers' ) ) {
	function kayan_scale_shadow_layers( $layers, $intensity, $multiplier = 1 ) {
		$scale = max( 0.5, min( 1.5, floatval( $intensity ) / 100 ) ) * $multiplier;
		$scaled = array();

		foreach ( $layers as $layer ) {
			$scaled[] = array(
				'x' => (string) round( intval( $layer['x'] ) * $scale ),
				'y' => (string) round( intval( $layer['y'] ) * $scale ),
				'blur' => (string) round( intval( $layer['blur'] ) * $scale ),
				'spread' => (string) round( intval( $layer['spread'] ) * $scale ),
			);
		}

		return $scaled;
	}
}

if ( ! function_exists( 'kayan_global_shadows_layers_to_css' ) ) {
	function kayan_global_shadows_layers_to_css( $layers, $color, $opacity ) {
		$rgba = kayan_hex_to_rgba( $color, $opacity );
		$parts = array();

		foreach ( $layers as $layer ) {
			$parts[] = intval( $layer['x'] ) . 'px ' . intval( $layer['y'] ) . 'px ' . intval( $layer['blur'] ) . 'px ' . intval( $layer['spread'] ) . 'px ' . $rgba;
		}

		return implode( ', ', $parts );
	}
}

if ( ! function_exists( 'kayan_global_shadows_to_css' ) ) {
	function kayan_global_shadows_to_css( $value, $variant = 'default' ) {
		$value = kayan_normalize_global_shadows( $value );

		if ( empty( $value['enabled'] ) ) {
			return '';
		}

		$multiplier = $variant === 'hover' ? 1.2 : 1;
		$layers = kayan_scale_shadow_layers( $value['layers'], $value['intensity'], $multiplier );

		return kayan_global_shadows_layers_to_css( $layers, $value['color'], $value['opacity'] );
	}
}

if ( ! function_exists( 'kayan_get_global_shadows_option' ) ) {
	function kayan_get_global_shadows_option() {
		$value = yc_get_option( 'kayan_global_shadows', array() );
		return kayan_normalize_global_shadows( $value );
	}
}

if ( ! function_exists( 'kayan_get_global_shadow_depth_tokens' ) ) {
	function kayan_get_global_shadow_depth_tokens( $value = null ) {
		$value = kayan_normalize_global_shadows( $value ?? kayan_get_global_shadows_option() );
		$presets = kayan_global_shadows_presets();

		return array(
			'sm' => kayan_global_shadows_layers_to_css(
				kayan_scale_shadow_layers( $presets['subtle'], $value['intensity'], 1 ),
				$value['color'],
				$value['opacity']
			),
			'md' => kayan_global_shadows_layers_to_css(
				kayan_scale_shadow_layers( $presets['medium'], $value['intensity'], 1 ),
				$value['color'],
				$value['opacity']
			),
			'lg' => kayan_global_shadows_layers_to_css(
				kayan_scale_shadow_layers( $presets['deep'], $value['intensity'], 1 ),
				$value['color'],
				$value['opacity']
			),
			'xl' => kayan_global_shadows_layers_to_css(
				kayan_scale_shadow_layers( $presets['floating'], $value['intensity'], 1 ),
				$value['color'],
				$value['opacity']
			),
		);
	}
}

if ( ! function_exists( 'kayan_get_shadow_target_selectors' ) ) {
	function kayan_get_shadow_target_selectors( $target ) {
		$map = array(
			'cards' => '.yc-shortcode--single-features-item, .--single--work-post-box, .-YC-Products-TabHead span, .-fix-post-box',
			'buttons' => '.-YC-Button, .-YC-Button-Primary, button[type="submit"], .-DropChevrons-UL>ul>li>span',
			'header' => 'header.-YC-Header-Main, .-YC-Header-Top-Bar',
			'widgets' => '[class*="-widget"], [class*="--widget"], .YourColor-IntroBoxes',
			'all' => '.yc-shortcode--single-features-item, .--single--work-post-box, .-YC-Button, .-YC-Button-Primary, button[type="submit"], header.-YC-Header-Main, [class*="-widget"], [class*="--widget"]',
		);

		return isset( $map[ $target ] ) ? $map[ $target ] : $map['cards'];
	}
}

if ( ! function_exists( 'kayan_render_global_shadow_styles' ) ) {
	function kayan_render_global_shadow_styles() {
		$settings = kayan_get_global_shadows_option();
		$shadow = kayan_global_shadows_to_css( $settings );
		$shadow_hover = kayan_global_shadows_to_css( $settings, 'hover' );

		if ( empty( $shadow ) ) {
			return;
		}

		$tokens = kayan_get_global_shadow_depth_tokens( $settings );
		$selector = kayan_get_shadow_target_selectors( $settings['apply_target'] );

		echo '<style id="kayan-global-shadows">';
			echo ':root{';
				echo '--kayan-global-shadow:' . esc_html( $shadow ) . ';';
				echo '--kayan-shadow-sm:' . esc_html( $tokens['sm'] ) . ';';
				echo '--kayan-shadow-md:' . esc_html( $tokens['md'] ) . ';';
				echo '--kayan-shadow-lg:' . esc_html( $tokens['lg'] ) . ';';
				echo '--kayan-shadow-xl:' . esc_html( $tokens['xl'] ) . ';';
				echo '--box-shadow-defult:' . esc_html( $shadow ) . ';';
				echo '--box-shadow-hover:' . esc_html( $shadow_hover ) . ';';
			echo '}';
			echo esc_html( $selector ) . '{box-shadow:var(--kayan-global-shadow);}';
		echo '</style>';
	}
}

if ( ! function_exists( 'kayan_homepage_sections_order_defaults' ) ) {
	function kayan_homepage_sections_order_defaults() {
		return array(
			'enabled' => '',
			'sections' => array(),
		);
	}
}

if ( ! function_exists( 'kayan_get_homepage_sections_catalog' ) ) {
	function kayan_get_homepage_sections_catalog() {
		if ( function_exists( 'kayan_homepage_uses_new_design' ) && kayan_homepage_uses_new_design() ) {
			return array();
		}

		$sections = array();
		$home_intro = yc_get_option( 'HomeIntro' );

		if ( is_array( $home_intro ) && ! empty( $home_intro['SelectedModel'] ) ) {
			$sections[] = array(
				'section_id' => 'intro',
				'type' => 'intro',
				'label' => is_rtl() ? 'قسم المقدمة (Intro)' : 'Intro Section',
				'meta' => $home_intro['SelectedModel'],
			);
		}

		$home_widgets = yc_get_option( 'widgets_home__meta' );
		if ( ! is_array( $home_widgets ) ) {
			return $sections;
		}

		global $yc__widgets__center;

		foreach ( $home_widgets as $widget_key => $widget ) {
			if ( empty( $widget['widget_id'] ) || empty( $widget['widget_post__id'] ) ) {
				continue;
			}

			$label = $widget['widget_id'];
			if (
				isset( $yc__widgets__center['Standard']['Packs'][ $widget['widget_id'] ]['title'] )
				&& ! empty( $yc__widgets__center['Standard']['Packs'][ $widget['widget_id'] ]['title'] )
			) {
				$label = $yc__widgets__center['Standard']['Packs'][ $widget['widget_id'] ]['title'];
			}

			$post = get_post( $widget['widget_post__id'] );
			if ( $post && ! empty( $post->post_title ) ) {
				$label = $post->post_title;
			}

			$sections[] = array(
				'section_id' => 'widget_' . $widget['widget_post__id'],
				'type' => 'widget',
				'widget_key' => (string) $widget_key,
				'widget_post__id' => (string) $widget['widget_post__id'],
				'widget_id' => $widget['widget_id'],
				'label' => $label,
			);
		}

		return $sections;
	}
}

if ( ! function_exists( 'kayan_normalize_homepage_sections_order' ) ) {
	function kayan_normalize_homepage_sections_order( $value ) {
		$value = wp_parse_args( is_array( $value ) ? $value : array(), kayan_homepage_sections_order_defaults() );
		$catalog = kayan_get_homepage_sections_catalog();
		$catalog_by_id = array();

		foreach ( $catalog as $item ) {
			$catalog_by_id[ $item['section_id'] ] = $item;
		}

		$ordered = array();
		$used = array();

		if ( is_array( $value['sections'] ) ) {
			foreach ( $value['sections'] as $saved_section ) {
				if ( ! is_array( $saved_section ) ) {
					continue;
				}

				$section_id = isset( $saved_section['section_id'] ) ? sanitize_key( $saved_section['section_id'] ) : '';
				if ( empty( $section_id ) || ! isset( $catalog_by_id[ $section_id ] ) ) {
					continue;
				}

				$ordered[] = array_merge(
					$catalog_by_id[ $section_id ],
					array(
						'visible' => ! empty( $saved_section['visible'] ) ? '1' : '',
					)
				);
				$used[ $section_id ] = true;
			}
		}

		foreach ( $catalog as $item ) {
			if ( isset( $used[ $item['section_id'] ] ) ) {
				continue;
			}

			$ordered[] = array_merge(
				$item,
				array(
					'visible' => '1',
				)
			);
		}

		$value['sections'] = array_values( $ordered );
		return $value;
	}
}

if ( ! function_exists( 'kayan_get_homepage_sections_order_option' ) ) {
	function kayan_get_homepage_sections_order_option() {
		return kayan_normalize_homepage_sections_order( yc_get_option( 'kayan_homepage_sections_order', array() ) );
	}
}

if ( ! function_exists( 'kayan_get_homepage_render_queue' ) ) {
	function kayan_get_homepage_render_queue( $home_intro, $home_widgets, $show_intro ) {
		$home_widgets = is_array( $home_widgets ) ? $home_widgets : array();
		$order = kayan_get_homepage_sections_order_option();

		if ( empty( $order['enabled'] ) ) {
			$queue = array();
			if ( $show_intro ) {
				$queue[] = array( 'type' => 'intro' );
			}
			foreach ( $home_widgets as $widget_key => $widget_data ) {
				$queue[] = array(
					'type' => 'widget',
					'key' => $widget_key,
					'data' => $widget_data,
				);
			}
			return $queue;
		}

		$queue = array();
		foreach ( $order['sections'] as $section ) {
			if ( empty( $section['visible'] ) ) {
				continue;
			}

			if ( $section['type'] === 'intro' && $show_intro ) {
				$queue[] = array( 'type' => 'intro' );
				continue;
			}

			if ( $section['type'] !== 'widget' ) {
				continue;
			}

			$widget_key = isset( $section['widget_key'] ) ? $section['widget_key'] : '';
			if ( $widget_key !== '' && isset( $home_widgets[ $widget_key ] ) ) {
				$queue[] = array(
					'type' => 'widget',
					'key' => $widget_key,
					'data' => $home_widgets[ $widget_key ],
				);
				continue;
			}

			if ( empty( $section['widget_post__id'] ) ) {
				continue;
			}

			foreach ( $home_widgets as $fallback_key => $widget_data ) {
				if ( (string) $widget_data['widget_post__id'] === (string) $section['widget_post__id'] ) {
					$queue[] = array(
						'type' => 'widget',
						'key' => $fallback_key,
						'data' => $widget_data,
					);
					break;
				}
			}
		}

		return $queue;
	}
}
