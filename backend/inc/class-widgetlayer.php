<?php
/**
 * Build page using widget layout methods.
 *
 * @package wp-gallery-enhancer
 * @since 1.0.0
 */

namespace WP_Gallery_Enhancer;

/**
 * Build page using widget layout methods.
 *
 * @since  1.0.0
 */
class WidgetLayer {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Array of all widget settings.
	 *
	 * @since  1.0.0
	 * @var    array
	 */
	private $widget_options = [];

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		add_action( 'in_widget_form', [ self::get_instance(), 'extend_widget_form' ], 9, 3 );
		add_filter( 'dynamic_sidebar_params', [ self::get_instance(), 'add_widget_customizations' ] );
		add_filter( 'widget_update_callback', [ self::get_instance(), 'update_settings' ], 10, 2 );
	}

	/**
	 * Get dynamically generated Widget html classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $widget_data {
	 *     Current widget's data to generate customized output.
	 *     @type str   $widget_id  Widget ID.
	 *     @type array $instance   Current widget instance settings.
	 *     @type str   $id_base    Widget ID base.
	 * }
	 * @return array  Verified class string or empty string.
	 */
	public function get_widget_classes( $widget_data ) {
		if ( ! $widget_data || ! is_array( $widget_data ) ) {
			return '';
		}

		$classes      = [];
		$widget_id    = $widget_data[0];
		$instance     = $widget_data[1];
		$id_base      = $widget_data[2];
		$wid_settings = array_intersect_key( $instance, $this->get_widget_options() );

		if ( ! empty( $wid_settings ) ) {
			foreach ( $this->get_widget_options() as $key => $args ) {
				if ( ! isset( $instance[ $key ] ) ) {
					continue;
				}
				$val = $instance[ $key ];
				switch ( $key ) {
					case 'wpge_gallery_style':
						if ( $val ) {
							$classes[] = $val;
						}
						break;
					case 'wpge_crop_size':
						if ( $val ) {
							$classes[] = 'is-cropped';
							$classes[] = $val;
						}
						break;
					case 'wpge_crop_pos':
						if ( $val ) {
							$classes[] = $val;
						}
						break;
					default:
						break;
				}
			}
		}

		$widget_classes = apply_filters( 'wpge_widget_custom_classes', $classes, $widget_data );
		$widget_classes = array_map( 'esc_attr', $widget_classes );
		$widget_classes = array_unique( $widget_classes );

		return join( ' ', $widget_classes );
	}

	/**
	 * Get array of all widget options.
	 *
	 * @since 1.0.0
	 */
	public function get_widget_options() {
		if ( ! empty( $this->widget_options ) ) {
			return $this->widget_options;
		}

		$this->widget_options = apply_filters(
			'wpge_widget_options',
			[
				'wpge_gallery_style' => [
					'setting' => 'wpge_gallery_style',
					'label'   => esc_html__( 'Gallery Style', 'wp-gallery-enhancer' ),
					'default' => esc_html__( '-- Default Gallery --', 'wp-gallery-enhancer' ),
					'type'    => 'select',
					'id_base' => 'media_gallery',
					'choices' => [
						'wpge-wid-slider'  => esc_html__( 'Slider', 'wp-gallery-enhancer' ),
						'wpge-wid-masonry' => esc_html__( 'Masonry', 'wp-gallery-enhancer' ),
					],
				],
				'wpge_crop_size'     => [
					'setting' => 'wpge_crop_size',
					'label'   => esc_html__( 'Image Cropping Size', 'wp-gallery-enhancer' ),
					'default' => esc_html__( '-- No Cropping --', 'wp-gallery-enhancer' ),
					'type'    => 'select',
					'id_base' => 'media_gallery',
					'choices' => [
						'land1'    => esc_html__( 'Landscape (4:3)', 'wp-gallery-enhancer' ),
						'land2'    => esc_html__( 'Landscape (3:2)', 'wp-gallery-enhancer' ),
						'port1'    => esc_html__( 'Portrait (3:4)', 'wp-gallery-enhancer' ),
						'port2'    => esc_html__( 'Portrait (2:3)', 'wp-gallery-enhancer' ),
						'widescrn' => esc_html__( 'Widescreen (16:9)', 'wp-gallery-enhancer' ),
						'sqre'     => esc_html__( 'Square (1:1)', 'wp-gallery-enhancer' ),
					],
				],
				'wpge_crop_pos'      => [
					'setting' => 'wpge_crop_pos',
					'label'   => esc_html__( 'Image Crop Position', 'wp-gallery-enhancer' ),
					'default' => esc_html__( '-- Default --', 'wp-gallery-enhancer' ),
					'type'    => 'select',
					'id_base' => 'media_gallery',
					'choices' => [
						'topcrop'    => esc_html__( 'Top Left Crop', 'wp-gallery-enhancer' ),
						'centercrop' => esc_html__( 'Center Crop', 'wp-gallery-enhancer' ),
					],
				],
			]
		);

		return $this->widget_options;
	}

	/**
	 * Adds a text filed to widgets for adding classes.
	 *
	 * @since 1.0.0
	 *
	 * @param object $widget The widget instance (passed by reference).
	 * @param null   $return Return null if new fields are added.
	 * @param array  $instance An array of the widget's settings.
	 */
	public function extend_widget_form( $widget, $return, $instance ) {
		$fields = [];
		foreach ( $this->get_widget_options() as $option => $value ) {
			$setting     = $value['setting'];
			$id          = esc_attr( $widget->get_field_id( $setting ) );
			$name        = esc_attr( $widget->get_field_name( $setting ) );
			$instance    = wp_parse_args( $instance, [ $setting => '' ] );
			$value       = wp_parse_args(
				$value,
				[
					'default'        => '',
					'description'    => '',
					'id_base'        => 'all',
					'premium_option' => false,
				]
			);
			$input_attrs = isset( $value['input_attrs'] ) ? (array) $value['input_attrs'] : [];
			$description = $value['description'] ? sprintf( '<span class="%s wid-setting-desc">%s</span>', esc_attr( $value['setting'] ) . '-desc', esc_html( $value['description'] ) ) : '';

			if ( is_array( $value['id_base'] ) ) {
				// Check if current Widget Option to be shown for this widget type.
				if ( ! in_array( $widget->id_base, $value['id_base'], true ) ) {
					continue;
				}
			} else {
				// Check if current Widget Option to be shown for this widget type.
				if ( 'all' !== $value['id_base'] && $widget->id_base !== $value['id_base'] ) {
					continue;
				}
			}

			// Prepare markup for custom widget options.
			switch ( $value['type'] ) {
				case 'select':
					$field  = '<label for="' . $id . '">' . $value['label'] . ': </label>';
					$field .= $description;
					// Select option field.
					$field .= sprintf( '<select name="%s" id="%s">', $name, $id );
					$field .= sprintf( '<option value="">%s</option>', $value['default'] );
					foreach ( $value['choices'] as $val => $label ) {
						$field .= sprintf(
							'<option value="%s" %s>%s</option>',
							esc_attr( $val ),
							selected( $instance[ $setting ], $val, false ),
							$label
						);
					}
					$field .= '</select>';
					$field  = sprintf( '<p class="%s widget-setting">%s</p>', esc_attr( $setting ), $field );
					break;
				default:
					$field  = '<label for="' . $id . '">' . $value['label'] . ': </label>';
					$field .= $description;
					$field .= sprintf( '<input name="%s" id="%s" type="%s" ', $name, $id, esc_attr( $value['type'] ) );
					foreach ( $input_attrs as $attr => $val ) {
						$field .= esc_html( $attr ) . '="' . esc_attr( $val ) . '" ';
					}
					if ( ! isset( $input_attrs['value'] ) ) {
						$field .= sprintf( 'value=%s', ( '' !== $instance[ $setting ] ) ? $instance[ $setting ] : $value['default'] );
					}
					$field .= ' />';
					$field  = sprintf( '<p class="%s widget-setting">%s</p>', esc_attr( $setting ), $field );
					break;
			}
			if ( false === $value['premium_option'] ) {
				$fields['basic'][] = $field;
			} else {
				$fields['premium'][] = $field;
			}
		}

		if ( ! empty( $fields ) ) {
			// Add widget Options Content.
			$content = sprintf( '<div class="wpge-widget-options-content">%s</div>', implode( '', $fields['basic'] ) );

			// Display Widget Options.
			printf( '<div class="wpge-widget-options-section">%s</div>', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Update settings for current widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance The current widget instance's settings.
	 * @param array $new_instance Array of new widget settings.
	 * @return false|array
	 */
	public function update_settings( $instance, $new_instance ) {

		foreach ( $this->get_widget_options() as $option => $value ) {
			$setting      = $value['setting'];
			$new_instance = wp_parse_args( $new_instance, [ $setting => '' ] );
			switch ( $value['type'] ) {
				case 'select':
					$instance[ $setting ] = array_key_exists( $new_instance[ $setting ], $value['choices'] ) ? $new_instance[ $setting ] : '';
					break;
				default:
					$instance[ $setting ] = '';
					break;
			}
		}
		return $instance;
	}

	/**
	 * Adds the classes to the widget in the front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $params Parameters passed to a widget's display callback.
	 * @return false|array
	 */
	public function add_widget_customizations( $params ) {

		if ( is_admin() || ! isset( $params[0] ) ) {
			return $params;
		}

		$widget_data = $this->get_widget_data_from_id( $params[0]['widget_id'] );
		if ( false === $widget_data ) {
			return $params;
		}

		// Return if not media gallery widget.
		if ( 'media_gallery' !== $widget_data[2] ) {
			return $params;
		}

		$custom_classes = $this->get_widget_classes( $widget_data );
		if ( ! $custom_classes ) {
			return $params;
		}

		$before_widget = $params[0]['before_widget'];
		if ( false !== strpos( $before_widget, 'class="' ) ) {
			$params[0]['before_widget'] = str_replace( 'class="', 'class="' . $custom_classes . ' ', $params[0]['before_widget'] );
		} elseif ( false !== strpos( $before_widget, "class='" ) ) {
			$params[0]['before_widget'] = str_replace( "class='", "class='" . $custom_classes . ' ', $params[0]['before_widget'] );
		} else {
			// If class attribute is not available, let's add a html wrapper around the widget.
			$custom_html                = sprintf( '<div class="wpge-gallery %s">', $custom_classes );
			$params[0]['before_widget'] = $custom_html . $params[0]['before_widget'];
			$params[0]['after_widget']  = $params[0]['after_widget'] . '</div>';
		}

		return $params;
	}

	/**
	 * Get widget settings and other information from widget id.
	 *
	 * @since 1.0.0
	 *
	 * @param str $widget_id   Widget ID.
	 * @return false|array
	 */
	public function get_widget_data_from_id( $widget_id ) {
		global $wp_registered_widgets;

		// Get widget parameters.
		if ( isset( $wp_registered_widgets[ $widget_id ] ) ) {
			$widget_params = $wp_registered_widgets[ $widget_id ];
		} else {
			return false;
		}

		/*
		 * Widget's display callback function is actually an array of widget object
		 * and 'display callback' method. Let's use that object to get widget settings.
		 */
		if ( ! ( is_array( $widget_params['callback'] ) && is_object( $widget_params['callback'][0] ) ) ) {
			return false;
		}
		$widget_obj = $widget_params['callback'][0];
		if ( ! ( method_exists( $widget_obj, 'get_settings' ) && isset( $widget_params['params'][0]['number'] ) ) ) {
			return false;
		}
		$instances = $widget_obj->get_settings();
		$number    = $widget_params['params'][0]['number'];
		if ( array_key_exists( $number, $instances ) ) {
			$instance = $instances[ $number ];
			$id_base  = property_exists( $widget_obj, 'id_base' ) ? $widget_obj->id_base : '';
		} else {
			return false;
		}

		return [ $widget_id, $instance, $id_base ];
	}

	/**
	 * Returns the instance of this class.
	 *
	 * @since  1.0.0
	 *
	 * @return object Instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

WidgetLayer::init();
