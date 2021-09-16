<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: Dimension Advanced.
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Field_dimensions_advanced' ) ) {

	/**
	 * The Advanced Dimensions field class.
	 *
	 * @since 3.5
	 */
	class SP_PC_Field_dimensions_advanced extends SP_PC_Fields {

		/**
		 * Advanced Dimensions field constructor.
		 *
		 * @param array  $field The field type.
		 * @param string $value The values of the field.
		 * @param string $unique The unique ID for the field.
		 * @param string $where To where show the output CSS.
		 * @param string $parent The parent args.
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * The render method.
		 *
		 * @return void
		 */
		public function render() {
			$args = wp_parse_args(
				$this->field,
				array(
					'top_icon'           => '<i class="fa fa-long-arrow-up"></i>',
					'right_icon'         => '<i class="fa fa-long-arrow-right"></i>',
					'left_icon'          => '<i class="fa fa-long-arrow-left"></i>',
					'bottom_icon'        => '<i class="fa fa-long-arrow-down"></i>',
					'all_icon'           => '<i class="fa fa-arrows"></i>',
					'top_placeholder'    => esc_html__( 'top', 'smart-post-show' ),
					'right_placeholder'  => esc_html__( 'right', 'smart-post-show' ),
					'bottom_placeholder' => esc_html__( 'bottom', 'smart-post-show' ),
					'left_placeholder'   => esc_html__( 'left', 'smart-post-show' ),
					'all_placeholder'    => esc_html__( 'all', 'smart-post-show' ),
					'top'                => true,
					'left'               => true,
					'bottom'             => true,
					'right'              => true,
					'all'                => false,
					'color'              => true,
					'style'              => true,
					'styles'             => array( 'Soft-crop', 'Hard-crop' ),
					'units'              => array( 'px', '%', 'em' ),
					'min'                => '0',
				)
			);

			$default_values = array(
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
				'color'  => '',
				'style'  => 'solid',
				'all'    => '',
				'min'    => '',
				'unit'   => 'px',
			);

			$border_props = array(
				'solid'  => esc_html__( 'Solid', 'smart-post-show' ),
				'dashed' => esc_html__( 'Dashed', 'smart-post-show' ),
				'dotted' => esc_html__( 'Dotted', 'smart-post-show' ),
				'double' => esc_html__( 'Double', 'smart-post-show' ),
				'inset'  => esc_html__( 'Inset', 'smart-post-show' ),
				'outset' => esc_html__( 'Outset', 'smart-post-show' ),
				'groove' => esc_html__( 'Groove', 'smart-post-show' ),
				'ridge'  => esc_html__( 'ridge', 'smart-post-show' ),
				'none'   => esc_html__( 'None', 'smart-post-show' ),
			);

			$default_values = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_values ) : $default_values;

			$value   = wp_parse_args( $this->value, $default_values );
			$unit    = ( count( $args['units'] ) === 1 && ! empty( $args['unit'] ) ) ? $args['units'][0] : '';
			$is_unit = ( ! empty( $unit ) ) ? ' spf--is-unit' : '';

			echo wp_kses_post( $this->field_before() );

			$min = ( isset( $args['min'] ) ) ? ' min="' . $args['min'] . '"' : '';

			echo '<div class="spf--inputs">';
			if ( ! empty( $args['all'] ) ) {

				$placeholder = ( ! empty( $args['all_placeholder'] ) ) ? ' placeholder="' . $args['all_placeholder'] . '"' : '';

				echo '<div class="spf--input">';
				echo ( ! empty( $args['all_icon'] ) ) ? '<span class="spf--label spf--icon">' . wp_kses_post( $args['all_icon'] ) . '</span>' : '';
				echo '<input type="number" name="' . esc_attr( $this->field_name( '[all]' ) ) . '" value="' . esc_attr( $value['all'] ) . '"' . wp_kses_post( $placeholder . $min ) . ' class="spf-input-number' . esc_attr( $is_unit ) . '" />';
				echo ( $unit ) ? '<span class="spf--label spf--unit">' . esc_html( $args['units'][0] ) . '</span>' : '';
				echo '</div>';

			} else {

				$properties = array();

				foreach ( array( 'top', 'right', 'bottom', 'left' ) as $prop ) {
					if ( ! empty( $args[ $prop ] ) ) {
						$properties[] = $prop;
					}
				}

				$properties = ( array( 'right', 'left' ) === $properties ) ? array_reverse( $properties ) : $properties;

				foreach ( $properties as $property ) {

					$placeholder = ( ! empty( $args[ $property . '_placeholder' ] ) ) ? ' placeholder="' . $args[ $property . '_placeholder' ] . '"' : '';

					echo '<div class="spf--input">';
					echo ( ! empty( $args[ $property . '_icon' ] ) ) ? '<span class="spf--label spf--icon">' . wp_kses_post( $args[ $property . '_icon' ] ) . '</span>' : '';
					echo '<input type="number" name="' . esc_attr( $this->field_name( '[' . $property . ']' ) ) . '" value="' . esc_attr( $value[ $property ] ) . '"' . wp_kses_post( $placeholder . $min ) . ' class="spf-input-number' . esc_attr( $is_unit ) . '" />';
					echo ( $unit ) ? '<span class="spf--label spf--unit">' . esc_html( $args['units'][0] ) . '</span>' : '';
					echo '</div>';

				}
			}

			if ( ! empty( $args['style'] ) ) {
				echo '<div class="spf--input">';
				echo '<select name="' . esc_attr( $this->field_name( '[style]' ) ) . '">';
				foreach ( $args['styles'] as $style_prop ) {
					$selected = ( $value['style'] === $style_prop ) ? ' selected' : '';
					echo '<option value="' . esc_attr( $style_prop ) . '"' . esc_attr( $selected ) . '>' . wp_kses_post( $style_prop ) . '</option>';
				}
				echo '</select>';
				echo '</div>';
			}

			if ( ! empty( $args['color'] ) ) {
				$default_color_attr = ( ! empty( $default_values['color'] ) ) ? ' data-default-color="' . $default_values['color'] . '"' : '';
				echo '<div class="spf--left spf-field-color">';
				echo '<input type="text" name="' . esc_attr( $this->field_name( '[color]' ) ) . '" value="' . esc_attr( $value['color'] ) . '" class="spf-color"' . wp_kses_post( $default_color_attr ) . ' />';
				echo '</div>';
			}

			echo '</div>';

			echo wp_kses_post( $this->field_after() );

		}

		/**
		 * The output.
		 *
		 * @return mixed
		 */
		public function output() {

			$output    = '';
			$unit      = ( ! empty( $this->value['unit'] ) ) ? $this->value['unit'] : 'px';
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];

			// properties.
			$top    = ( isset( $this->value['top'] ) && '' !== $this->value['top'] ) ? $this->value['top'] : '';
			$right  = ( isset( $this->value['right'] ) && '' !== $this->value['right'] ) ? $this->value['right'] : '';
			$bottom = ( isset( $this->value['bottom'] ) && '' !== $this->value['bottom'] ) ? $this->value['bottom'] : '';
			$left   = ( isset( $this->value['left'] ) && '' !== $this->value['left'] ) ? $this->value['left'] : '';
			$style  = ( isset( $this->value['style'] ) && '' !== $this->value['style'] ) ? $this->value['style'] : '';
			$color  = ( isset( $this->value['color'] ) && '' !== $this->value['color'] ) ? $this->value['color'] : '';
			$all    = ( isset( $this->value['all'] ) && '' !== $this->value['all'] ) ? $this->value['all'] : '';

			if ( ! empty( $this->field['all'] ) && ( '' !== $all || '' !== $color ) ) {

				$output  = $element . '{';
				$output .= ( '' !== $all ) ? 'border-width:' . $all . $unit . $important . ';' : '';
				$output .= ( '' !== $color ) ? 'border-color:' . $color . $important . ';' : '';
				$output .= ( '' !== $style ) ? 'border-style:' . $style . $important . ';' : '';
				$output .= '}';

			} elseif ( '' !== $top || '' !== $right || '' !== $bottom || '' !== $left || '' !== $color ) {

				$output  = $element . '{';
				$output .= ( '' !== $top ) ? 'border-top-width:' . $top . $unit . $important . ';' : '';
				$output .= ( '' !== $right ) ? 'border-right-width:' . $right . $unit . $important . ';' : '';
				$output .= ( '' !== $bottom ) ? 'border-bottom-width:' . $bottom . $unit . $important . ';' : '';
				$output .= ( '' !== $left ) ? 'border-left-width:' . $left . $unit . $important . ';' : '';
				$output .= ( '' !== $color ) ? 'border-color:' . $color . $important . ';' : '';
				$output .= ( '' !== $style ) ? 'border-style:' . $style . $important . ';' : '';
				$output .= '}';

			}

			$this->parent->output_css .= $output;

			return $output;

		}

	}
}
