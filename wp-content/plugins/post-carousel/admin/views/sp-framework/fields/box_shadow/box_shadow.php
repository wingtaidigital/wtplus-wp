<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: border
 *
 * @since 2.0
 * @version 2.0
 */
if ( ! class_exists( 'SP_PC_Field_box_shadow' ) ) {
	class SP_PC_Field_box_shadow extends SP_PC_Fields {

		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'horizontal_icon'        => __( 'X offset', 'smart-post-show' ),
					'vertical_icon'          => __( 'Y offset', 'smart-post-show' ),
					'blur_icon'              => __( 'Blur', 'smart-post-show' ),
					'spread_icon'            => __( 'Spread', 'smart-post-show' ),
					'horizontal_placeholder' => 'h-offset',
					'vertical_placeholder'   => 'v-offset',
					'blur_placeholder'       => 'blur',
					'spread_placeholder'     => 'spread',
					'horizontal'             => true,
					'vertical'               => true,
					'blur'                   => true,
					'spread'                 => true,
					'color'                  => true,
					'style'                  => true,
					'unit'                   => 'px',
				)
			);

			$default_value = array(
				'horizontal' => '0',
				'vertical'   => '0',
				'blur'       => '0',
				'spread'     => '0',
				'color'      => '#ddd',
				'style'      => 'outset',
			);

			$default_value = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;

			$value = wp_parse_args( $this->value, $default_value );

			echo wp_kses_post( $this->field_before() );

			echo '<div class="spf--inputs">';

			$properties = array();

			foreach ( array( 'horizontal', 'vertical', 'blur', 'spread' ) as $prop ) {
				if ( ! empty( $args[ $prop ] ) ) {
					$properties[] = $prop;
				}
			}

			foreach ( $properties as $property ) {

				$placeholder = ( ! empty( $args[ $property . '_placeholder' ] ) ) ? ' placeholder="' . $args[ $property . '_placeholder' ] . '"' : '';

				echo '<div class="spf--input">';
				echo ( ! empty( $args[ $property . '_icon' ] ) ) ? '<span class="spf--label spf--icon">' . wp_kses_post( $args[ $property . '_icon' ] ) . '</span>' : '';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[' . $property . ']' ) ) . '" value="' . esc_attr( $value[ $property ] ) . '"' . wp_kses_post( $placeholder ) . ' class="spf-input-number spf--is-unit" />';
				echo ( ! empty( $args['unit'] ) ) ? '<span class="spf--label spf--unit">' . esc_html( $args['unit'] ) . '</span>' : '';
				echo '</div>';

			}

			if ( ! empty( $args['style'] ) ) {
				echo '<div class="spf--input">';
				echo '<select name="' . esc_attr( $this->field_name( '[style]' ) ) . '">';
				foreach ( array( 'inset', 'outset' ) as $style ) {
					$selected = ( $value['style'] === $style ) ? ' selected' : '';
					echo '<option value="' . esc_attr( $style ) . '"' . esc_attr( $selected ) . '>' . esc_html( ucfirst( $style ) ) . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}

			echo '</div>';

			if ( ! empty( $args['color'] ) ) {
				$default_color_attr = ( ! empty( $default_value['color'] ) ) ? ' data-default-color="' . $default_value['color'] . '"' : '';
				echo '<div class="spf-field-color">';
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[color]' ) ) . '" value="' . esc_attr( $value['color'] ) . '" class="spf-color"' . wp_kses_post( $default_color_attr ) . ' />';
				echo '</div>';
			}

			echo '<div class="clear"></div>';

			echo wp_kses_post( $this->field_after() );

		}

		/**
		 * The output function
		 *
		 * @return mixed
		 */
		public function output() {

			$output    = '';
			$unit      = ( ! empty( $this->value['unit'] ) ) ? $this->value['unit'] : 'px';
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];

			// properties.
			$horizontal = ( isset( $this->value['horizontal'] ) && '' !== $this->value['horizontal'] ) ? $this->value['horizontal'] : '';
			$vertical   = ( isset( $this->value['vertical'] ) && '' !== $this->value['vertical'] ) ? $this->value['vertical'] : '';
			$blur       = ( isset( $this->value['blur'] ) && '' !== $this->value['blur'] ) ? $this->value['blur'] : '';
			$spread     = ( isset( $this->value['spread'] ) && '' !== $this->value['spread'] ) ? $this->value['spread'] : '';
			$style      = ( isset( $this->value['style'] ) && '' !== $this->value['style'] && 'outset' !== $this->value['style'] ) ? $this->value['style'] : '';
			$color      = ( isset( $this->value['color'] ) && '' !== $this->value['color'] ) ? $this->value['color'] : '';

				$output  = $element . '{ box-shadow: ';
				$output .= ( '' !== $horizontal ) ? $horizontal . $unit : '0' . $unit;
				$output .= ( '' !== $vertical ) ? $vertical . $unit : '0' . $unit;
				$output .= ( '' !== $blur ) ? $blur . $unit : '0' . $unit;
				$output .= ( '' !== $spread ) ? $spread . $unit : '0' . $unit;
				$output .= ( '' !== $color ) ? $color : '';
				$output .= ( '' !== $style ) ? $style : '';
				$output .= ';' . $important . ' }';

			$this->parent->output_css .= $output;

			return $output;

		}

	}
}
