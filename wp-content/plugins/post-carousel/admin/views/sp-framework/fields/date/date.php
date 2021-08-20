<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.
/**
 *
 * Field: date
 *
 * @since 2.0
 * @version 2.0
 */
if ( ! class_exists( 'SP_PC_Field_date' ) ) {
	class SP_PC_Field_date extends SP_PC_Fields {

		/**
		 * The constructor.
		 *
		 * @param attributes $field The fields array.
		 * @param string $value
		 * @param string $unique
		 * @param string $where
		 * @param string $parent
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

			$default_settings = array(
				'dateFormat' => 'mm/dd/yy',
			);

			$view_options = ( ! empty( $this->field['settings'] ) ) ? $this->field['settings'] : array();
			$view_options = wp_parse_args( $view_options, $default_settings );

			echo wp_kses_post( $this->field_before() );

			if ( ! empty( $this->field['from_to'] ) ) {

				$args = wp_parse_args(
					$this->field,
					array(
						'text_from' => 'From',
						'text_to'   => 'To',
					)
				);

				$value = wp_parse_args(
					$this->value,
					array(
						'from' => '',
						'to'   => '',
					)
				);

				echo '<label class="spf--from">' . esc_html( $args['text_from'] ) . ' <input type="text" name="' . esc_attr( $this->field_name( '[from]' ) ) . '" value="' . esc_attr( $value['from'] ) . '"' . wp_kses_post( $this->field_attributes() ) . '/></label>';
				echo '<label class="spf--to">' . esc_html( $args['text_to'] ) . ' <input type="text" name="' . esc_attr( $this->field_name( '[to]' ) ) . '" value="' . esc_attr( $value['to'] ) . '"' . wp_kses_post( $this->field_attributes() ) . '/></label>';

			} else {

				echo '<input type="text" name="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '"' . wp_kses_post( $this->field_attributes() ) . '/>';

			}

			echo '<div class="spf-date-settings" data-settings="' . esc_attr( json_encode( $view_options ) ) . '"></div>';

			echo wp_kses_post( $this->field_after() );

		}

		/**
		 * Enqueue Date Picker.
		 *
		 * @return void
		 */
		public function enqueue() {

			if ( ! wp_script_is( 'jquery-ui-datepicker' ) ) {
				wp_enqueue_script( 'jquery-ui-datepicker' );
			}

		}

	}
}
