<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.
/**
 *
 * Field: number
 *
 * @since 1.0.0
 * @version 1.0.0
 */
if ( ! class_exists( 'SP_PC_Field_number' ) ) {
	class SP_PC_Field_number extends SP_PC_Fields {
		/**
		 * Constructor
		 *
		 * @param [type] $field
		 * @param string $value
		 * @param string $unique
		 * @param string $where
		 * @param string $parent
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}
		/**
		 * Render method.
		 *
		 * @return void
		 */
		public function render() {
			$args = wp_parse_args(
				$this->field,
				array(
					'unit' => '',
				)
			);
			echo wp_kses_post( $this->field_before() );
			echo '<div class="spf--wrap">';
			echo '<input type="number" name="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '"' . wp_kses_post( $this->field_attributes( array( 'class' => 'spf-input-number' ) ) ) . '/>';
			echo ( ! empty( $args['unit'] ) ) ? '<span class="spf--unit">' . esc_html( $args['unit'] ) . '</span>' : '';
			echo '</div>';
			echo '<div class="clear"></div>';
			echo wp_kses_post( $this->field_after() );
		}
		/**
		 * The output method.
		 *
		 * @return mixed
		 */
		public function output() {
			$output    = '';
			$elements  = ( is_array( $this->field['output'] ) ) ? $this->field['output'] : array_filter( (array) $this->field['output'] );
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$mode      = ( ! empty( $this->field['output_mode'] ) ) ? $this->field['output_mode'] : 'width';
			$unit      = ( ! empty( $this->field['unit'] ) ) ? $this->field['unit'] : 'px';
			if ( ! empty( $elements ) && isset( $this->value ) && '' !== $this->value ) {
				foreach ( $elements as $key_property => $element ) {
					if ( is_numeric( $key_property ) ) {
						if ( $mode ) {
							$output = implode( ',', $elements ) . '{' . $mode . ':' . $this->value . $unit . $important . ';}';
						}
						break;
					} else {
						$output .= $element . '{' . $key_property . ':' . $this->value . $unit . $important . '}';
					}
				}
			}
			$this->parent->output_css .= $output;
			return $output;
		}
	}
}
